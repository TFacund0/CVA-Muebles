<?php

namespace App\Controllers\Api\Admin;

use App\Controllers\Api\BaseApiController;
use App\Services\ConsultaService;
use App\Services\GaleriaService;
use App\Services\VentasService;

class AdminVentaController extends BaseApiController
{
    protected VentasService $ventasService;
    protected ConsultaService $consultaService;
    protected GaleriaService $galeriaService;

    public function __construct()
    {
        $this->ventasService   = new VentasService();
        $this->consultaService = new ConsultaService();
        $this->galeriaService  = new GaleriaService();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $estado = $this->request->getGet('estado');

        return $this->ok($this->ventasService->getVentasConEstadisticas($search, $estado));
    }

    public function dashboard()
    {
        return $this->ok([
            'estados'            => $this->ventasService->getDashboardStats(),
            'consultas_activas'  => $this->consultaService->countActivas(),
            'galeria_pendientes' => $this->galeriaService->getPendientesCount(),
        ]);
    }

    public function estado($id = null)
    {
        $body = $this->getBody();

        if (empty($body['estado'])) {
            return $this->fail('El campo estado es obligatorio.', 422);
        }

        $resultado = $this->ventasService->actualizarEstado($id, $body['estado']);

        if (!$resultado) {
            return $this->fail('No se pudo actualizar el estado del pedido.', 422);
        }

        return $this->ok(null);
    }

    public function pago($id = null)
    {
        $body = $this->getBody();
        $monto = (float) ($body['monto'] ?? 0);

        if ($monto <= 0) {
            return $this->fail('El monto debe ser mayor a 0.', 422);
        }

        $resultado = $this->ventasService->registrarPago($id, $monto, $body['nota'] ?? '');

        if (!$resultado) {
            return $this->fail('No se pudo registrar el pago.', 422);
        }

        return $this->ok(null, 201);
    }

    public function observaciones($id = null)
    {
        $body = $this->getBody();

        $resultado = $this->ventasService->actualizarObservaciones($id, $body['observaciones'] ?? '');

        if (!$resultado) {
            return $this->fail('No se pudieron guardar las observaciones.', 422);
        }

        return $this->ok(null);
    }

    public function prioridad($id = null)
    {
        $body = $this->getBody();

        if (($body['direccion'] ?? null) === 'bajar') {
            $this->ventasService->bajarPrioridad($id);
        } else {
            $this->ventasService->subirPrioridad($id);
        }

        return $this->ok(null);
    }

    public function destroy($id = null)
    {
        $resultado = $this->ventasService->eliminarPedidoPermanente($id);

        if ($resultado['status'] !== 'success') {
            return $this->fail($resultado['message'], 422);
        }

        return $this->ok(null);
    }

    public function personalizado()
    {
        $data = $this->request->getPost([
            'usuario_id', 'nombre_cliente', 'detalles_obra', 'total_venta', 'monto_sena',
        ]);

        if (empty($data['nombre_cliente']) || empty($data['detalles_obra']) || empty($data['total_venta'])) {
            return $this->fail('Nombre del cliente, detalles y total son obligatorios.', 422);
        }

        $data['monto_sena'] = (float) ($data['monto_sena'] ?? 0);

        $imagen = $this->request->getFile('imagen_referencia');
        $imagenValida = $imagen && $imagen->isValid() && !$imagen->hasMoved() ? $imagen : null;

        $resultado = $this->ventasService->registrarPedidoPersonalizado($data, $imagenValida);

        if ($resultado['status'] !== 'success') {
            return $this->fail($resultado['message'], 422);
        }

        return $this->ok($resultado, 201);
    }
}
