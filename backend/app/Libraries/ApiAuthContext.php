<?php

namespace App\Libraries;

/**
 * Contenedor estático simple para el payload del usuario autenticado vía JWT
 * durante el ciclo de vida de la request. Lo setea JwtAuth::before(), lo leen
 * los controladores de la API (App\Controllers\Api\*).
 */
class ApiAuthContext
{
    private static ?array $user = null;

    public static function set(array $payload): void
    {
        self::$user = $payload;
    }

    public static function user(): ?array
    {
        return self::$user;
    }
}
