<?php

namespace App\Controllers\Api\Admin;

use App\Controllers\Api\BaseApiController;
use App\Services\ConsultaService;

class AdminConsultaController extends BaseApiController
{
    protected ConsultaService $consultaService;

    public function __construct()
    {
        $this->consultaService = new ConsultaService();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $asunto = $this->request->getGet('asunto');

        $resultado = $this->consultaService->getConsultasConStats($search, $asunto, 'server');

        return $this->ok([
            'consultas' => $resultado['consultas'],
            'counts'    => $resultado['counts'],
            'pager'     => $this->pagerMeta($resultado['pager'], 'consultas'),
        ]);
    }

    public function eliminar($id = null)
    {
        $resultado = $this->consultaService->desactivar($id);

        if (!$resultado) {
            return $this->fail('No se pudo archivar la consulta.', 422);
        }

        return $this->ok(null);
    }

    public function restaurar($id = null)
    {
        $resultado = $this->consultaService->restaurar($id);

        if (!$resultado) {
            return $this->fail('No se pudo restaurar la consulta.', 422);
        }

        return $this->ok(null);
    }

    public function destroy($id = null)
    {
        $resultado = $this->consultaService->eliminarPermanente($id);

        if (!$resultado) {
            return $this->fail('No se pudo eliminar la consulta.', 422);
        }

        return $this->ok(null);
    }
}
