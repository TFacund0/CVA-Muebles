<?php

namespace App\Services;

use App\Models\ProductoModel;
use App\Models\ProductoImagenModel;
use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * Class ProductoService
 *
 * Servicio encargado de gestionar la lógica de negocio asociada al catálogo de productos (muebles)
 * de CVA Muebles. Provee operaciones CRUD avanzadas, lógica de borrado lógico (soft delete) y físico
 * (hard delete con control de integridad referencial), almacenamiento de imágenes principales y
 * galerías fotográficas adicionales.
 *
 * @package App\Services
 */
class ProductoService
{
    /**
     * @var ProductoModel Modelo principal de acceso a datos para los productos/muebles.
     */
    protected $productoModel;

    /**
     * @var ProductoImagenModel Modelo de acceso a datos para las galerías de imágenes secundarias de los productos.
     */
    protected $imagenModel;

    /**
     * Constructor del servicio.
     *
     * Inicializa los modelos requeridos para la administración de productos y sus galerías asociadas.
     */
    public function __construct()
    {
        $this->productoModel = new ProductoModel();
        $this->imagenModel = new ProductoImagenModel();
    }

    /**
     * Recupera el listado de todos los productos y calcula métricas generales
     * (totales, activos, sin stock, archivados/eliminados) para el tablero administrativo de gestión.
     *
     * @return array Estructura con la lista de productos ('productos') y métricas estadísticas ('counts').
     */
    public function getProductosConStats()
    {
        $productos = $this->productoModel->getProductoAll();

        $counts = [
            'total'      => count($productos),
            'activos'    => 0,
            'sin_stock'  => 0,
            'eliminados' => 0
        ];

        foreach ($productos as $p) {
            if ($p['eliminado'] == 'NO') {
                $counts['activos']++;
                if ($p['stock'] <= 0) {
                    $counts['sin_stock']++;
                }
            } else {
                $counts['eliminados']++;
            }
        }

        return [
            'productos' => $productos,
            'counts'    => $counts
        ];
    }

    /**
     * Recupera el listado de productos aptos para la venta al público general.
     * Excluye productos marcados como archivados/eliminados lógicamente o cuyas categorías asociadas estén inactivas.
     *
     * @return array Listado de productos activos aptos para venta.
     */
    public function getProductosPublicos()
    {
        return $this->productoModel->getProductosPublicos();
    }

