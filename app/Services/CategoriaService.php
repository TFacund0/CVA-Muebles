<?php

namespace App\Services;

use App\Models\CategoriaModel;
use App\Models\ProductoModel;

/**
 * Class CategoriaService
 *
 * Servicio encargado de la lógica de negocio para la gestión de categorías
 * de productos de CVA Muebles, regulando el registro, actualización, baja
 * física o lógica (toggle), estadísticas de uso y control de dependencias
 * antes de la eliminación.
 *
 * @package App\Services
 */
class CategoriaService
{
    /**
     * @var CategoriaModel Modelo para interactuar con la tabla de categorías en la base de datos.
     */
    protected $categoriaModel;

    /**
     * @var ProductoModel Modelo para interactuar con la tabla de productos y validar sus relaciones.
     */
    protected $productoModel;

    /**
     * Constructor del servicio.
     *
     * Inicializa los modelos de acceso a datos para categorías y productos.
     */
    public function __construct()
    {
        $this->categoriaModel = new CategoriaModel();
        $this->productoModel = new ProductoModel();
    }

    /**
     * Obtiene el listado de categorías, anexando estadísticas del total de productos asociados
     * y productos activos para cada una.
     *
     * @param bool $soloActivas Indica si se deben retornar únicamente las categorías activas.
     * 
     * @return array Listado de categorías enriquecido con campos de estadísticas ('total_productos', 'productos_activos').
     */
    public function getCategoriasConStats($soloActivas = false)
    {
        $cache = \Config\Services::cache();
        $cacheKey = 'categorias_stats_' . ($soloActivas ? 'activas' : 'todas');

        if ($cached = $cache->get($cacheKey)) {
            return $cached;
        }

        if ($soloActivas) {
            $categorias = $this->categoriaModel->where('activo', 1)->findAll();
        } else {
            $categorias = $this->categoriaModel->findAll();
        }
        
        foreach ($categorias as &$cat) {
            $cat['total_productos'] = $this->productoModel->where('categoria_id', $cat['id_categoria'])->countAllResults();
            $cat['productos_activos'] = $this->productoModel->where('categoria_id', $cat['id_categoria'])
                                                            ->where('eliminado', 'NO')
                                                            ->countAllResults();
        }

        $cache->save($cacheKey, $categorias, 3600); // Guardar caché por 1 hora

        return $categorias;
    }

    /**
     * Procesa el registro de una nueva categoría de producto en el sistema.
     *
     * @param array $data Atributos de la categoría a insertar.
     * 
     * @return array Resumen de estado ('status' => 'success'|'error', 'message' => string).
     */
    public function crear($data)
    {
        $data['activo'] = 1;
        if ($this->categoriaModel->insert($data) === false) {
            return ['status' => 'error', 'message' => 'Errores: ' . implode(', ', $this->categoriaModel->errors())];
        }

        $cache = \Config\Services::cache();
        $cache->delete('categorias_stats_activas');
        $cache->delete('categorias_stats_todas');

        return ['status' => 'success', 'message' => 'Categoría creada con éxito.'];
    }

    /**
     * Procesa la actualización de los atributos de una categoría existente.
     *
     * @param int|string $id Identificador único de la categoría.
     * @param array $data Nuevos datos a actualizar.
     * 
     * @return array Resumen de estado ('status' => 'success'|'error', 'message' => string).
     */
    public function actualizar($id, $data)
    {
        if ($this->categoriaModel->update($id, $data) === false) {
            return ['status' => 'error', 'message' => 'Errores: ' . implode(', ', $this->categoriaModel->errors())];
        }

        $cache = \Config\Services::cache();
        $cache->delete('categorias_stats_activas');
        $cache->delete('categorias_stats_todas');

        return ['status' => 'success', 'message' => 'Categoría actualizada con éxito.'];
    }

    /**
     * Procesa la eliminación física de una categoría de la base de datos.
     * Restringe la eliminación si existen productos asociados (activos o no) para resguardar la integridad referencial.
     *
     * @param int|string $id Identificador de la categoría.
     * 
     * @return array Resumen de estado ('status' => 'success'|'error', 'message' => string).
     */
    public function eliminar($id)
    {
        $total = $this->productoModel->where('categoria_id', $id)->countAllResults();
        
        if ($total > 0) {
            return [
                'status' => 'error', 
                'message' => 'No se puede eliminar la categoría porque tiene ' . $total . ' productos asociados.'
            ];
        }

        $this->categoriaModel->delete($id);

        $cache = \Config\Services::cache();
        $cache->delete('categorias_stats_activas');
        $cache->delete('categorias_stats_todas');

        return ['status' => 'success', 'message' => 'Categoría eliminada correctamente.'];
    }

    /**
     * Alterna de forma lógica el estado activo/inactivo (activo = 1/0) de una categoría de producto.
     *
     * @param int|string $id Identificador único de la categoría.
     * 
     * @return bool|int|string Retorna el resultado del update o false si no se encuentra la categoría.
     */
    public function toggleEstado($id)
    {
        $cat = $this->categoriaModel->find($id);
        if (!$cat) {
            return false;
        }

        $nuevo_estado = ($cat['activo'] == 1) ? 0 : 1;
        $result = $this->categoriaModel->update($id, ['activo' => $nuevo_estado]);

        if ($result) {
            $cache = \Config\Services::cache();
            $cache->delete('categorias_stats_activas');
            $cache->delete('categorias_stats_todas');
        }

        return $result;
    }

    /**
     * Obtiene los datos detallados de una categoría por su identificador.
     *
     * @param int|string $id Identificador de la categoría.
     * 
     * @return array|null Datos de la categoría o null si no existe.
     */
    public function getCategoria($id)
    {
        return $this->categoriaModel->find($id);
    }
}
