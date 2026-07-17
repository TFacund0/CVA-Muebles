<?php

namespace App\Controllers\Api;

use App\Models\ProductoImagenModel;
use App\Models\ProductoModel;

class ProductoController extends BaseApiController
{
    protected ProductoModel $productoModel;
    protected ProductoImagenModel $productoImagenModel;

    public function __construct()
    {
        $this->productoModel = new ProductoModel();
        $this->productoImagenModel = new ProductoImagenModel();
    }

    public function index()
    {
        $categoria = $this->request->getGet('categoria');
        return $this->ok($this->productoModel->getProductosPublicos($categoria));
    }

    public function show($id = null)
    {
        $producto = $this->productoModel->getProducto($id);

        if (!$producto || $producto['eliminado'] === 'SI') {
            return $this->fail('Producto no encontrado.', 404);
        }

        $producto['galeria'] = $this->productoImagenModel->getImagenesPorProducto($id);

        return $this->ok($producto);
    }
}
