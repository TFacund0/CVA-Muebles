<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Class CategoriaModel
 *
 * Modelo que interactúa con la tabla 'categorias' de la base de datos de CVA Muebles.
 * Permite gestionar las clasificaciones de los productos en catálogo.
 *
 * @property int|string $id_categoria Identificador único de la categoría.
 * @property string $descripcion Nombre o descripción corta de la categoría.
 * @property int|string $activo Estado de habilitación (1 = activo, 0 = inactivo).
 * 
 * @package App\Models
 */
class CategoriaModel extends Model
{
    /**
     * @var string Nombre de la tabla en la base de datos.
     */
    protected $table = 'categorias';

    /**
     * @var string Nombre de la columna que actúa como clave primaria de la tabla.
     */
    protected $primaryKey = 'id_categoria';

    /**
     * @var array Campos de la tabla que están permitidos para su asignación e inserción masiva.
     */
    protected $allowedFields = ['descripcion', 'activo'];
    
    /**
     * @var array Reglas de validación interna que aplica el modelo de forma automática antes de guardar.
     */
    protected $validationRules = [
        'descripcion' => 'required|min_length[3]|max_length[100]',
        'activo'      => 'permit_empty|numeric|max_length[2]'
    ];

    /**
     * Recupera el listado completo de categorías existentes en la base de datos.
     *
     * @return array Listado de categorías.
     */
    public function getCategorias()
    {
        return $this->findAll();
    }
}