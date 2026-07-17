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

    /**
     * Metadata de paginación lista para JSON, a partir del Pager que deja seteado
     * un Model después de llamar a paginate($perPage, $group). El número de página
     * actual lo lee CI4 automáticamente del query string (?page_<group>=N).
     *
     * @param \CodeIgniter\Pager\Pager|null $pager
     */
    protected function pagerMeta($pager, string $group): array
    {
        if (!$pager) {
            return ['page' => 1, 'per_page' => 0, 'total' => 0, 'page_count' => 1];
        }

        return [
            'page'       => $pager->getCurrentPage($group),
            'per_page'   => $pager->getPerPage($group),
            'total'      => $pager->getTotal($group),
            'page_count' => $pager->getPageCount($group),
        ];
    }
}
