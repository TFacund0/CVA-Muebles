<?php

namespace App\Services;

use App\Models\VentasCabeceraModel;
use App\Models\VentasDetalleModel;
use App\Models\ProductoModel;
use App\Models\VentasPagosModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

/**
 * Servicio para manejar la lógica de negocio relacionada con las ventas.
 */
class VentasService
{
    protected $ventasModel;
    protected $detalleModel;
    protected $productoModel;
    protected $pagosModel;
    protected $db;

    public function __construct()
    {
        $this->ventasModel = new VentasCabeceraModel();
        $this->detalleModel = new VentasDetalleModel();
        $this->productoModel = new ProductoModel();
        $this->pagosModel = new VentasPagosModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Obtiene todas las ventas con estadísticas procesadas para el panel.
     */
    public function getVentasConEstadisticas()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        $totalRecaudado = $this->pagosModel->selectSum('monto')->first();
        $recaudadoReal = (float) ($totalRecaudado['monto'] ?? 0);

        // Fetch only non-rejected to save memory
        $ventas = $this->ventasModel->select('ventas_cabecera.id, ventas_cabecera.fecha, ventas_cabecera.usuario_id, ventas_cabecera.total_venta, ventas_cabecera.estado, ventas_cabecera.estado_aprobacion, ventas_cabecera.tipo_pedido, ventas_cabecera.observaciones, ventas_cabecera.prioridad, usuarios.nombre, usuarios.apellido, usuarios.email, usuarios.usuario')
                                    ->join('usuarios', 'usuarios.id_usuario = ventas_cabecera.usuario_id', 'left')
                                    ->where('ventas_cabecera.estado_aprobacion !=', 'RECHAZADO')
                                    ->orderBy('ventas_cabecera.prioridad', 'DESC')
                                    ->orderBy('ventas_cabecera.fecha', 'DESC')
                                    ->findAll();

        $counts = [
            'total'      => count($ventas),
            'mensuales'  => $this->ventasModel->countMensuales($currentMonth, $currentYear),
            'pendientes' => $this->ventasModel->countEstado('PENDIENTE') + $this->ventasModel->countEstado('ACEPTADO'),
            'en_proceso' => $this->ventasModel->countEstado('EN_PROCESO'),
            'terminados' => $this->ventasModel->countEstado('TERMINADO') + $this->ventasModel->countEstado('ENTREGADO'),
            'ingresos'   => $recaudadoReal
        ];

        $ventas_procesadas = [];
        $solicitados = [];

        foreach ($ventas as &$venta) {
            $venta['total_pagado'] = $this->pagosModel->getTotalPagado($venta['id']);
            $nombre_completo = ($venta['nombre'] ?? '') . ' ' . ($venta['apellido'] ?? '');
            $venta['search_data'] = strtolower(esc($venta['id'] . ' ' . $nombre_completo . ' ' . ($venta['usuario'] ?? '')));

            if (($venta['estado_aprobacion'] ?? '') == 'SOLICITUD') {
                $solicitados[] = $venta;
            } else {
                $ventas_procesadas[] = $venta;
            }
        }

        return [
            'ventas'      => $ventas_procesadas,
            'solicitados' => $solicitados,
            'counts'      => $counts
        ];
    }

