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
    protected $cloudinary;

    public function __construct(?ProductoModel $productoModel = null, ?ProductoImagenModel $imagenModel = null, ?CloudinaryService $cloudinary = null)
    {
        $this->productoModel = $productoModel ?? new ProductoModel();
        $this->imagenModel = $imagenModel ?? new ProductoImagenModel();
        $this->cloudinary = $cloudinary ?? new CloudinaryService();
    }

    /**
     * Obtiene el listado de productos con estadísticas para el panel.
     *
     * @return array ['productos' => array, 'counts' => array]
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
            if ($p['deleted_at'] === null) {
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
     *
     * @return array Productos disponibles para el catálogo público
     */
    public function getProductosPublicos()
    {
        return $this->productoModel->getProductosPublicos();
    }

    /**
     * Crea un nuevo producto.
     *
     * @param array $data Datos del producto a insertar
     * @param \CodeIgniter\HTTP\Files\UploadedFile|null $image Imagen principal opcional
     * @return array ['status' => 'success'|'error', 'message' => string]
     */
    public function crearProducto($data, $image = null)
    {
        try {
            if ($image && $image->isValid() && !$image->hasMoved()) {
                $subida = $this->cloudinary->subir($image->getTempName());
                $data['imagen'] = $subida['secure_url'];
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
     *
     * @param int $id Identificador del producto
     * @param array $data Datos a actualizar
     * @param \CodeIgniter\HTTP\Files\UploadedFile|null $image Nueva imagen principal opcional (reemplaza la anterior)
     * @return array ['status' => 'success'|'error', 'message' => string]
     */
    public function actualizarProducto($id, $data, $image = null)
    {
        try {
            if ($image && $image->isValid() && !$image->hasMoved()) {
                // Borrar imagen anterior si existe
                $producto_actual = $this->productoModel->withDeleted()->find($id);
                if ($producto_actual && !empty($producto_actual['imagen'])) {
                    $this->cloudinary->eliminarPorUrl($producto_actual['imagen']);
                }

                $subida = $this->cloudinary->subir($image->getTempName());
                $data['imagen'] = $subida['secure_url'];
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
     *
     * @param int $id Identificador del producto
     * @return bool Resultado de la eliminación
     */
    public function eliminar($id)
    {
        return $this->productoModel->delete($id);
    }

    /**
     * Reactiva un producto eliminado.
     *
     * @param int $id Identificador del producto
     * @return bool Resultado de la actualización
     */
    public function reactivar($id)
    {
        // deleted_at no está en $allowedFields (es un campo protegido, gestionado
        // por el soft-delete de CI4), así que Model::update() lo descartaría.
        // Se restaura vía una query directa a la tabla (bypass del Model).
        return \Config\Database::connect()
            ->table('productos')
            ->where('id_producto', $id)
            ->update(['deleted_at' => null]);
    }

    /**
     * Obtiene un producto por ID con su galería de imágenes.
     *
     * @param int $id Identificador del producto
     * @return array|null Datos del producto con la clave 'galeria', o null si no existe
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
     *
     * @param int $producto_id Identificador del producto
     * @param array $files Lista de archivos subidos (UploadedFile)
     * @return bool true si al menos una imagen se subió, false si no hay archivos o ninguna es válida
     */
    public function subirImagenesGaleria($producto_id, $files)
    {
        if (empty($files)) return false;

        $count = 0;
        foreach ($files as $img) {
            if ($img->isValid() && !$img->hasMoved()) {
                $subida = $this->cloudinary->subir($img->getTempName());

                $this->imagenModel->insert([
                    'producto_id' => $producto_id,
                    'imagen'      => $subida['secure_url'],
                    'orden'       => 0
                ]);
                $count++;
            }
        }
        return $count > 0;
    }

    /**
     * Elimina una imagen de la galería, incluyendo el archivo físico si existe.
     *
     * @param int $id Identificador de la imagen
     * @return bool Resultado de la eliminación, false si la imagen no existe
     */
    public function eliminarImagenGaleria($id)
    {
        $img = $this->imagenModel->find($id);
        if ($img) {
            $this->cloudinary->eliminarPorUrl($img['imagen']);
            return $this->imagenModel->delete($id);
        }
        return false;
    }

    /**
     * Elimina permanentemente un producto del catálogo si no tiene ventas o pedidos asociados.
     *
     * @param int $id Identificador del producto
     * @return array ['status' => 'success'|'error', 'message' => string]
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
            // Obtener datos del producto para borrar su imagen principal (incluye archivados)
            $producto = $this->productoModel->withDeleted()->find($id);
            if (!$producto) {
                return [
                    'status' => 'error',
                    'message' => 'El mueble especificado no existe.'
                ];
            }

            // 2. Eliminar todas las imágenes de la galería (física y lógicamente)
            $imagenesGaleria = $this->imagenModel->getImagenesPorProducto($id);
            foreach ($imagenesGaleria as $img) {
                $this->cloudinary->eliminarPorUrl($img['imagen']);
            }
            $db->table('producto_imagenes')->where('producto_id', $id)->delete();

            // 3. Eliminar de la tabla favoritos
            $db->table('favoritos')->where('producto_id', $id)->delete();

            // 4. Borrar la imagen principal del producto
            if (!empty($producto['imagen'])) {
                $this->cloudinary->eliminarPorUrl($producto['imagen']);
            }

            // 5. Borrar físicamente el producto (purge=true, ignora el soft-delete)
            $this->productoModel->delete($id, true);

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
