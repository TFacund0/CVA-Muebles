<?php

namespace App\Controllers\Api\Admin;

use App\Controllers\Api\BaseApiController;
use App\Services\CategoriaService;

class AdminCategoriaController extends BaseApiController
{
    protected CategoriaService $categoriaService;

    public function __construct()
    {
        $this->categoriaService = new CategoriaService();
    }

    public function index()
    {
        return $this->ok($this->categoriaService->getCategoriasConStats(false));
    }

    public function store()
    {
        $body = $this->getBody();

        $resultado = $this->categoriaService->crear(['descripcion' => $body['descripcion'] ?? null]);

        if ($resultado['status'] !== 'success') {
            return $this->fail($resultado['message'], 422);
        }

        return $this->ok(null, 201);
    }

    public function update($id = null)
    {
        $body = $this->getBody();

        $resultado = $this->categoriaService->actualizar($id, ['descripcion' => $body['descripcion'] ?? null]);

        if ($resultado['status'] !== 'success') {
            return $this->fail($resultado['message'], 422);
        }

        return $this->ok(null);
    }

    public function destroy($id = null)
    {
        $resultado = $this->categoriaService->eliminar($id);

        if ($resultado['status'] !== 'success') {
            return $this->fail($resultado['message'], 422);
        }

        return $this->ok(null);
    }

    public function toggle($id = null)
    {
        $resultado = $this->categoriaService->toggleEstado($id);

        if (!$resultado) {
            return $this->fail('Categoría no encontrada.', 404);
        }

        return $this->ok(null);
    }
}
