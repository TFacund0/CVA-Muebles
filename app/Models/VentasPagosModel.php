<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * @class VentasPagosModel
 * @brief Modelo para registrar y gestionar los pagos (señas/cuotas) de cada venta.
 */
class VentasPagosModel extends Model {
    protected $table = 'ventas_pagos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['venta_id', 'monto', 'fecha', 'nota'];

    protected $validationRules = [
        'venta_id' => 'required|numeric',
        'monto'    => 'required|numeric|greater_than[0]'
    ];

    /**
     * @brief Obtiene todos los pagos asociados a una venta específica.
     * @param int $venta_id ID de la venta.
     * @return array Listado de pagos.
     */
    public function getPagosPorVenta($venta_id) {
        return $this->where('venta_id', $venta_id)->orderBy('fecha', 'DESC')->findAll();
    }

    /**
     * @brief Obtiene el total pagado hasta el momento para una venta.
     * @param int $venta_id ID de la venta.
     * @return float Suma total de los montos pagados.
     */
    public function getTotalPagado($venta_id) {
        $result = $this->selectSum('monto')->where('venta_id', $venta_id)->first();
        return (float) ($result['monto'] ?? 0);
    }
}
