<?php

namespace App\Controllers\Api;

use App\Libraries\ApiAuthContext;
use App\Models\ProductoModel;
use App\Services\VentasService;

class VentaController extends BaseApiController
{
    protected VentasService $ventasService;
    protected ProductoModel $productoModel;

    public function __construct()
    {
        $this->ventasService = new VentasService();
        $this->productoModel = new ProductoModel();
    }

    public function index()
    {
        $usuario = ApiAuthContext::user();
        return $this->ok($this->ventasService->getVentasPorUsuario($usuario['id_usuario']));
    }

    public function show($id = null)
    {
        $usuario = ApiAuthContext::user();
        $detalle = $this->ventasService->getGestionDetalle($id);

        if (!$detalle) {
            return $this->fail('Pedido no encontrado.', 404);
        }

        $esPropietario = (int) $detalle['venta']['usuario_id'] === (int) $usuario['id_usuario'];
        $esAdmin       = (int) $usuario['perfil_id'] === 1;

        if (!$esPropietario && !$esAdmin) {
            return $this->fail('No tenés permiso para ver este pedido.', 403);
        }

        return $this->ok($detalle);
    }

    public function store()
    {
        if (!env('SHOPPING_CART_ENABLED')) {
            return $this->fail('El carrito de compras no está habilitado.', 403);
        }

        $usuario = ApiAuthContext::user();
        $body = $this->getBody();

        $items = $body['items'] ?? [];
        if (empty($items)) {
            return $this->fail('Debés incluir al menos un producto.', 422);
        }

        // Nunca confiamos en un precio mandado por el cliente: se busca el precio de venta actual en la BD.
        $itemsAProcesar = [];
        foreach ($items as $item) {
            $producto = $this->productoModel->find($item['producto_id'] ?? null);
            if (!$producto) {
                return $this->fail("Producto {$item['producto_id']} no encontrado.", 422);
            }

            $cantidad = (int) ($item['cantidad'] ?? 0);
            if ($cantidad < 1) {
                return $this->fail('La cantidad debe ser al menos 1.', 422);
            }

            $itemsAProcesar[] = [
                'id'    => $producto['id_producto'],
                'name'  => $producto['nombre_prod'],
                'qty'   => $cantidad,
                'price' => $producto['precio_vta'],
            ];
        }

        $resultado = $this->ventasService->procesarVenta(
            $usuario['id_usuario'],
            $itemsAProcesar,
            $body['observaciones'] ?? ''
        );

        if ($resultado['status'] !== 'success') {
            return $this->fail($resultado['message'], 422);
        }

        return $this->ok($resultado, 201);
    }
}
