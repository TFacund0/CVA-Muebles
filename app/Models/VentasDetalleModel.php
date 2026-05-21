<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Class VentasDetalleModel
 *
 * Modelo que interactúa con la tabla 'ventas_detalle' de la base de datos de CVA Muebles.
 * Representa la apertura o desglose individual de los muebles solicitados dentro de cada pedido/venta.
 *
 * @property int|string $id Identificador único del registro de detalle de la venta.
 * @property int|string $venta_id Identificador único de la cabecera de la venta vinculada.
 * @property int|string|null $producto_id Identificador del producto/mueble adquirido (nulo para pedidos personalizados a medida).
 * @property int|string $cantidad Cantidad de unidades solicitadas del mueble.
 * @property float|string $precio Precio unitario acordado o de venta para el ítem al momento del pedido.
 * 
 * @package App\Models
 */
class VentasDetalleModel extends Model
{
    /**
     * @var string Nombre de la tabla en la base de datos.
     */
    protected $table = 'ventas_detalle';

    /**
     * @var string Nombre de la columna que actúa como clave primaria de la tabla.
     */
    protected $primaryKey = 'id';

    /**
     * @var array Campos de la tabla que están permitidos para su asignación e inserción masiva.
     */
    protected $allowedFields = ['venta_id', 'producto_id', 'cantidad', 'precio'];

    /**
     * @var array Reglas de validación interna que aplica el modelo de forma automática antes de guardar.
     */
    protected $validationRules = [
        'venta_id' => 'required|numeric',
        'cantidad' => 'required|numeric',
        'precio'   => 'required|numeric'
    ];

    /**
     * Recupera la lista abierta de ítems asociados a un pedido específico, filtrando de forma opcional por
     * identificador de pedido o por identificador de cliente, e incorporando datos descriptivos de los muebles.
     *
     * @param int|string|null $id Identificador único de la venta/pedido (opcional).
     * @param int|string|null $id_usuario Identificador único del usuario cliente (opcional).
     * 
     * @return array Desglose de ítems del pedido.
     */
    public function getDetalles($id = null, $id_usuario = null)
    {
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