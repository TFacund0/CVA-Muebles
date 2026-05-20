<?php

namespace App\Services;

use App\Models\ProductoModel;
use App\Models\ProductoImagenModel;

/**
 * Servicio para manejar la lógica de negocio relacionada con los productos.
 */
class ProductoService
{
    protected $productoModel;
    protected $imagenModel;

    public function __construct()
    {
        $this->productoModel = new ProductoModel();
        $this->imagenModel = new ProductoImagenModel();
    }

    /**
     * Obtiene el listado de productos con estadísticas para el panel.
     */
    public function getProductosConStats()
    {
        $productos = $this->productoModel->getProductoAll();

        $counts = [
            'total' => count($productos),
            'activos' => 0,
            'sin_stock' => 0,
            'eliminados' => 0
        ];

        foreach ($productos as $p) {
            if ($p['eliminado'] == 'NO') {
                $counts['activos']++;
                if ($p['stock'] <= 0) $counts['sin_stock']++;
            } else {
                $counts['eliminados']++;
            }
        }

        return [
            'productos' => $productos,
            'counts' => $counts
        ];
    }

    /**
     * Obtiene el listado de productos públicos (no eliminados y con categorías activas).
     */
    public function getProductosPublicos()
    {
        return $this->productoModel->getProductosPublicos();
    }

    /**
     * Crea un nuevo producto.
     */
    public function crearProducto($data, $image = null)
    {
        try {
            if ($image && $image->isValid() && !$image->hasMoved()) {
                $nombre_imagen = $image->getRandomName();
                $image->move(FCPATH . 'assets/uploads', $nombre_imagen);
                $data['imagen'] = $nombre_imagen;
            }

            if ($this->productoModel->insert($data) === false) {
                return ['status' => 'error', 'message' => 'Errores: ' . implode(', ', $this->productoModel->errors())];
            }
            return ['status' => 'success', 'message' => 'Producto creado con éxito.'];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Actualiza un producto existente.
     */
    public function actualizarProducto($id, $data, $image = null)
    {
        try {
            if ($image && $image->isValid() && !$image->hasMoved()) {
                // Borrar imagen anterior si existe
                $producto_actual = $this->productoModel->find($id);
                if ($producto_actual && !empty($producto_actual['imagen'])) {
                    $old_path = FCPATH . 'assets/uploads/' . $producto_actual['imagen'];
                    if (file_exists($old_path)) @unlink($old_path);
                }

                $nombre_imagen = $image->getRandomName();
                $image->move(FCPATH . 'assets/uploads', $nombre_imagen);
                $data['imagen'] = $nombre_imagen;
            }

            if ($this->productoModel->update($id, $data) === false) {
                return ['status' => 'error', 'message' => 'Errores: ' . implode(', ', $this->productoModel->errors())];
            }
            return ['status' => 'success', 'message' => 'Producto actualizado con éxito.'];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Marca un producto como eliminado (soft delete).
     */
    public function eliminar($id)
    {
        return $this->productoModel->update($id, ['eliminado' => 'SI']);
    }

    /**
     * Reactiva un producto eliminado.
     */
    public function reactivar($id)
    {
        return $this->productoModel->update($id, ['eliminado' => 'NO']);
    }

    /**
     * Obtiene un producto por ID con su galería de imágenes.
     */
    public function getProductoConGaleria($id)
    {
        $producto = $this->productoModel->getProducto($id);
        if ($producto) {
            $producto['galeria'] = $this->imagenModel->getImagenesPorProducto($id);
        }
        return $producto;
    }

    /**
     * Sube imágenes adicionales a la galería.
     */
    public function subirImagenesGaleria($producto_id, $files)
    {
        if (empty($files)) return false;

        $count = 0;
        foreach ($files as $img) {
            if ($img->isValid() && !$img->hasMoved()) {
                $newName = $img->getRandomName();
                $img->move(FCPATH . 'assets/uploads', $newName);

                $this->imagenModel->insert([
                    'producto_id' => $producto_id,
                    'imagen'      => $newName,
                    'orden'       => 0
                ]);
                $count++;
            }
        }
        return $count > 0;
    }

    /**
     * Elimina una imagen de la galería.
     */
    public function eliminarImagenGaleria($id)
    {
        $img = $this->imagenModel->find($id);
        if ($img) {
            $path = FCPATH . 'assets/uploads/' . $img['imagen'];
            if (file_exists($path)) {
                @unlink($path);
            }
            return $this->imagenModel->delete($id);
        }
        return false;
    }

    /**
     * Elimina permanentemente un producto del catálogo si no tiene ventas o pedidos asociados.
     */
    public function eliminarPermanente($id)
    {
        $db = \Config\Database::connect();
        
        // 1. Verificar si tiene registros asociados en ventas_detalle
        $ventas = $db->table('ventas_detalle')->where('producto_id', $id)->countAllResults();
        if ($ventas > 0) {
            return [
                'status' => 'error',
                'message' => 'No se puede eliminar permanentemente este mueble porque ya está asociado a pedidos o ventas registradas. Puedes mantenerlo como Archivado para proteger el historial de ventas.'
            ];
        }

        try {
            // Obtener datos del producto para borrar su imagen principal
            $producto = $this->productoModel->find($id);
            if (!$producto) {
                return [
                    'status' => 'error',
                    'message' => 'El mueble especificado no existe.'
                ];
            }

            // 2. Eliminar todas las imágenes de la galería (física y lógicamente)
            $imagenesGaleria = $this->imagenModel->getImagenesPorProducto($id);
            foreach ($imagenesGaleria as $img) {
                $imgPath = FCPATH . 'assets/uploads/' . $img['imagen'];
                if (file_exists($imgPath)) {
                    @unlink($imgPath);
                }
            }
            $db->table('producto_imagenes')->where('producto_id', $id)->delete();

            // 3. Eliminar de la tabla favoritos
            $db->table('favoritos')->where('producto_id', $id)->delete();

            // 4. Borrar la imagen principal del producto
            if (!empty($producto['imagen'])) {
                $mainImgPath = FCPATH . 'assets/uploads/' . $producto['imagen'];
                if (file_exists($mainImgPath)) {
                    @unlink($mainImgPath);
                }
            }

            // 5. Borrar físicamente el producto
            $this->productoModel->delete($id);

            return [
                'status' => 'success',
                'message' => 'Mueble eliminado permanentemente del catálogo.'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Ocurrió un error al intentar eliminar el producto: ' . $e->getMessage()
            ];
        }
    }
}
