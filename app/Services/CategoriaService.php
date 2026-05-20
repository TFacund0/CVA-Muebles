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

    public function __construct()
    {
        $this->categoriaModel = new CategoriaModel();
        $this->productoModel = new ProductoModel();
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
            $cat['total_productos'] = $this->productoModel->where('categoria_id', $cat['id_categoria'])->countAllResults();
            $cat['productos_activos'] = $this->productoModel->where('categoria_id', $cat['id_categoria'])
                                                            ->where('eliminado', 'NO')
                                                            ->countAllResults();
        }

        return $categorias;
    }

    /**
     * Crea una nueva categoría.
     */
    public function crear($data)
    {
        $data['activo'] = 1;
        return $this->categoriaModel->insert($data);
    }

    /**
     * Actualiza una categoría.
     */
    public function actualizar($id, $data)
    {
        return $this->categoriaModel->update($id, $data);
    }

    /**
     * Elimina una categoría si no tiene productos asociados.
     * Si los tiene, lanza una excepción o devuelve un error.
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
        return $this->categoriaModel->builder()
                                    ->where('id_categoria', $id)
                                    ->set('activo', $nuevo_estado)
                                    ->update();
    }

    /**
     * Obtiene una categoría por ID.
     */
    public function getCategoria($id)
    {
        return $this->categoriaModel->find($id);
    }
}
