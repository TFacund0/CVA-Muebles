<?php

namespace App\Filters;

use App\Libraries\ApiAuthContext;
use App\Libraries\Jwt;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Jwt as JwtConfig;
use RuntimeException;

/**
 * Clase JwtAuth
 *
 * Filtro de autenticación para la API REST de CVA Muebles.
 * Valida el header `Authorization: Bearer <token>` en vez de la sesión de cookies
 * (usada por Auth/AdminAuth para las vistas embebidas). Siempre responde en JSON.
 *
 * @package App\Filters
 */
class JwtAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeaderLine('Authorization');

        if (!$header || !preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
            return service('response')
                ->setJSON(['status' => 'error', 'message' => 'Token de autenticación no provisto.'])
                ->setStatusCode(401);
        }

        $config = config(JwtConfig::class);

        try {
            $payload = Jwt::decode($matches[1], $config->secret);
        } catch (RuntimeException) {
            return service('response')
                ->setJSON(['status' => 'error', 'message' => 'Token inválido o expirado.'])
                ->setStatusCode(401);
        }

        if (!empty($arguments) && in_array('admin', $arguments, true) && (int) ($payload['perfil_id'] ?? 0) !== 1) {
            return service('response')
                ->setJSON(['status' => 'error', 'message' => 'No tienes permisos.'])
                ->setStatusCode(403);
        }

        ApiAuthContext::set($payload);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No se requiere ninguna acción post-solicitud en este filtro.
    }
}
