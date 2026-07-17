<?php

namespace App\Controllers\Api\Admin;

use App\Controllers\Api\BaseApiController;
use App\Services\ProductoService;

class AdminProductoController extends BaseApiController
{
    protected ProductoService $productoService;

    public function __construct()
    {
        $this->productoService = new ProductoService();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $categoria = $this->request->getGet('categoria');

        // Paginado server-side: la página actual la lee CI4 del query string
        // (?page_productos=N), no hace falta parsearla acá.
        $resultado = $this->productoService->getProductosConStats($search, $categoria, 'server');

        return $this->ok([
            'productos' => $resultado['productos'],
            'counts'    => $resultado['counts'],
            'pager'     => $this->pagerMeta($resultado['pager'], 'productos'),
        ]);
    }

    public function show($id = null)
    {
        // A diferencia del endpoint público (ProductoController::show), este no
        // oculta productos archivados — el admin necesita poder editarlos.
        $producto = $this->productoService->getProductoConGaleria($id);

        if (!$producto) {
            return $this->fail('Producto no encontrado.', 404);
        }

        return $this->ok($producto);
    }

    public function store()
    {
        // No se revalida acá: ProductoModel::$validationRules ya corre automáticamente
        // dentro de crearProducto() -> $productoModel->insert(), y devuelve los mismos
        // errores vía $resultado['message']. Duplicar las reglas acá era código muerto
        // que además podía divergir del modelo con el tiempo.
        $data = $this->request->getPost([
            'nombre_prod', 'categoria_id', 'precio', 'precio_vta', 'stock', 'stock_min', 'descripcion',
        ]);
        $data['eliminado'] = 'NO';

        $imagen = $this->request->getFile('imagen');
        $imagenValida = $imagen && $imagen->isValid() && !$imagen->hasMoved() ? $imagen : null;

        $resultado = $this->productoService->crearProducto($data, $imagenValida);

        if ($resultado['status'] !== 'success') {
            return $this->fail($resultado['message'], 422);
        }

        return $this->ok(null, 201);
    }

    public function update($id = null)
    {
        $data = $this->request->getPost([
            'nombre_prod', 'categoria_id', 'precio', 'precio_vta', 'stock', 'stock_min', 'descripcion',
        ]);

        $imagen = $this->request->getFile('imagen');
        $imagenValida = $imagen && $imagen->isValid() && !$imagen->hasMoved() ? $imagen : null;

        $resultado = $this->productoService->actualizarProducto($id, $data, $imagenValida);

        if ($resultado['status'] !== 'success') {
            return $this->fail($resultado['message'], 422);
        }

        return $this->ok(null);
    }

    public function estado($id = null)
    {
        $body = $this->getBody();
        $accion = $body['accion'] ?? null;

        $resultado = $accion === 'reactivar'
            ? $this->productoService->reactivar($id)
            : $this->productoService->eliminar($id);

        if (!$resultado) {
            return $this->fail('No se pudo actualizar el estado del producto.', 422);
        }

        return $this->ok(null);
    }

    public function destroy($id = null)
    {
        $resultado = $this->productoService->eliminarPermanente($id);

        if ($resultado['status'] !== 'success') {
            return $this->fail($resultado['message'], 422);
        }

        return $this->ok(null);
    }

    public function subirGaleria($id = null)
    {
        $files = $this->request->getFileMultiple('imagenes') ?? [];

        if (empty($files)) {
            return $this->fail('Debés incluir al menos una imagen.', 422);
        }

        $resultado = $this->productoService->subirImagenesGaleria($id, $files);

        if (!$resultado) {
            return $this->fail('No se pudo subir la galería.', 422);
        }

        return $this->ok(null, 201);
    }

    public function eliminarFotoGaleria($fotoId = null)
    {
        $resultado = $this->productoService->eliminarFotoGaleria($fotoId);

        if (!$resultado) {
            return $this->fail('Foto no encontrada.', 404);
        }

        return $this->ok(null);
    }
}
