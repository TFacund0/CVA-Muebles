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

    public function __construct($cart = null, ?ProductoModel $productoModel = null)
    {
        $this->cart = $cart ?? \Config\Services::cart();
        $this->productoModel = $productoModel ?? new ProductoModel();
    }

    /**
     * Agrega un producto al carrito con validación de stock.
     *
     * @param array $data Datos del formulario, debe incluir 'id_producto'
     * @return array ['status' => 'success'|'error', 'message' => string]
     */
    public function agregar($data)
    {
        if (!isset($data['id_producto'])) {
            return ['status' => 'error', 'message' => 'Identificador de producto no especificado.'];
        }

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
     *
     * @param string $rowid Identificador de fila del carrito
     * @return array|false ['status' => 'success'] o false si el item no existe
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
     *
     * @param string $rowid Identificador de fila del carrito
     * @return bool true si se procesó (decrementó o eliminó), false si el item no existe
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
     *
     * @param string $rowid Identificador de fila del carrito, o "all" para vaciarlo
     * @return bool true siempre que la operación se ejecuta
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
     *
     * @param array $rowids Lista de identificadores de fila del carrito
     * @return bool true si se eliminaron, false si la lista está vacía
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
     *
     * @return array Items actuales del carrito
     */
    public function getContenido()
    {
        return $this->cart->contents();
    }

    /**
     * Vacía el carrito.
     *
     * @return void
     */
    public function vaciar()
    {
        $this->cart->destroy();
    }
}
