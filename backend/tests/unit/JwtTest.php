<?php

use App\Libraries\Jwt;
use PHPUnit\Framework\TestCase;

/**
 * Tests de la implementación propia de JWT (App\Libraries\Jwt). Es pura
 * (hash_hmac/json, sin tocar la base de datos ni servicios de CI4), así que
 * usa PHPUnit\Framework\TestCase en vez de CIUnitTestCase.
 *
 * NOTA: escrito y revisado a mano, pero nunca ejecutado — este entorno de
 * desarrollo no tiene PHP/Composer disponibles para correr `vendor/bin/phpunit`.
 * Correr `composer install && vendor/bin/phpunit tests/unit/JwtTest.php` para
 * verificarlo antes de confiar en que pasa.
 *
 * @internal
 */
final class JwtTest extends TestCase
{
    private const SECRET = 'clave-de-prueba-no-usar-en-produccion';

    public function testEncodeProduceUnTokenDeTresSegmentos(): void
    {
        $token = Jwt::encode(['id_usuario' => 1], self::SECRET);

        $this->assertSame(3, substr_count($token, '.') + 1);
        $this->assertCount(3, explode('.', $token));
    }

    public function testDecodeRecuperaElPayloadOriginal(): void
    {
        $payload = ['id_usuario' => 42, 'perfil_id' => 1, 'exp' => time() + 3600];
        $token = Jwt::encode($payload, self::SECRET);

        $decoded = Jwt::decode($token, self::SECRET);

        $this->assertSame(42, $decoded['id_usuario']);
        $this->assertSame(1, $decoded['perfil_id']);
    }

    public function testDecodeRechazaUnTokenFirmadoConOtroSecret(): void
    {
        $token = Jwt::encode(['id_usuario' => 1], self::SECRET);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Firma del token inválida.');

        Jwt::decode($token, 'otro-secret-distinto');
    }

    public function testDecodeRechazaUnTokenConFormatoInvalido(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Token con formato inválido.');

        Jwt::decode('esto-no-es-un-jwt', self::SECRET);
    }

    public function testDecodeRechazaUnTokenExpirado(): void
    {
        $token = Jwt::encode(['id_usuario' => 1, 'exp' => time() - 10], self::SECRET);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Token expirado.');

        Jwt::decode($token, self::SECRET);
    }

    public function testDecodeAceptaUnTokenSinExpQueNuncaVence(): void
    {
        $token = Jwt::encode(['id_usuario' => 1], self::SECRET);

        $decoded = Jwt::decode($token, self::SECRET);

        $this->assertSame(1, $decoded['id_usuario']);
    }

    public function testDecodeDetectaUnaFirmaAlteradaAMano(): void
    {
        $token = Jwt::encode(['id_usuario' => 1], self::SECRET);
        [$header, $payload] = explode('.', $token);

        // Simula un atacante reemplazando el payload sin conocer el secret.
        $payloadFalso = rtrim(strtr(base64_encode(json_encode(['id_usuario' => 999])), '+/', '-_'), '=');
        $tokenAlterado = "{$header}.{$payloadFalso}.firmainventada";

        $this->expectException(RuntimeException::class);

        Jwt::decode($tokenAlterado, self::SECRET);
    }
}
