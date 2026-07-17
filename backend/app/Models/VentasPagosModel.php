<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Class VentasPagosModel
 *
 * Modelo que interactúa con la tabla 'ventas_pagos' de la base de datos de CVA Muebles.
 * Registra y gestiona las amortizaciones, señas y cobros parciales de cada venta.
 *
 * @property int|string $id Identificador único del registro de pago/cobro.
 * @property int|string $venta_id Identificador único de la cabecera de la venta vinculada.
 * @property float|string $monto Importe monetario del cobro registrado.
 * @property string $fecha Marca de tiempo del registro del pago.
 * @property string $nota Notas descriptivas adicionales del cobro (ej. "Seña", "Transferencia").
 * 
 * @package App\Models
 */
class VentasPagosModel extends Model
{
    /**
     * @var string Nombre de la tabla en la base de datos.
     */
    protected $table = 'ventas_pagos';

    /**
     * @var string Nombre de la columna que actúa como clave primaria de la tabla.
     */
    protected $primaryKey = 'id';

    /**
     * @var array Campos de la tabla que están permitidos para su asignación e inserción masiva.
     */
    protected $allowedFields = ['venta_id', 'monto', 'fecha', 'nota'];

    /**
     * @var array Reglas de validación interna que aplica el modelo de forma automática antes de guardar.
     */
    protected $validationRules = [
        'venta_id' => 'required|numeric',
        'monto'    => 'required|numeric|greater_than[0]'
    ];

    /**
     * Recupera todos los abonos o pagos registrados para un pedido en curso,
     * ordenándolos cronológicamente de forma descendente (últimos cobros registrados primero).
     *
     * @param int|string $venta_id Identificador único del pedido.
     * 
     * @return array Listado de cobros realizados sobre el pedido.
     */
    public function getPagosPorVenta($venta_id)
    {
        return $this->where('venta_id', $venta_id)->orderBy('fecha', 'DESC')->findAll();
    }

    /**
     * Calcula la suma acumulada total de los importes cobrados o amortizados para un pedido.
     *
     * @param int|string $venta_id Identificador del pedido.
     * 
     * @return float Suma total monetaria pagada hasta el momento.
     */
    public function getTotalPagado($venta_id)
    {
        $result = $this->selectSum('monto')->where('venta_id', $venta_id)->first();
        return (float) ($result['monto'] ?? 0);
    }

    /**
     * Calcula la suma acumulada de los importes cobrados para múltiples pedidos en una sola consulta.
     * Soluciona el problema N+1 al procesar listados masivos.
     *
     * @param array $venta_ids Arreglo de identificadores de pedidos.
     * 
     * @return array Diccionario asociativo [venta_id => total_pagado].
     */
    public function getTotalesPagadosBatch(array $venta_ids)
    {
        if (empty($venta_ids)) {
            return [];
        }

        $results = $this->select('venta_id, SUM(monto) as total_pagado')
                        ->whereIn('venta_id', $venta_ids)
                        ->groupBy('venta_id')
                        ->findAll();

        $totales = [];
        foreach ($results as $row) {
            $totales[$row['venta_id']] = (float) $row['total_pagado'];
        }

        return $totales;
    }
}
