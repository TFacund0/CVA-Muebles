<?php

namespace App\Models;

use CodeIgniter\Model;

class VentasDetalleModel extends Model {
    protected $table = 'ventas_detalle';
    protected $primaryKey = 'id';
    protected $allowedFields = ['venta_id', 'producto_id', 'cantidad', 'precio'];

    protected $validationRules = [
        'venta_id' => 'required|numeric',
        'cantidad' => 'required|numeric',
        'precio'   => 'required|numeric'
    ];

    public function getDetallesAll() {
        $results = $this->select('ventas_detalle.id as id_detalle, ventas_detalle.venta_id, ventas_detalle.producto_id, ventas_detalle.cantidad, ventas_detalle.precio, ventas_cabecera.fecha, ventas_cabecera.estado, productos.nombre_prod, usuarios.usuario as nombre_usuario')
                        ->join('ventas_cabecera', 'ventas_cabecera.id = ventas_detalle.venta_id')
                        ->join('productos', 'productos.id_producto = ventas_detalle.producto_id', 'left')
                        ->join('usuarios', 'usuarios.id_usuario = ventas_cabecera.usuario_id')
                        ->findAll();

        foreach ($results as &$row) {
            $row['id'] = $row['id_detalle'];
        }

        return $results;
    }

    public function getDetalles($id = null, $id_usuario = null) {
        $builder = $this->select('ventas_detalle.id, ventas_detalle.venta_id, ventas_detalle.producto_id, ventas_detalle.cantidad, ventas_detalle.precio, productos.nombre_prod, productos.imagen, productos.descripcion')
                        ->join('productos', 'productos.id_producto = ventas_detalle.producto_id', 'left');

        if ($id != null) {
            $builder->where('ventas_detalle.venta_id', $id);
        }

        if ($id_usuario != null) {
            $builder->join('ventas_cabecera', 'ventas_cabecera.id = ventas_detalle.venta_id');
            $builder->where('ventas_cabecera.usuario_id', $id_usuario);
        }

        return $builder->findAll();
    }
}