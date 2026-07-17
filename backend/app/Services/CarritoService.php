<?php

namespace App\Services;

use App\Models\ProductoModel;
use CodeIgniterCart\Cart;

/**
 * Class CarritoService
 *
 * Servicio encargado de gestionar toda la lógica de negocio y persistencia del
 * carrito de compras en la sesión activa del usuario, incluyendo el control y validación de stock.
 *
 * @package App\Services
 */
class CarritoService
{
    /**
     * @var Cart Instancia del servicio de carrito provisto por el framework.
     */
    protected $cart;

    /**
     * @var ProductoModel Modelo de acceso a datos para la gestión de productos.
     */
    protected $productoModel;

    /**
     * Constructor del servicio.
     *
     * Inicializa la instancia del carrito de compras de CodeIgniter y el modelo de producto.
     */
    public function __construct()
    {
        $this->cart = \Config\Services::cart();
        $this->productoModel = new ProductoModel();
    }

    /**
     * Agrega un producto al carrito de compras con validación básica de datos y existencia.
     *
     * @param array $data Conjunto de datos de la petición POST que contiene 'id_producto'.
     * 
     * @return array Resumen de estado ('status' => 'success'|'error', 'message' => string).
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
     * Incrementa en una unidad la cantidad de un ítem existente dentro del carrito.
     *
     * @param string $rowid Identificador único del ítem dentro de la sesión del carrito.
     * 
     * @return array|false Retorna un array con el estado de éxito o false si el ítem no existe.
     */
    public function incrementar($rowid)
    {
        $item = $this->cart->getItem($rowid);
        if (!$item) {
            return false;
        }

        $this->cart->update([
            'rowid' => $rowid,
            'qty'   => $item['qty'] + 1
        ]);

        return ['status' => 'success'];
    }

    /**
     * Decrementa en una unidad la cantidad de un ítem. Si la cantidad desciende
     * de uno, el ítem se elimina automáticamente del carrito de compras.
     *
     * @param string $rowid Identificador único del ítem dentro del carrito.
     * 
     * @return bool True si la operación fue exitosa, false si el ítem no existe.
     */
    public function decrementar($rowid)
    {
        $item = $this->cart->getItem($rowid);
        if (!$item) {
            return false;
        }

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
     * Elimina un ítem específico del carrito, o vacía por completo el carrito de compras
     * si el identificador es igual a 'all'.
     *
     * @param string $rowid Identificador del ítem en el carrito o la cadena 'all' para vaciar.
     * 
     * @return bool Siempre retorna true.
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
     * Elimina múltiples ítems de forma simultánea a partir de una lista de identificadores.
     *
     * @param array $rowids Colección de identificadores de fila (rowids) a eliminar.
     * 
     * @return bool True si se eliminaron correctamente, false si la lista de entrada está vacía.
     */
    public function eliminarVarios($rowids)
    {
        if (empty($rowids)) {
            return false;
        }
        foreach ($rowids as $rowid) {
            $this->cart->remove($rowid);
        }
        return true;
    }

    /**
     * Recupera la totalidad de los productos almacenados en el carrito de compras actual.
     *
     * @return array Listado asociativo con el contenido actual del carrito de compras.
     */
    public function getContenido()
    {
        return $this->cart->contents();
    }

    /**
     * Vacía por completo la sesión del carrito de compras.
     *
     * @return void
     */
    public function vaciar()
    {
        $this->cart->destroy();
    }
}
