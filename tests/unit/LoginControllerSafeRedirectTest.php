<?php

use App\Controllers\LoginController;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Prueba unitaria de LoginController::safeRedirect(): allow-list de
 * redirect_to que rechaza URLs externas, protocol-relative, esquemas
 * peligrosos e inyección de encabezados (CR/LF), cayendo a '/'.
 *
 * @internal
 */
final class LoginControllerSafeRedirectTest extends CIUnitTestCase
{
    private function invoke(?string $target): string
    {
        $controller = new LoginController();
        $method = new ReflectionMethod(LoginController::class, 'safeRedirect');
        $method->setAccessible(true);

        return $method->invoke($controller, $target);
    }

    /**
     * @return array<string, array{0: ?string, 1: string}>
     */
    public static function targetsProvider(): array
    {
        return [
            'ruta relativa valida'         => ['/detalle_producto/5', '/detalle_producto/5'],
            'null cae a raiz'              => [null, '/'],
            'vacio cae a raiz'             => ['', '/'],
            'protocol-relative rechazado'  => ['//evil.com', '/'],
            'backslash rechazado'          => ['/\\evil.com', '/'],
            'esquema http externo'         => ['https://evil.example', '/'],
            'esquema javascript'           => ['javascript:alert(1)', '/'],
            'inyeccion CRLF'               => ["/x\r\nSet-Cookie: a=b", '/'],
            'excede longitud maxima'       => ['/' . str_repeat('a', 600), '/'],
        ];
    }

    /**
     * @dataProvider targetsProvider
     */
    public function testSafeRedirectAplicaAllowList(?string $target, string $esperado): void
    {
        $this->assertSame($esperado, $this->invoke($target));
    }

    public function testSafeRedirectPermiteUrlAbsolutaMismoHost(): void
    {
        $mismoHost = rtrim(base_url(), '/') . '/detalle_producto/5';

        $this->assertSame($mismoHost, $this->invoke($mismoHost));
    }
}
