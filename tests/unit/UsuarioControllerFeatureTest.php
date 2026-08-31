<?php

use App\Models\UsuarioModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Throttle\ThrottlerInterface;
use Config\Services;

/**
 * Prueba de feature contra la base de datos real de desarrollo: cubre
 * UsuarioController::index_registrar/formValidation/guardarCambios/
 * cambiarPassword y las rutas de gestión de usuarios protegidas por el
 * filtro adminAuth (delete/activar/editar/eliminar-permanente).
 *
 * @internal
 */
final class UsuarioControllerFeatureTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private const EMAIL_TEST = 'usuario.test.xyz@cva-muebles-test.local';

    private const USUARIO_TEST = 'usuario_test_xyz';

    private const EMAIL_ADMIN_TEST = 'usuario.admin.test.xyz@cva-muebles-test.local';

    private const USUARIO_ADMIN_TEST = 'usr_admin_test_xyz';

    private const EMAIL_TARGET_TEST = 'usuario.target.test.xyz@cva-muebles-test.local';

    private const USUARIO_TARGET_TEST = 'usr_target_test_xyz';

    private const PASSWORD_TEST = 'ClaveDeTest123!';

    private const SESION_ADMIN = ['logged_in' => true, 'perfil_id' => 1, 'id_usuario' => 1];

    private const SESION_CLIENTE = ['logged_in' => true, 'perfil_id' => 2, 'id_usuario' => 999];

    /** @var array<int, string> Emails de filas creadas durante un test que deben purgarse en tearDown() */
    private array $emailsACrear = [];

    private ?int $idUsuarioTest = null;

    private ?int $idUsuarioTarget = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->emailsACrear = [];

        $model = new UsuarioModel();

        // Purga cualquier fila residual de una corrida anterior fallida.
        // UsuarioModel usa soft-delete: un delete() normal en tearDown no
        // libera el email/usuario para la validación is_unique.
        $model->withDeleted()->where('email', self::EMAIL_TEST)
            ->orWhere('usuario', self::USUARIO_TEST)
            ->orWhere('email', self::EMAIL_ADMIN_TEST)
            ->orWhere('usuario', self::USUARIO_ADMIN_TEST)
            ->orWhere('email', self::EMAIL_TARGET_TEST)
            ->orWhere('usuario', self::USUARIO_TARGET_TEST)
            ->delete(null, true);

        $this->idUsuarioTest = $model->insert([
            'nombre'    => 'Usuario',
            'apellido'  => 'Test',
            'usuario'   => self::USUARIO_TEST,
            'email'     => self::EMAIL_TEST,
            'pass'      => password_hash(self::PASSWORD_TEST, PASSWORD_DEFAULT),
            'perfil_id' => 2,
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Services::resetSingle('throttler');

        // Hard-delete (no soft-delete) para no dejar filas "dadas de baja"
        // que bloqueen la validación is_unique de una corrida futura.
        $model = new UsuarioModel();
        $model->withDeleted()->where('email', self::EMAIL_TEST)
            ->orWhere('usuario', self::USUARIO_TEST)
            ->orWhere('email', self::EMAIL_ADMIN_TEST)
            ->orWhere('usuario', self::USUARIO_ADMIN_TEST)
            ->orWhere('email', self::EMAIL_TARGET_TEST)
            ->orWhere('usuario', self::USUARIO_TARGET_TEST)
            ->delete(null, true);

        foreach ($this->emailsACrear as $email) {
            $model->withDeleted()->where('email', $email)->delete(null, true);
        }
    }

    private function conCsrf(array $data): array
    {
        return $data + [csrf_token() => csrf_hash()];
    }

    /**
     * Inyecta un mock del throttler que siempre permite o siempre deniega,
     * evitando que las pruebas dependan del estado real del rate limiter.
     *
     * NOTA: se usa una clase anónima en lugar de $this->createMock() porque
     * el mock generado por PHPUnit exige exactamente los 4 parámetros
     * declarados en ThrottlerInterface::check(), mientras que
     * UsuarioController::formValidation() invoca check() con solo 3
     * argumentos (confiando en el valor por defecto de $cost de la
     * implementación real). Patrón copiado verbatim de
     * LoginControllerFeatureTest::mockThrottler().
     */
    private function mockThrottler(bool $allow): void
    {
        $throttler = new class ($allow) implements ThrottlerInterface {
            public function __construct(private bool $allow)
            {
            }

            public function check(string $key, int $capacity, int $seconds, int $cost = 1)
            {
                return $this->allow;
            }

            public function getTokenTime(): int
            {
                return 0;
            }
        };

        Services::injectMock('throttler', $throttler);
    }

    public function testIndexRegistrarAnonimoMuestraFormulario(): void
    {
        $result = $this->withSession([])->get('/registro');

        $result->assertOK();
    }

    public function testIndexRegistrarNoAdminLogueadoRedirige(): void
    {
        $result = $this->withSession(self::SESION_CLIENTE)->get('/registro');

        $result->assertRedirect();
    }

    public function testEnviarFormAnonimoRegistraYRedirigeALogin(): void
    {
        $this->mockThrottler(true);

        $emailNuevo = 'registro.anonimo.xyz@cva-muebles-test.local';
        $this->emailsACrear[] = $emailNuevo;

        $result = $this->withSession([])
            ->post('/enviar-form', $this->conCsrf([
                'name'    => 'Anonimo',
                'surname' => 'Registro',
                'user'    => 'registro_anonimo_xyz',
                'email'   => $emailNuevo,
                'pass'    => self::PASSWORD_TEST,
            ]));

        $result->assertRedirectTo('/login');
        $result->assertSessionHas('success');

        $model = new UsuarioModel();
        $creado = $model->where('email', $emailNuevo)->first();
        $this->assertNotNull($creado);
    }

    public function testEnviarFormAnonimoConRedirectToLoCarraAlLogin(): void
    {
        $this->mockThrottler(true);

        $emailNuevo = 'registro.redirect.xyz@cva-muebles-test.local';
        $this->emailsACrear[] = $emailNuevo;

        $result = $this->withSession([])
            ->post('/enviar-form', $this->conCsrf([
                'name'        => 'Redirect',
                'surname'     => 'Registro',
                'user'        => 'reg_redirect_xyz',
                'email'       => $emailNuevo,
                'pass'        => self::PASSWORD_TEST,
                'redirect_to' => '/mis_favoritos',
            ]));

        $result->assertRedirectTo('/login');
        $result->assertSessionHas('success');
        $result->assertSessionHas('redirect_to', '/mis_favoritos');
    }

    public function testEnviarFormEmailDuplicadoReabreModalRegistro(): void
    {
        $this->mockThrottler(true);

        $result = $this->withSession([])
            ->post('/enviar-form', $this->conCsrf([
                'name'    => 'Duplicado',
                'surname' => 'Test',
                'user'    => 'usuario_duplicado_reopen_xyz',
                'email'   => self::EMAIL_TEST,
                'pass'    => self::PASSWORD_TEST,
            ]));

        $result->assertRedirect();
        $result->assertSessionHas('fail');
        $result->assertSessionHas('reopen_modal', 'registro');
    }

    public function testEnviarFormAdminRegistraYRedirigeACrudUsuarios(): void
    {
        $this->mockThrottler(true);

        $emailNuevo = 'registro.admin.xyz@cva-muebles-test.local';
        $this->emailsACrear[] = $emailNuevo;

        $result = $this->withSession(self::SESION_ADMIN)
            ->post('/enviar-form', $this->conCsrf([
                'name'    => 'AltaAdmin',
                'surname' => 'Registro',
                'user'    => 'registro_admin_xyz',
                'email'   => $emailNuevo,
                'pass'    => self::PASSWORD_TEST,
            ]));

        $result->assertRedirectTo('/crud-usuarios');
        $result->assertSessionHas('success');

        $model = new UsuarioModel();
        $creado = $model->where('email', $emailNuevo)->first();
        $this->assertNotNull($creado);
    }

    public function testEnviarFormEmailDuplicadoNoRegistra(): void
    {
        $this->mockThrottler(true);

        $model = new UsuarioModel();
        $antes = $model->where('email', self::EMAIL_TEST)->countAllResults();

        $result = $this->withSession([])
            ->post('/enviar-form', $this->conCsrf([
                'name'    => 'Duplicado',
                'surname' => 'Test',
                'user'    => 'usuario_duplicado_xyz',
                'email'   => self::EMAIL_TEST,
                'pass'    => self::PASSWORD_TEST,
            ]));

        $result->assertRedirect();
        $result->assertSessionHas('fail');

        $despues = $model->where('email', self::EMAIL_TEST)->countAllResults();
        $this->assertSame($antes, $despues);
    }

    public function testEnviarFormThrottlerDenegadoNoRegistra(): void
    {
        $this->mockThrottler(false);

        $emailNuevo = 'registro.denegado.xyz@cva-muebles-test.local';

        $result = $this->withSession([])
            ->post('/enviar-form', $this->conCsrf([
                'name'    => 'Denegado',
                'surname' => 'Test',
                'user'    => 'usuario_denegado_xyz',
                'email'   => $emailNuevo,
                'pass'    => self::PASSWORD_TEST,
            ]));

        $result->assertRedirect();
        $result->assertSessionHas('fail');

        $model = new UsuarioModel();
        $creado = $model->where('email', $emailNuevo)->first();
        $this->assertNull($creado);
    }

    public function testGuardarCambiosImagenInvalidaRechazaYNoModificaImagen(): void
    {
        $model = new UsuarioModel();
        $model->update($this->idUsuarioTest, ['imagen' => 'original.png']);

        // Archivo de texto plano disfrazado de imagen: falla la validación is_image.
        $rutaTemp = tempnam(sys_get_temp_dir(), 'fake_img');
        file_put_contents($rutaTemp, 'esto no es una imagen');

        $result = $this->withSession(['id_usuario' => $this->idUsuarioTest] + self::SESION_ADMIN)
            ->withBodyFormat('multipart')
            ->post('/guardarCambios', $this->conCsrf([
                'username' => self::USUARIO_TEST,
                'name'     => 'Usuario',
                'surname'  => 'Test',
                'email'    => self::EMAIL_TEST,
            ]));

        @unlink($rutaTemp);

        $result->assertRedirect();
        $result->assertSessionHas('fail');

        $actual = $model->find($this->idUsuarioTest);
        $this->assertSame('original.png', $actual['imagen']);
    }

    public function testCambiarPasswordConPasswordActualIncorrectaNoModificaHash(): void
    {
        $model = new UsuarioModel();
        $hashOriginal = $model->find($this->idUsuarioTest)['pass'];

        $result = $this->withSession(['id_usuario' => $this->idUsuarioTest] + self::SESION_CLIENTE)
            ->post('/cambiarPassword', $this->conCsrf([
                'current_password' => 'password-incorrecta',
                'new_password'     => 'NuevaClave456!',
                'confirm_password' => 'NuevaClave456!',
            ]));

        $result->assertRedirect();
        $result->assertSessionHas('fail');

        $hashDespues = $model->find($this->idUsuarioTest)['pass'];
        $this->assertSame($hashOriginal, $hashDespues);
    }

    public function testCambiarPasswordCorrectaModificaHash(): void
    {
        $model = new UsuarioModel();
        $hashOriginal = $model->find($this->idUsuarioTest)['pass'];

        $result = $this->withSession(['id_usuario' => $this->idUsuarioTest] + self::SESION_CLIENTE)
            ->post('/cambiarPassword', $this->conCsrf([
                'current_password' => self::PASSWORD_TEST,
                'new_password'     => 'NuevaClave456!',
                'confirm_password' => 'NuevaClave456!',
            ]));

        $result->assertRedirectTo('/perfil');
        $result->assertSessionHas('success');

        $hashDespues = $model->find($this->idUsuarioTest)['pass'];
        $this->assertNotSame($hashOriginal, $hashDespues);
        $this->assertTrue(password_verify('NuevaClave456!', $hashDespues));
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function rutasAdminProvider(): array
    {
        return [
            'delete-usuario'                   => ['/delete-usuario'],
            'activar-usuario'                  => ['/activar-usuario'],
            'editar-usuario'                   => ['/editar-usuario'],
            'eliminar-usuario-permanente'      => ['/eliminar-usuario-permanente'],
        ];
    }

    /**
     * @dataProvider rutasAdminProvider
     */
    public function testRutaAdminSinSesionRedirigeALogin(string $ruta): void
    {
        $result = $this->withSession([])
            ->post($ruta . '/' . $this->idUsuarioTest, $this->conCsrf([]));

        $result->assertRedirectTo('/login');

        $model = new UsuarioModel();
        $actual = $model->withDeleted()->find($this->idUsuarioTest);
        $this->assertNull($actual['deleted_at']);
        $this->assertSame(2, (int) $actual['perfil_id']);
    }

    /**
     * @dataProvider rutasAdminProvider
     */
    public function testRutaAdminNoAdminRedirigeAInicio(string $ruta): void
    {
        $result = $this->withSession(self::SESION_CLIENTE)
            ->post($ruta . '/' . $this->idUsuarioTest, $this->conCsrf([]));

        $result->assertRedirectTo('/');
        $result->assertSessionHas('error');

        $model = new UsuarioModel();
        $actual = $model->withDeleted()->find($this->idUsuarioTest);
        $this->assertNull($actual['deleted_at']);
        $this->assertSame(2, (int) $actual['perfil_id']);
    }

    public function testAdminActivarUsuarioReactivaUsuarioDadoDeBaja(): void
    {
        $model = new UsuarioModel();
        $this->idUsuarioTarget = $model->insert([
            'nombre'    => 'Target',
            'apellido'  => 'Test',
            'usuario'   => self::USUARIO_TARGET_TEST,
            'email'     => self::EMAIL_TARGET_TEST,
            'pass'      => password_hash(self::PASSWORD_TEST, PASSWORD_DEFAULT),
            'perfil_id' => 2,
        ]);
        if ($this->idUsuarioTarget === false || empty($this->idUsuarioTarget)) {
            $this->fail('No se pudo insertar el usuario target: ' . implode(', ', $model->errors() ?? []) . ' | valor devuelto: ' . var_export($this->idUsuarioTarget, true));
        }
        $model->delete((int) $this->idUsuarioTarget);

        $result = $this->withSession(self::SESION_ADMIN)
            ->post('/activar-usuario/' . $this->idUsuarioTarget, $this->conCsrf([]));

        $result->assertRedirect();
        $result->assertSessionHas('success');

        $actual = $model->withDeleted()->find($this->idUsuarioTarget);
        $this->assertNull($actual['deleted_at']);
    }
}
