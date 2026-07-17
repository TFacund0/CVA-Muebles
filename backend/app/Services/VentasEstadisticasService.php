<?php

namespace App\Services;

use App\Constants\VentaEstado;
use App\Models\VentasCabeceraModel;
use App\Models\VentasPagosModel;

/**
 * Class VentasEstadisticasService
 *
 * Extraído de VentasService (mezclaba reportes junto con la creación de pedidos,
 * pagos y la cola de prioridad en un solo archivo de 569 líneas). Aísla las
 * consultas de listado/estadísticas del panel admin (detalleVentas, dashboard).
 *
 * VentasService lo sigue exponiendo vía getVentasConEstadisticas()/getDashboardStats()
 * para no romper a los controladores que ya lo consumen (delegación interna, misma API pública).
 *
 * @package App\Services
 */
class VentasEstadisticasService
{
    protected VentasCabeceraModel $ventasModel;
    protected VentasPagosModel $pagosModel;

    public function __construct()
    {
        $this->ventasModel = new VentasCabeceraModel();
        $this->pagosModel  = new VentasPagosModel();
    }

    /**
     * Recupera el listado total de pedidos (separándolos en solicitudes de presupuesto y pedidos activos en el taller),
     * computando métricas financieras consolidadas (ingresos reales acumulados por cobros) y operacionales de producción
     * para el mes calendario en curso.
     *
     * @return array Estructura enriquecida con pedidos en taller ('ventas'), presupuestos ('solicitados') y métricas ('counts').
     */
    public function getVentasConEstadisticas($search = null, $estado = null, $filterMode = 'client')
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        $totalRecaudado = $this->pagosModel->selectSum('monto')->first();
        $recaudadoReal = (float) ($totalRecaudado['monto'] ?? 0);

        // Pedidos para métricas
        $totalVentas = $this->ventasModel->where('estado_aprobacion !=', 'RECHAZADO')->countAllResults();

        $counts = [
            'total'      => $totalVentas,
            'mensuales'  => $this->ventasModel->countMensuales($currentMonth, $currentYear),
            'pendientes' => $this->ventasModel->countEstado(VentaEstado::PENDIENTE) + $this->ventasModel->countEstado('ACEPTADO'),
            'en_proceso' => $this->ventasModel->countEstado(VentaEstado::EN_PROCESO),
            'terminados' => $this->ventasModel->countEstado(VentaEstado::TERMINADO) + $this->ventasModel->countEstado(VentaEstado::ENTREGADO),
            'ingresos'   => $recaudadoReal
        ];

        $isServerMode = ($filterMode === 'server');

        // Obtener ventas filtradas si hay filtro, de lo contrario todas
        if ($search || ($estado && strtoupper($estado) !== 'ALL') || $isServerMode) {
            $resultado = $this->ventasModel->getVentasFiltradas($search, $estado, $isServerMode);
            $ventas = $resultado['data'];
            $pager = $resultado['pager'];
        } else {
            $resultado = $this->ventasModel->getVentasFiltradas();
            $ventas = $resultado['data'];
            $pager = null;
        }

        $ventas_procesadas = [];
        $solicitados = [];

        // Solución Problema N+1: Cargar todos los totales pagados en 1 sola consulta
        $venta_ids = array_column($ventas, 'id');
        $totales_pagados = $this->pagosModel->getTotalesPagadosBatch($venta_ids);

        foreach ($ventas as &$venta) {
            $venta['total_pagado'] = $totales_pagados[$venta['id']] ?? 0.0;
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
            'pager'       => $pager,
            'counts'      => $counts
        ];
    }

    /**
     * Recupera el volumen agregado de pedidos clasificados por su fase activa de producción
     * (PENDIENTE, EN_PROCESO, TERMINADO, ENTREGADO) a través de una consulta única agrupada.
     *
     * @return array Histograma de cantidades por estado operacional de fabricación.
     */
    public function getDashboardStats()
    {
        $db = \Config\Database::connect();

        $rows = $db
            ->table('ventas_cabecera')
            ->select('estado, COUNT(*) as total')
            ->whereIn('estado', VentaEstado::all())
            ->groupBy('estado')
            ->get()
            ->getResultArray();

        $stats = array_fill_keys(VentaEstado::all(), 0);
        foreach ($rows as $row) {
            $stats[$row['estado']] = (int) $row['total'];
        }

        return $stats;
    }
}
