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

    /**
     * Obtiene el detalle de ventas junto con los datos del producto, con filtros opcionales.
     *
     * @param int|null $id ID de la venta cuyos detalles se buscan (opcional).
     * @param int|null $id_usuario ID del usuario dueño de la venta, usado para filtrar por su cabecera (opcional).
     * @return array Listado de líneas de detalle de venta con los datos del producto.
     */
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