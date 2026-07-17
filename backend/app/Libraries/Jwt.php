<?php

namespace App\Libraries;

use RuntimeException;

/**
 * Implementación mínima de JSON Web Tokens (HS256), sin dependencias externas.
 * Se optó por esta implementación en vez de firebase/php-jwt porque el entorno
 * de desarrollo actual no tiene composer/PHP CLI disponibles para instalar el paquete;
 * se puede reemplazar por la librería oficial ejecutando `composer require firebase/php-jwt`.
 */
class Jwt
{
    public static function encode(array $payload, string $secret): string
    {
        $header = ['typ' => 'JWT', 'alg' => 'HS256'];

        $segments = [
            self::base64UrlEncode(json_encode($header)),
            self::base64UrlEncode(json_encode($payload)),
        ];

        $signature = hash_hmac('sha256', implode('.', $segments), $secret, true);
        $segments[] = self::base64UrlEncode($signature);

        return implode('.', $segments);
    }

    /**
     * @return array Payload decodificado.
     * @throws RuntimeException Si el token es inválido, la firma no coincide, o expiró.
     */
    public static function decode(string $token, string $secret): array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new RuntimeException('Token con formato inválido.');
        }

        [$headerB64, $payloadB64, $signatureB64] = $parts;

        $expectedSignature = hash_hmac('sha256', "{$headerB64}.{$payloadB64}", $secret, true);
        if (!hash_equals($expectedSignature, self::base64UrlDecode($signatureB64))) {
            throw new RuntimeException('Firma del token inválida.');
        }

        $payload = json_decode(self::base64UrlDecode($payloadB64), true);
        if (!is_array($payload)) {
            throw new RuntimeException('Payload del token inválido.');
        }

        if (isset($payload['exp']) && time() >= $payload['exp']) {
            throw new RuntimeException('Token expirado.');
        }

        return $payload;
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string
    {
        $padded = str_pad($data, strlen($data) % 4 === 0 ? strlen($data) : strlen($data) + 4 - strlen($data) % 4, '=');
        return base64_decode(strtr($padded, '-_', '+/'));
    }
}