    /**
     * Procesa una venta completa: valida stock (opcional), crea registros y guarda mensaje.
     * Nota: Ya no descuenta stock aquí, se hace al aprobar el pedido.
     */
    public function procesarVenta($usuario_id, $items_seleccionados, $observaciones = '')
    {
        if (empty($items_seleccionados)) {
            return ['status' => 'error', 'message' => 'No hay productos seleccionados para el pedido.'];
        }

        $total = 0;
        foreach ($items_seleccionados as $item) {
            $total += $item['price'] * $item['qty'];
        }

        $this->db->transStart();
        try {
            $venta_id = $this->ventasModel->insert([
                'usuario_id'       => $usuario_id,
                'fecha'            => date('Y-m-d H:i:s'),
                'total_venta'      => $total,
                'estado'           => 'PENDIENTE',
                'estado_aprobacion'=> 'SOLICITUD',
                'observaciones'    => $observaciones,
                'tipo_pedido'      => 'CARRITO'
            ]);

            if (!$venta_id) {
                throw new \Exception("No se pudo crear la cabecera de la venta.");
            }

            foreach ($items_seleccionados as $item) {
                $this->detalleModel->insert([
                    'venta_id'    => $venta_id,
                    'producto_id' => $item['id'],
                    'cantidad'    => $item['qty'],
                    'precio'      => $item['price'],
                ]);
            }

            $this->db->transComplete();
            return ['status' => 'success', 'total' => $total, 'venta_id' => $venta_id];
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Obtiene el detalle completo de una venta para gestión administrativa.
     */
    public function getGestionDetalle($venta_id)
    {
        $venta = $this->ventasModel->getVentas($venta_id)[0] ?? null;
        if (!$venta) return null;

        $detalles = $this->detalleModel->getDetalles($venta_id);
        $pagos = $this->pagosModel->getPagosPorVenta($venta_id);
        $total_pagado = $this->pagosModel->getTotalPagado($venta_id);

        return [
            'venta' => $venta,
            'detalles' => $detalles,
            'pagos' => $pagos,
            'total_pagado' => $total_pagado,
            'saldo_pendiente' => $venta['total_venta'] - $total_pagado
        ];
    }

    /**
     * Actualiza el estado de una venta y gestiona el stock si es necesario.
     */
    public function actualizarEstado($venta_id, $estado)
    {
        $venta_actual = $this->ventasModel->find($venta_id);
        if (!$venta_actual) return false;

        // --- FLUJO DE APROBACIÓN (SOLICITUD -> ACEPTADO/RECHAZADO) ---
        if ($estado == 'ACEPTADO' || $estado == 'RECHAZADO') {
            $this->db->transStart();
            try {
                // Lógica de stock removida ya que se trabaja bajo pedido.

                $this->ventasModel->update($venta_id, [
                    'estado_aprobacion' => $estado,
                    'estado' => ($estado == 'ACEPTADO') ? 'PENDIENTE' : $venta_actual['estado']
                ]);

                $this->db->transComplete();
                return true;
            } catch (\Exception $e) {
                $this->db->transRollback();
                return false;
            }
        }

        // --- FLUJO DE PRODUCCIÓN (PENDIENTE -> EN_PROCESO -> TERMINADO -> ENTREGADO) ---
        // Aquí no se toca stock, solo actualizamos la fase del pedido.
        return $this->ventasModel->update($venta_id, ['estado' => $estado]);
    }

    /**
     * Registra un pago para una venta.
     */
    public function registrarPago($venta_id, $monto, $nota = '')
    {
        return $this->pagosModel->insert([
            'venta_id' => $venta_id,
            'monto'    => $monto,
            'nota'     => $nota
        ]);
    }

    /**
     * Actualiza las observaciones de una venta.
     */
    public function actualizarObservaciones($venta_id, $observaciones)
    {
        return $this->ventasModel->update($venta_id, ['observaciones' => $observaciones]);
    }

    /**
     * Obtiene estadísticas agregadas para el dashboard en una sola query.
     */
    public function getDashboardStats()
    {
        $rows = $this->db
            ->table('ventas_cabecera')
            ->select('estado, COUNT(*) as total')
            ->whereIn('estado', ['PENDIENTE', 'EN_PROCESO', 'TERMINADO', 'ENTREGADO'])
            ->groupBy('estado')
            ->get()
            ->getResultArray();

        $stats = ['PENDIENTE' => 0, 'EN_PROCESO' => 0, 'TERMINADO' => 0, 'ENTREGADO' => 0];
        foreach ($rows as $row) {
            $stats[$row['estado']] = (int) $row['total'];
        }

        return $stats;
    }

    /**
     * Registra un pedido personalizado.
     */
    public function registrarPedidoPersonalizado($data, $file = null)
    {
        $usuarioModel = new \App\Models\UsuarioModel();
        
        // Si viene un usuario_id, lo usamos. Si no, usamos el genérico.
        $usuario_id = $data['usuario_id'] ?? null;
        
        if (empty($usuario_id)) {
            $usuario_gen = $usuarioModel->where('usuario', 'cliente_whatsapp')->first();
            if (!$usuario_gen) return ['status' => 'error', 'message' => 'No se encontró el usuario genérico.'];
            $usuario_id = $usuario_gen['id_usuario'];
        }

        $this->db->transStart();
        try {
            // Manejo de Imagen Opcional
            $img_ref = "";
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $img_ref = $file->getRandomName();
                $file->move(FCPATH . 'assets/uploads/referencias/', $img_ref);
            }

            $observaciones = "CLIENTE: " . $data['nombre_cliente'] . "\n" . $data['detalles_obra'];
            if ($img_ref) {
                $observaciones .= "\n[IMG_REF:" . $img_ref . "]";
            }

            $venta_id = $this->ventasModel->insert([
                'usuario_id'       => $usuario_id,
                'total_venta'      => $data['total_venta'],
                'estado'           => 'PENDIENTE',
                'estado_aprobacion'=> 'ACEPTADO',
                'observaciones'    => $observaciones,
                'fecha'            => date('Y-m-d H:i:s'),
                'tipo_pedido'      => 'MANUAL'
            ]);
            
            $this->detalleModel->insert([
                'venta_id' => $venta_id, 'producto_id' => null, 'cantidad' => 1, 'precio' => $data['total_venta']
            ]);

            if ($data['monto_sena'] > 0) {
                $this->registrarPago($venta_id, $data['monto_sena'], 'Seña inicial - Pedido Manual');
            }

            $this->db->transComplete();
            return ['status' => 'success', 'venta_id' => $venta_id];
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    /**
     * Obtiene el historial de ventas de un usuario específico.
     */
    public function getVentasPorUsuario($usuario_id)
    {
        $ventas = $this->ventasModel->getVentas(null, $usuario_id);
        
        // Sort strictly by date DESC (most recent first) for customer view
        usort($ventas, function($a, $b) {
            return strtotime($b['fecha']) - strtotime($a['fecha']);
        });

        foreach ($ventas as &$v) {
            $v['items'] = $this->detalleModel->getDetalles($v['id']);
        }
        return $ventas;
    }

    /**
     * Incrementa la prioridad del pedido para subirlo en el listado activo.
     */
    public function subirPrioridad($venta_id)
    {
        $ventas_activas = $this->ventasModel->getVentasActivas();

        // Encontrar la posición actual en la lista activa
        $index = -1;
        for ($i = 0; $i < count($ventas_activas); $i++) {
            if ($ventas_activas[$i]['id'] == $venta_id) {
                $index = $i;
                break;
            }
        }

        if ($index > 0) {
            $item_current = $ventas_activas[$index];
            $item_above = $ventas_activas[$index - 1];

            $p_current = (int) ($item_current['prioridad'] ?? 0);
            $p_above = (int) ($item_above['prioridad'] ?? 0);

            if ($p_current != $p_above) {
                // Si las prioridades son distintas, las intercambiamos
                $this->ventasModel->update($item_current['id'], ['prioridad' => $p_above]);
                $this->ventasModel->update($item_above['id'], ['prioridad' => $p_current]);
            } else {
                // Si son iguales, al que sube (current) le damos la del de arriba + 1
                $this->ventasModel->update($item_current['id'], ['prioridad' => $p_above + 1]);
            }
        } elseif ($index == 0 && !empty($ventas_activas)) {
            // Ya está arriba de todo, pero incrementamos para asegurar
            $item_current = $ventas_activas[0];
            $p_current = (int) ($item_current['prioridad'] ?? 0);
            $this->ventasModel->update($item_current['id'], ['prioridad' => $p_current + 1]);
        }
    }

    /**
     * Decrementa la prioridad del pedido para bajarlo en el listado activo.
     */
    public function bajarPrioridad($venta_id)
    {
        $ventas_activas = $this->ventasModel->getVentasActivas();

        // Encontrar la posición actual en la lista activa
        $index = -1;
        for ($i = 0; $i < count($ventas_activas); $i++) {
            if ($ventas_activas[$i]['id'] == $venta_id) {
                $index = $i;
                break;
            }
        }

        if ($index != -1 && $index < count($ventas_activas) - 1) {
            $item_current = $ventas_activas[$index];
            $item_below = $ventas_activas[$index + 1];

            $p_current = (int) ($item_current['prioridad'] ?? 0);
            $p_below = (int) ($item_below['prioridad'] ?? 0);

            if ($p_current != $p_below) {
                // Si las prioridades son distintas, las intercambiamos
                $this->ventasModel->update($item_current['id'], ['prioridad' => $p_below]);
                $this->ventasModel->update($item_below['id'], ['prioridad' => $p_current]);
            } else {
                // Si son iguales, al que sube (below) le damos la del de arriba (current) + 1
                $this->ventasModel->update($item_below['id'], ['prioridad' => $p_current + 1]);
            }
        } elseif ($index == count($ventas_activas) - 1 && $index != -1) {
            // Ya está en el fondo, pero decrementamos para asegurar
            $item_current = $ventas_activas[$index];
            $p_current = (int) ($item_current['prioridad'] ?? 0);
            $this->ventasModel->update($item_current['id'], ['prioridad' => $p_current - 1]);
        }
    }
}
