<?php

namespace App\Controllers\Api;

use App\Libraries\JwtIssuer;
use App\Models\UsuarioModel;
use App\Services\GoogleAuthService;
use App\Services\UsuarioService;

/**
 * Class GoogleAuthController
 *
 * Versión JWT/stateless del login con Google (equivalente a UsuarioController::
 * loginGoogle/callbackGoogle/completarRegistroGoogle/finalizarRegistroGoogle,
 * que dependen de sesión de PHP y solo sirven a las vistas legacy).
 *
 * El intercambio de código y el client_secret de Google se procesan acá, nunca
 * en el frontend. El hand-off de tokens al frontend es por fragmento de URL
 * (#access_token=...), que el navegador nunca envía al servidor en requests
 * posteriores — evita que el JWT quede en logs del servidor Next.js.
 *
 * Para el registro cuando el email no existe todavía: como esta API no tiene
 * sesión, el perfil de Google no puede guardarse en `session()` como hace el
 * flujo legacy. Se guarda en caché server-side detrás de un token aleatorio de
 * un solo uso — así el paso de "completar registro" nunca confía en un email
 * que mande el cliente (evitaría que cualquiera se registre alegando ser
 * cualquier email sin haber pasado realmente por Google).
 */
class GoogleAuthController extends BaseApiController
{
    private const PENDING_TTL = 600; // 10 minutos

    protected GoogleAuthService $googleAuthService;
    protected UsuarioService $usuarioService;

    public function __construct()
    {
        $this->googleAuthService = new GoogleAuthService(env('GOOGLE_API_REDIRECT_URI'));
        $this->usuarioService    = new UsuarioService();
    }

    /**
     * Redirige al usuario a la pantalla de consentimiento de Google.
     */
    public function redirect()
    {
        return redirect()->to($this->googleAuthService->getLoginUrl());
    }

    /**
     * Callback de Google. Intercambia el code, arma el perfil, y redirige al
     * frontend con los tokens (login existente) o con un pending_token
     * (cuenta nueva, falta elegir username).
     */
    public function callback()
    {
        $frontendUrl = rtrim(env('FRONTEND_URL', 'http://localhost:3000'), '/');
        $code = $this->request->getGet('code');

        if (!$code) {
            return redirect()->to("{$frontendUrl}/login?google_error=" . rawurlencode('La autenticación con Google fue cancelada.'));
        }

        $accessToken = $this->googleAuthService->authenticate($code);
        if (!$accessToken) {
            return redirect()->to("{$frontendUrl}/login?google_error=" . rawurlencode('Error al obtener el token de Google.'));
        }

        $profile = $this->googleAuthService->getUserProfile($accessToken);
        if (!$profile || empty($profile['email'])) {
            return redirect()->to("{$frontendUrl}/login?google_error=" . rawurlencode('No se pudo leer el perfil de tu cuenta de Google.'));
        }

        $resultado = $this->usuarioService->loginOrRegisterGoogle($profile);

        if ($resultado['status'] === 'success') {
            $tokens = JwtIssuer::issue($resultado['data']);
            $query = http_build_query([
                'access_token'  => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'expires_in'    => $tokens['expires_in'],
            ]);
            return redirect()->to("{$frontendUrl}/login/callback#{$query}");
        }

        if ($resultado['status'] === 'pending_registration') {
            $pendingToken = bin2hex(random_bytes(20));
            \Config\Services::cache()->save('google_pending_' . $pendingToken, $resultado['profile'], self::PENDING_TTL);

            $query = http_build_query([
                'token'    => $pendingToken,
                'nombre'   => $resultado['profile']['nombre'] ?? '',
                'apellido' => $resultado['profile']['apellido'] ?? '',
                'email'    => $resultado['profile']['email'] ?? '',
            ]);
            return redirect()->to("{$frontendUrl}/registro/google#{$query}");
        }

        return redirect()->to("{$frontendUrl}/login?google_error=" . rawurlencode($resultado['message'] ?? 'No se pudo iniciar sesión con Google.'));
    }

    /**
     * Completa el registro de una cuenta nueva originada en Google, una vez que
     * el usuario eligió su nombre de usuario. El email/nombre vienen del perfil
     * cacheado en callback() (identificado por $pendingToken), nunca del body
     * de esta request — es la garantía de que el email fue verificado por Google.
     */
    public function completar()
    {
        $body = $this->getBody();
        $pendingToken = $body['token'] ?? null;
        $username     = $body['user'] ?? null;

        if (empty($pendingToken) || empty($username)) {
            return $this->fail('Token y nombre de usuario son obligatorios.', 422);
        }

        $profile = \Config\Services::cache()->get('google_pending_' . $pendingToken);
        if (!$profile) {
            return $this->fail('La sesión de Google expiró. Por favor, intentá nuevamente.', 401);
        }

        $rules = ['user' => 'required|min_length[4]|is_unique[usuarios.usuario]'];
        if (!$this->validateData($body, $rules)) {
            return $this->fail('El nombre de usuario ya está en uso o es demasiado corto.', 422);
        }

        $userData = [
            'name'    => $profile['nombre'],
            'surname' => $profile['apellido'],
            'user'    => $username,
            'email'   => $profile['email'],
            'pass'    => bin2hex(random_bytes(10)), // Irrelevante: entra siempre vía Google.
        ];

        $resultado = $this->usuarioService->registrarUsuario($userData);
        if ($resultado['status'] !== 'success') {
            return $this->fail($resultado['message'], 422);
        }

        $usuarioModel = new UsuarioModel();
        $newUser = $usuarioModel->where('email', $profile['email'])->first();

        if (!empty($profile['imagen'])) {
            $usuarioModel->update($newUser['id_usuario'], ['imagen' => $profile['imagen']]);
            $newUser['imagen'] = $profile['imagen'];
        }

        \Config\Services::cache()->delete('google_pending_' . $pendingToken);

        return $this->ok(JwtIssuer::issue($newUser), 201);
    }
}
