<?php

namespace App\Services;

/**
 * Class GoogleAuthService
 *
 * Servicio encargado de gestionar la autenticación OAuth 2.0 con Google
 * utilizando cURL nativo para evitar dependencias de Composer.
 */
class GoogleAuthService
{
    private $clientId;
    private $clientSecret;
    private $redirectUri;

    public function __construct()
    {
        // Se cargan desde el archivo .env
        $this->clientId = getenv('GOOGLE_CLIENT_ID');
        $this->clientSecret = getenv('GOOGLE_CLIENT_SECRET');
        $this->redirectUri = getenv('GOOGLE_REDIRECT_URI');
    }

    /**
     * Genera la URL a la cual el usuario será redirigido para iniciar sesión.
     *
     * @return string URL de autenticación de Google.
     */
    public function getLoginUrl()
    {
        $params = [
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->redirectUri,
            'response_type' => 'code',
            'scope'         => 'email profile',
            'access_type'   => 'online',
            'prompt'        => 'select_account'
        ];

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }

    /**
     * Intercambia el código de autorización temporal por un Access Token.
     *
     * @param string $code El código recibido de Google en el callback.
     * @return array|false Retorna el Access Token si es exitoso, o false.
     */
    public function authenticate($code)
    {
        $ch = curl_init('https://oauth2.googleapis.com/token');

        $params = [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri'  => $this->redirectUri,
            'grant_type'    => 'authorization_code',
            'code'          => $code
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Evitar problemas locales con certificados

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $data = json_decode($response, true);
            return $data['access_token'] ?? false;
        }

        return false;
    }

    /**
     * Obtiene los datos del perfil de usuario de Google usando el Access Token.
     *
     * @param string $accessToken El token de acceso OAuth 2.0.
     * @return array|false Retorna un array con 'email', 'name', 'picture' (u otros), o false en caso de error.
     */
    public function getUserProfile($accessToken)
    {
        $ch = curl_init('https://www.googleapis.com/oauth2/v2/userinfo');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            return json_decode($response, true);
        }

        return false;
    }
}
