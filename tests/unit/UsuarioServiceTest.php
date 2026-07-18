<?php

use App\Services\UsuarioService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * Tests de UsuarioService contra la base de datos real de test
 * (cva_muebles_test). Cada test corre en una transacción que se
 * revierte automáticamente al finalizar (DatabaseTestTrait).
 *
 * @internal
 */
final class UsuarioServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $namespace = false;
    protected $migrate = false;
    protected $refresh = false;
    private UsuarioService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UsuarioService();
    }

    private function crearUsuario(array $overrides = []): int
    {
        $data = array_merge([
            'nombre'    => 'Test',
            'apellido'  => 'Usuario',
            'usuario'   => 'test_user_' . uniqid(),
            'email'     => uniqid() . '@test.com',
            'pass'      => password_hash('claveSegura123', PASSWORD_DEFAULT),
            'imagen'    => '',
            'perfil_id' => 2,
        ], $overrides);

        return (int) $this->db->table('usuarios')->insert($data) ? $this->db->insertID() : 0;
    }

    public function testAutenticarConCredencialesValidasDevuelveSuccess(): void
    {
        $email = 'valido_' . uniqid() . '@test.com';
        $this->crearUsuario(['email' => $email, 'pass' => password_hash('claveSegura123', PASSWORD_DEFAULT)]);

        $resultado = $this->service->autenticar($email, 'claveSegura123');

        $this->assertSame('success', $resultado['status']);
        $this->assertSame($email, $resultado['data']['email']);
    }

    public function testAutenticarConPasswordIncorrectaFalla(): void
    {
        $email = 'malapass_' . uniqid() . '@test.com';
        $this->crearUsuario(['email' => $email]);

        $resultado = $this->service->autenticar($email, 'password-equivocada');

        $this->assertSame('error', $resultado['status']);
        $this->assertSame('Contraseña Incorrecta', $resultado['message']);
    }

    public function testAutenticarUsuarioInexistenteFalla(): void
    {
        $resultado = $this->service->autenticar('no-existe-' . uniqid() . '@test.com', 'cualquiera');

        $this->assertSame('error', $resultado['status']);
    }

    public function testDarDeBajaImpideElLogin(): void
    {
        $email = 'baja_' . uniqid() . '@test.com';
        $id = $this->crearUsuario(['email' => $email]);

        $this->service->darDeBaja($id);
        $resultado = $this->service->autenticar($email, 'claveSegura123');

        $this->assertSame('error', $resultado['status']);
        $this->assertSame('Usuario dado de baja', $resultado['message']);
    }

    public function testReactivarPermiteElLoginDeNuevo(): void
    {
        $email = 'reactivado_' . uniqid() . '@test.com';
        $id = $this->crearUsuario(['email' => $email]);

        $this->service->darDeBaja($id);
        $this->service->reactivar($id);
        $resultado = $this->service->autenticar($email, 'claveSegura123');

        $this->assertSame('success', $resultado['status']);
    }
}
