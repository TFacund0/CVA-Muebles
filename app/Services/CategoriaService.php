<?php

namespace App\Services;

use App\Models\CategoriaModel;
use App\Models\ProductoModel;

/**
 * Servicio para manejar la lógica de negocio de las categorías.
 */
class CategoriaService
{
    protected $categoriaModel;
    protected $productoModel;

    public function __construct(?CategoriaModel $categoriaModel = null, ?ProductoModel $productoModel = null)
    {
        $this->categoriaModel = $categoriaModel ?? new CategoriaModel();
        $this->productoModel = $productoModel ?? new ProductoModel();
    }

    /**
     * Obtiene todas las categorías con estadísticas de uso.
     */
    public function getCategoriasConStats($soloActivas = false)
    {
        if ($soloActivas) {
            $categorias = $this->categoriaModel->where('activo', 1)->findAll();
        } else {
            $categorias = $this->categoriaModel->findAll();
        }
        
        foreach ($categorias as &$cat) {
            $cat['total_productos'] = $this->productoModel->countByCategoriaConArchivados($cat['id_categoria']);
            $cat['productos_activos'] = $this->productoModel->countByCategoria($cat['id_categoria']);
        }

        return $categorias;
    }

    /**
     * Crea una nueva categoría.
     */
    public function crear($data)
    {
        $data['activo'] = 1;
        if ($this->categoriaModel->insert($data) === false) {
            return ['status' => 'error', 'message' => 'Errores: ' . implode(', ', $this->categoriaModel->errors())];
        }
        return ['status' => 'success', 'message' => 'Categoría creada con éxito.'];
    }

    /**
     * Actualiza una categoría.
     */
    public function actualizar($id, $data)
    {
        if ($this->categoriaModel->update($id, $data) === false) {
            return ['status' => 'error', 'message' => 'Errores: ' . implode(', ', $this->categoriaModel->errors())];
        }
        return ['status' => 'success', 'message' => 'Categoría actualizada con éxito.'];
    }

    /**
     * Elimina una categoría si no tiene productos asociados.
     * Si los tiene, lanza una excepción o devuelve un error.
     */
    public function eliminar($id)
    {
        $total = $this->productoModel->countByCategoriaConArchivados($id);
        
        if ($total > 0) {
            return [
                'status' => 'error', 
                'message' => 'No se puede eliminar la categoría porque tiene ' . $total . ' productos asociados.'
            ];
        }

        $this->categoriaModel->delete($id);
        return ['status' => 'success', 'message' => 'Categoría eliminada correctamente.'];
    }

    /**
     * Alterna el estado activo/inactivo.
     */
    public function toggleEstado($id)
    {
        $cat = $this->categoriaModel->find($id);
        if (!$cat) return false;

        $nuevo_estado = ($cat['activo'] == 1) ? 0 : 1;
        return $this->categoriaModel->update($id, ['activo' => $nuevo_estado]);
    }

    /**
     * Obtiene una categoría por ID.
     */
    public function getCategoria($id)
    {
        return $this->categoriaModel->find($id);
    }
}
