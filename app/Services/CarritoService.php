<?php

namespace App\Services;

use App\Models\ProductoModel;

/**
 * Servicio para manejar la lógica del carrito de compras.
 */
class CarritoService
{
    protected $cart;
    protected $productoModel;

    public function __construct()
    {
        $this->cart = \Config\Services::cart();
        $this->productoModel = new ProductoModel();
    }

    /**
     * Agrega un producto al carrito con validación de stock.
     */
    public function agregar($data)
    {
        $producto = $this->productoModel->find($data['id_producto']);
        if (!$producto) {
            return ['status' => 'error', 'message' => 'Producto no encontrado.'];
        }

        $this->cart->insert([
            'id'     => $producto['id_producto'],
            'qty'    => 1,
            'name'   => $producto['nombre_prod'],
            'price'  => $producto['precio_vta'],
            'imagen' => $producto['imagen'],
        ]);

        return ['status' => 'success', 'message' => 'Producto agregado al carrito.'];
    }

    /**
     * Incrementa la cantidad de un producto.
     */
    public function incrementar($rowid)
    {
        $item = $this->cart->getItem($rowid);
        if (!$item) return false;

        $this->cart->update([
            'rowid' => $rowid,
            'qty'   => $item['qty'] + 1
        ]);

        return ['status' => 'success'];
    }

    /**
     * Decrementa la cantidad de un producto.
     */
    public function decrementar($rowid)
    {
        $item = $this->cart->getItem($rowid);
        if (!$item) return false;

        if ($item['qty'] > 1) {
            $this->cart->update([
                'rowid' => $rowid,
                'qty'   => $item['qty'] - 1
            ]);
        } else {
            $this->cart->remove($rowid);
        }
        return true;
    }

    /**
     * Elimina un item o vacía el carrito.
     */
    public function eliminar($rowid)
    {
        if ($rowid == "all") {
            $this->cart->destroy();
        } else {
            $this->cart->remove($rowid);
        }
        return true;
    }

    /**
     * Elimina varios items por su rowid.
     */
    public function eliminarVarios($rowids)
    {
        if (empty($rowids)) return false;
        foreach ($rowids as $rowid) {
            $this->cart->remove($rowid);
        }
        return true;
    }

    /**
     * Obtiene el contenido del carrito.
     */
    public function getContenido()
    {
        return $this->cart->contents();
    }

    /**
     * Vacía el carrito.
     */
    public function vaciar()
    {
        $this->cart->destroy();
    }
}
