<?php

namespace App\Controllers\Api\Admin;

use App\Controllers\Api\BaseApiController;
use App\Services\GaleriaService;

class AdminGaleriaController extends BaseApiController
{
    protected GaleriaService $galeriaService;

    public function __construct()
    {
        $this->galeriaService = new GaleriaService();
    }

    public function index()
    {
        return $this->ok($this->galeriaService->getAllConUsuarios());
    }

    public function aprobar($id = null)
    {
        $resultado = $this->galeriaService->aprobar($id);

        if (!$resultado) {
            return $this->failJson('No se pudo aprobar la foto.', 422);
        }

        return $this->ok(null);
    }

    public function destroy($id = null)
    {
        $resultado = $this->galeriaService->eliminar($id);

        if (!$resultado) {
            return $this->failJson('No se pudo eliminar la foto.', 422);
        }

        return $this->ok(null);
    }
}
