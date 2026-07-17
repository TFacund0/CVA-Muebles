<?php

namespace App\Controllers\Api;

use App\Libraries\ApiAuthContext;
use App\Libraries\Jwt;
use App\Libraries\JwtIssuer;
use App\Services\UsuarioService;
use Config\Jwt as JwtConfig;
use RuntimeException;

class AuthController extends BaseApiController
{
    protected UsuarioService $usuarioService;

    public function __construct()
    {
        $this->usuarioService = new UsuarioService();
    }

    public function login()
    {
        $throttler = \Config\Services::throttler();

        // Límite de 5 intentos por minuto por IP (mismo criterio que LoginController::auth legacy).
        if ($throttler->check('api_login_' . md5($this->request->getIPAddress()), 5, MINUTE) === false) {
            return $this->fail('Demasiados intentos. Por favor, espera un minuto.', 429);
        }

        $body     = $this->getBody();
        $login    = $body['login'] ?? null;
        $password = $body['password'] ?? null;

        if (empty($login) || empty($password)) {
            return $this->fail('Login y contraseña son obligatorios.', 422);
        }

        $resultado = $this->usuarioService->autenticar($login, $password);
        if ($resultado['status'] !== 'success') {
            return $this->fail($resultado['message'], 401);
        }

        return $this->ok(JwtIssuer::issue($resultado['data']));
    }

    public function register()
    {
        $body = $this->getBody();

        $data = [
            'name'    => $body['name'] ?? null,
            'surname' => $body['surname'] ?? null,
            'user'    => $body['user'] ?? null,
            'email'   => $body['email'] ?? null,
            'pass'    => $body['pass'] ?? null,
        ];

        if (in_array(null, $data, true)) {
            return $this->fail('Todos los campos (name, surname, user, email, pass) son obligatorios.', 422);
        }

        $resultado = $this->usuarioService->registrarUsuario($data);
        if ($resultado['status'] !== 'success') {
            return $this->fail($resultado['message'], 422);
        }

        // Autenticamos automáticamente tras el registro exitoso.
        $auth = $this->usuarioService->autenticar($data['email'], $data['pass']);
        if ($auth['status'] !== 'success') {
            return $this->ok(null, 201);
        }

        return $this->ok(JwtIssuer::issue($auth['data']), 201);
    }

    public function refresh()
    {
        $refreshToken = $this->getBody()['refresh_token'] ?? null;
        if (empty($refreshToken)) {
            return $this->fail('refresh_token es obligatorio.', 422);
        }

        $config = config(JwtConfig::class);

        try {
            $payload = Jwt::decode($refreshToken, $config->secret);
        } catch (RuntimeException) {
            return $this->fail('Refresh token inválido o expirado.', 401);
        }

        if (($payload['type'] ?? null) !== 'refresh') {
            return $this->fail('El token provisto no es un refresh token.', 401);
        }

        $usuario = $this->usuarioService->getUsuario($payload['id_usuario']);
        if (!$usuario || $usuario['baja'] === 'SI') {
            return $this->fail('Usuario no encontrado o suspendido.', 401);
        }

        return $this->ok(JwtIssuer::issue([
            'id_usuario' => $usuario['id_usuario'],
            'nombre'     => $usuario['nombre'],
            'apellido'   => $usuario['apellido'],
            'email'      => $usuario['email'],
            'usuario'    => $usuario['usuario'],
            'perfil_id'  => $usuario['perfil_id'],
            'imagen'     => $usuario['imagen'],
        ]));
    }

    public function me()
    {
        $user = ApiAuthContext::user();
        if (!$user) {
            return $this->fail('No autenticado.', 401);
        }

        return $this->ok($user);
    }
}
