<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

/**
 * Controlador base para todos los endpoints de la API REST de CVA Muebles.
 * Centraliza el formato de respuesta JSON uniforme ({status, data, message}).
 */
abstract class BaseApiController extends ResourceController
{
    protected $format = 'json';

    protected function ok($data = null, int $code = 200)
    {
        return $this->response->setStatusCode($code)->setJSON([
            'status'  => 'success',
            'data'    => $data,
            'message' => null,
        ]);
    }

    protected function fail(string $message, int $code = 400, $data = null)
    {
        return $this->response->setStatusCode($code)->setJSON([
            'status'  => 'error',
            'data'    => $data,
            'message' => $message,
        ]);
    }

    /**
     * Lee el body de la request como array asociativo, sea JSON o form-urlencoded/multipart.
     * Antes `getJSON(true) ?? getPost()` estaba repetido literal en 5 controllers distintos.
     *
     * @return array<string, mixed>
     */
    protected function getBody(): array
    {
        return $this->request->getJSON(true) ?? $this->request->getPost() ?? [];
    }
}
