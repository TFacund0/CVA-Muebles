<?php

namespace App\Libraries;

use Config\Jwt as JwtConfig;

/**
 * Arma el par access/refresh token para un usuario autenticado. Extraído de
 * AuthController::issueTokens() para reusarlo también desde GoogleAuthController
 * (login con Google emite JWT igual que el login normal, sin duplicar la lógica).
 */
class JwtIssuer
{
    /**
     * @param array $usuario Debe incluir: id_usuario, nombre, apellido, email, usuario, perfil_id, imagen.
     */
    public static function issue(array $usuario): array
    {
        $config = config(JwtConfig::class);
        $now    = time();

        $claims = [
            'id_usuario' => $usuario['id_usuario'],
            'nombre'     => $usuario['nombre'],
            'apellido'   => $usuario['apellido'],
            'email'      => $usuario['email'],
            'usuario'    => $usuario['usuario'],
            'perfil_id'  => $usuario['perfil_id'],
            'imagen'     => $usuario['imagen'],
        ];

        $accessToken = Jwt::encode($claims + ['type' => 'access', 'iat' => $now, 'exp' => $now + $config->accessTtl], $config->secret);
        $refreshToken = Jwt::encode(['id_usuario' => $usuario['id_usuario'], 'type' => 'refresh', 'iat' => $now, 'exp' => $now + $config->refreshTtl], $config->secret);

        return [
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in'    => $config->accessTtl,
            'user'          => $claims,
        ];
    }
}
