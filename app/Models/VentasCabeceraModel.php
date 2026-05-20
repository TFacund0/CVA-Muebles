<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * @class VentasCabeceraModel
 * @brief Modelo para la gestión de la cabecera de ventas.
 */
class VentasCabeceraModel extends Model {
    protected $table = 'ventas_cabecera';
    protected $primaryKey = 'id';
    protected $allowedFields = ['fecha', 'usuario_id', 'total_venta', 'estado', 'observaciones', 'tipo_pedido', 'estado_aprobacion', 'prioridad'];

    protected $validationRules = [
        'usuario_id'  => 'required|numeric',
        'total_venta' => 'required|numeric|greater_than_equal_to[0]',
        'estado'      => 'required|alpha_dash'
    ];

    public function getVentas($id = null, $id_usuario = null) {
        $builder = $this->select('ventas_cabecera.id, ventas_cabecera.fecha, ventas_cabecera.usuario_id, ventas_cabecera.total_venta, ventas_cabecera.estado, ventas_cabecera.estado_aprobacion, ventas_cabecera.tipo_pedido, ventas_cabecera.observaciones, ventas_cabecera.prioridad, usuarios.nombre, usuarios.apellido, usuarios.email, usuarios.usuario')
                        ->join('usuarios', 'usuarios.id_usuario = ventas_cabecera.usuario_id', 'left');

        if ($id != null) {
            $builder->where('ventas_cabecera.id', $id);
        }

        if ($id_usuario != null) {
            $builder->where('ventas_cabecera.usuario_id', $id_usuario);
        }
        
        return $builder->orderBy('ventas_cabecera.prioridad', 'DESC')
                       ->orderBy('ventas_cabecera.fecha', 'DESC')
                       ->findAll();
    }

    public function getVentasActivas() {
        return $this->select('ventas_cabecera.*')
                    ->where('estado_aprobacion !=', 'RECHAZADO')
                    ->where('estado_aprobacion !=', 'SOLICITUD')
                    ->orderBy('prioridad', 'DESC')
                    ->orderBy('fecha', 'DESC')
                    ->findAll();
    }

    public function countMensuales($mes, $anio) {
        return $this->where('MONTH(fecha)', $mes)
                    ->where('YEAR(fecha)', $anio)
                    ->where('estado_aprobacion !=', 'RECHAZADO')
                    ->countAllResults();
    }

    public function countEstado($estado) {
        return $this->where('estado_aprobacion !=', 'RECHAZADO')
                    ->where('estado', $estado)
                    ->countAllResults();
    }
}
