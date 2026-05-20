<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriaModel extends Model {
    protected $table = 'categorias';
    protected $primaryKey = 'id_categoria';
    protected $allowedFields = ['descripcion', 'activo'];
    
    protected $validationRules = [
        'descripcion' => 'required|min_length[3]|max_length[100]',
        'activo'      => 'permit_empty|numeric|max_length[2]'
    ];

    public function getCategorias() {
        return $this->findAll();
    }
}