<?php

use App\Models\UsuarioModel;
use App\Services\UsuarioService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Tests unitarios puros de UsuarioService::autenticar(): el UsuarioModel
 * se mockea por completo, no hay conexión a base de datos ni al framework
 * más allá de lo mínimo que CIUnitTestCase necesita para arrancar.
 *
 * @internal
 */
final class UsuarioServiceUnitTest extends CIUnitTestCase
{
    private function usuarioMock(?array $usuario): UsuarioService
    {
        $model = $this->createMock(UsuarioModel::class);
        $model->method('findByEmailOUsuario')->willReturn($usuario);

        return new UsuarioService($model);
    }

    public function testAutenticarUsuarioInexistenteDevuelveError(): void
    {
        $service = $this->usuarioMock(null);

        $resultado = $service->autenticar('nadie@test.com', 'cualquiera');

        $this->assertSame('error', $resultado['status']);
        $this->assertSame('Email o nombre de usuario incorrectos', $resultado['message']);
    }

    public function testAutenticarUsuarioDadoDeBajaDevuelveError(): void
    {
        $service = $this->usuarioMock([
            'id_usuario' => 1,
            'email'      => 'baja@test.com',
            'pass'       => password_hash('clave123', PASSWORD_DEFAULT),
            'deleted_at' => '2026-01-01 00:00:00',
        ]);

        $resultado = $service->autenticar('baja@test.com', 'clave123');

        $this->assertSame('error', $resultado['status']);
        $this->assertSame('Usuario dado de baja', $resultado['message']);
    }

    public function testAutenticarConPasswordIncorrectaDevuelveError(): void
    {
        $service = $this->usuarioMock([
            'id_usuario' => 1,
            'email'      => 'activo@test.com',
            'pass'       => password_hash('claveCorrecta', PASSWORD_DEFAULT),
            'deleted_at' => null,
        ]);

        $resultado = $service->autenticar('activo@test.com', 'claveIncorrecta');

        $this->assertSame('error', $resultado['status']);
        $this->assertSame('Contraseña Incorrecta', $resultado['message']);
    }

    public function testAutenticarConCredencialesValidasDevuelveSuccess(): void
    {
        $service = $this->usuarioMock([
            'id_usuario' => 7,
            'nombre'     => 'Ana',
            'apellido'   => 'Test',
            'email'      => 'ana@test.com',
            'usuario'    => 'ana',
            'perfil_id'  => 2,
            'imagen'     => '',
            'pass'       => password_hash('claveCorrecta', PASSWORD_DEFAULT),
            'deleted_at' => null,
        ]);

        $resultado = $service->autenticar('ana@test.com', 'claveCorrecta');

        $this->assertSame('success', $resultado['status']);
        $this->assertSame(7, $resultado['data']['id_usuario']);
        $this->assertTrue($resultado['data']['logged_in']);
    }
}
