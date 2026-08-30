<?php

use App\Models\UsuarioModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Throttle\ThrottlerInterface;
use Config\Services;

/**
 * Prueba de feature contra la base de datos real de desarrollo: cubre
 * LoginController::create/auth/logout, incluyendo el guardrail de
 * throttling y la protección (parcial, ver nota) contra enumeración
 * de usuarios.
 *
 * @internal
 */
final class LoginControllerFeatureTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private const EMAIL_TEST = 'login.test.xyz@cva-muebles-test.local';

    private const USUARIO_TEST = 'login_test_xyz';

    private const PASSWORD_TEST = 'ClaveDeTest123!';

    private const SESION_ADMIN = ['logged_in' => true, 'perfil_id' => 1, 'id_usuario' => 1];

    private ?int $idUsuarioTest = null;

    protected function setUp(): void
    {
        parent::setUp();

        $model = new UsuarioModel();

        // Purga cualquier fila residual de una corrida anterior fallida:
        // UsuarioModel usa soft-delete, así que un delete() normal en
        // tearDown no libera el email/usuario para la validación is_unique.
        $model->where('email', self::EMAIL_TEST)->orWhere('usuario', self::USUARIO_TEST)->purgeDeleted();
        $model->withDeleted()->where('email', self::EMAIL_TEST)
            ->orWhere('usuario', self::USUARIO_TEST)->delete(null, true);

        $this->idUsuarioTest = $model->insert([
            'nombre'    => 'Login',
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
            ->orWhere('usuario', self::USUARIO_TEST)->delete(null, true);
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
     * LoginController::auth() invoca check() con solo 3 argumentos
     * (confiando en el valor por defecto de $cost de la implementación real).
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

    public function testCreateConSesionAutenticadaRedirigeAInicio(): void
    {
        $result = $this->withSession(self::SESION_ADMIN)->get('/login');

        $result->assertRedirectTo('/');
    }

    public function testAuthConCredencialesValidasAutenticaYRedirige(): void
    {
        $this->mockThrottler(true);

        $result = $this->withSession([])
            ->post('/enviar-login', $this->conCsrf([
                'email' => self::EMAIL_TEST,
                'pass'  => self::PASSWORD_TEST,
            ]));

        $result->assertRedirectTo('/');
        $result->assertSessionHas('success');
        $result->assertSessionHas('logged_in', true);
        $result->assertSessionHas('id_usuario', (string) $this->idUsuarioTest);
        $result->assertSessionHas('perfil_id', '2');
    }

    public function testAuthConPasswordIncorrectaNoAutentica(): void
    {
        $this->mockThrottler(true);

        $result = $this->withSession([])
            ->post('/enviar-login', $this->conCsrf([
                'email' => self::EMAIL_TEST,
                'pass'  => 'password-incorrecta',
            ]));

        $result->assertRedirect();
        $result->assertSessionHas('error');
        $result->assertSessionMissing('logged_in');
    }

    /**
     * NOTA (desviación documentada respecto al spec): el controlador actual
     * NO protege realmente contra enumeración de usuarios — UsuarioService::autenticar
     * devuelve mensajes distintos para "no existe" ('Email o nombre de usuario
     * incorrectos') y "contraseña incorrecta" ('Contraseña Incorrecta'). Esta
     * prueba documenta el comportamiento actual (falla de forma genérica, sin
     * autenticar), no que los mensajes sean indistinguibles.
     */
    public function testAuthConEmailInexistenteNoAutentica(): void
    {
        $this->mockThrottler(true);

        $result = $this->withSession([])
            ->post('/enviar-login', $this->conCsrf([
                'email' => 'no-existe-xyz@cva-muebles-test.local',
                'pass'  => 'cualquiera',
            ]));

        $result->assertRedirect();
        $result->assertSessionHas('error');
        $result->assertSessionMissing('logged_in');
    }

    public function testAuthConThrottlerDenegadoNoAutentica(): void
    {
        $this->mockThrottler(false);

        $result = $this->withSession([])
            ->post('/enviar-login', $this->conCsrf([
                'email' => self::EMAIL_TEST,
                'pass'  => self::PASSWORD_TEST,
            ]));

        $result->assertRedirect();
        $result->assertSessionHas('error', 'Demasiados intentos. Por favor, espera un minuto.');
        $result->assertSessionMissing('logged_in');
    }

    /**
     * NOTA: CodeIgniter\Session\Session::destroy() es un no-op cuando
     * ENVIRONMENT === 'testing' (ver system/Session/Session.php), por lo que
     * en el harness de feature tests no es posible verificar que $_SESSION
     * quede vacío tras el logout. Esta prueba solo puede verificar el
     * redirect, que es el efecto observable en este entorno.
     */
    public function testLogoutRedirigeAInicio(): void
    {
        $result = $this->withSession(self::SESION_ADMIN)->get('/logout');

        $result->assertRedirectTo('/');
    }
}