    /**
     * Procesa el registro e inserción de un nuevo mueble en el catálogo del taller.
     * Se encarga de procesar físicamente la subida de la imagen destacada principal si se provee.
     *
     * @param array $data Atributos del producto a crear (nombre, categoría, precio, stock, etc.).
     * @param UploadedFile|null $image Archivo físico de la imagen principal cargada (opcional).
     * 
     * @return array Resumen de estado ('status' => 'success'|'error', 'message' => string).
     */
    public function crearProducto($data, UploadedFile $image = null)
    {
        try {
            if ($image && $image->isValid() && !$image->hasMoved()) {
                $tempName = $image->getRandomName();
                $nombre_imagen = pathinfo($tempName, PATHINFO_FILENAME) . '.webp';
                
                $image->move(FCPATH . 'assets/uploads', $tempName);
                
                $originalPath = FCPATH . 'assets/uploads/' . $tempName;
                $destPath = FCPATH . 'assets/uploads/' . $nombre_imagen;
                
                $imageService = \Config\Services::image();
                $imageService->withFile($originalPath);
                
                $width = $imageService->getWidth();
                $height = $imageService->getHeight();
                
                if ($width > 800 || $height > 800) {
                    $imageService->resize(800, 800, true, 'auto');
                }
                
                $imageService->save($destPath, 80);
                
                if ($originalPath !== $destPath && file_exists($originalPath)) {
                    @unlink($originalPath);
                }
                
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
     * Procesa la actualización de los datos de un mueble existente en el catálogo.
     * Elimina el archivo de imagen física anterior del disco si una nueva imagen principal es provista.
     *
     * @param int|string $id Identificador del producto a actualizar.
     * @param array $data Nuevos atributos del producto.
     * @param UploadedFile|null $image Nuevo archivo de imagen principal (opcional).
     * 
     * @return array Resumen de estado ('status' => 'success'|'error', 'message' => string).
     */
    public function actualizarProducto($id, $data, UploadedFile $image = null)
    {
        try {
            if ($image && $image->isValid() && !$image->hasMoved()) {
                // Borrar imagen anterior si existe
                $producto_actual = $this->productoModel->find($id);
                if ($producto_actual && !empty($producto_actual['imagen'])) {
                    $old_path = FCPATH . 'assets/uploads/' . $producto_actual['imagen'];
                    if (file_exists($old_path)) {
                        @unlink($old_path);
                    }
                }

                $tempName = $image->getRandomName();
                $nombre_imagen = pathinfo($tempName, PATHINFO_FILENAME) . '.webp';
                
                $image->move(FCPATH . 'assets/uploads', $tempName);
                
                $originalPath = FCPATH . 'assets/uploads/' . $tempName;
                $destPath = FCPATH . 'assets/uploads/' . $nombre_imagen;
                
                $imageService = \Config\Services::image();
                $imageService->withFile($originalPath);
                
                $width = $imageService->getWidth();
                $height = $imageService->getHeight();
                
                if ($width > 800 || $height > 800) {
                    $imageService->resize(800, 800, true, 'auto');
                }
                
                $imageService->save($destPath, 80);
                
                if ($originalPath !== $destPath && file_exists($originalPath)) {
                    @unlink($originalPath);
                }
                
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
     * Realiza una baja lógica (soft delete) sobre un producto del catálogo marcándolo como archivado/eliminado ('SI').
     *
     * @param int|string $id Identificador del producto.
     * 
     * @return bool|int|string Retorna el resultado de la actualización en la base de datos.
     */
    public function eliminar($id)
    {
        return $this->productoModel->update($id, ['eliminado' => 'SI']);
    }

    /**
     * Reactiva o restaura un producto archivado lógicamente para habilitar su venta y visibilidad pública ('NO').
     *
     * @param int|string $id Identificador del producto.
     * 
     * @return bool|int|string Retorna el resultado de la actualización en la base de datos.
     */
    public function reactivar($id)
    {
        return $this->productoModel->update($id, ['eliminado' => 'NO']);
    }

    /**
     * Recupera un producto por su identificador único junto a su galería completa de imágenes adicionales.
     *
     * @param int|string $id Identificador del producto.
     * 
     * @return array|null Datos del producto enriquecidos con la clave asociativa 'galeria' conteniendo sus imágenes secundarias.
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
     * Procesa la subida y almacenamiento seguro de múltiples imágenes adicionales para enriquecer
     * la galería descriptiva secundaria de un mueble.
     *
     * @param int|string $producto_id Identificador único del producto asociado.
     * @param array $files Colección de objetos UploadedFile correspondientes a las fotos secundarias.
     * 
     * @return bool True si al menos una imagen fue procesada y registrada exitosamente, false en caso contrario.
     */
    public function subirImagenesGaleria($producto_id, $files)
    {
        if (empty($files)) {
            return false;
        }

        $count = 0;
        foreach ($files as $img) {
            if ($img->isValid() && !$img->hasMoved()) {
                $tempName = $img->getRandomName();
                $nombre_imagen = pathinfo($tempName, PATHINFO_FILENAME) . '.webp';
                
                $img->move(FCPATH . 'assets/uploads', $tempName);
                
                $originalPath = FCPATH . 'assets/uploads/' . $tempName;
                $destPath = FCPATH . 'assets/uploads/' . $nombre_imagen;
                
                try {
                    $imageService = \Config\Services::image();
                    $imageService->withFile($originalPath);
                    
                    $width = $imageService->getWidth();
                    $height = $imageService->getHeight();
                    
                    if ($width > 800 || $height > 800) {
                        $imageService->resize(800, 800, true, 'auto');
                    }
                    
                    $imageService->save($destPath, 80);
                    
                    if ($originalPath !== $destPath && file_exists($originalPath)) {
                        @unlink($originalPath);
                    }
                    
                    $this->imagenModel->insert([
                        'producto_id' => $producto_id,
                        'imagen'      => $nombre_imagen,
                        'orden'       => 0
                    ]);
                    $count++;
                } catch (\Exception $e) {
                    if (file_exists($originalPath)) {
                        @unlink($originalPath);
                    }
                    log_message('error', 'Error optimizando imagen de galería: ' . $e->getMessage());
                }
            }
        }
        return $count > 0;
    }

    /**
     * Elimina físicamente una imagen secundaria de la galería descriptiva del servidor
     * y remueve su registro asociado de la base de datos.
     *
     * @param int|string $id Identificador único de la imagen en la galería.
     * 
     * @return bool True si la imagen secundaria fue eliminada correcta física y lógicamente; false si no existe.
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
     * Procesa la eliminación física total y definitiva de un producto del catálogo de la tienda.
     * Restringe severamente el borrado si el mueble se encuentra asociado a ventas históricas o pedidos
     * en curso, salvaguardando la integridad referencial. Si no hay dependencias, procede a limpiar
     * todas sus imágenes de galerías secundarias y favoritos antes de borrar el mueble.
     *
     * @param int|string $id Identificador único del producto.
     * 
     * @return array Resumen de estado ('status' => 'success'|'error', 'message' => string).
     */
    public function eliminarPermanente($id)
    {
        $db = \Config\Database::connect();
        
        // 1. Verificar si tiene registros asociados en ventas_detalle
        $ventas = $db->table('ventas_detalle')->where('producto_id', $id)->countAllResults();
        if ($ventas > 0) {
            return [
                'status'  => 'error',
                'message' => 'No se puede eliminar permanentemente este mueble porque ya está asociado a pedidos o ventas registradas. Puedes mantenerlo como Archivado para proteger el historial de ventas.'
            ];
        }

        try {
            // Obtener datos del producto para borrar su imagen principal
            $producto = $this->productoModel->find($id);
            if (!$producto) {
                return [
                    'status'  => 'error',
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
                'status'  => 'success',
                'message' => 'Mueble eliminado permanentemente del catálogo.'
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Ocurrió un error al intentar eliminar el producto: ' . $e->getMessage()
            ];
        }
    }
}
