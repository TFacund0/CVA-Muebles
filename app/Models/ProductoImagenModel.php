<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductoImagenModel extends Model
{
    protected $table      = 'producto_imagenes';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['producto_id', 'imagen', 'orden'];

    /**
     * Obtiene las imágenes secundarias de un producto.
     */
    public function getImagenesPorProducto($producto_id)
    {
        return $this->where('producto_id', $producto_id)
                    ->orderBy('orden', 'ASC')
                    ->findAll();
    }
}
