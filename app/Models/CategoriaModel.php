<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para la gestión de categorías de productos.
 */
class CategoriaModel extends Model {
    protected $table = 'categorias';
    protected $primaryKey = 'id_categoria';
    protected $allowedFields = ['descripcion', 'activo'];

    protected $validationRules = [
        'descripcion' => 'required|min_length[3]|max_length[100]',
        'activo'      => 'permit_empty|numeric|max_length[2]'
    ];

    /**
     * Obtiene todas las categorías registradas.
     *
     * @return array Listado de categorías.
     */
    public function getCategorias() {
        return $this->findAll();
    }
}