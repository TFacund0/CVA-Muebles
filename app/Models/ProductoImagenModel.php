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
     *
     * @param int $producto_id ID del producto.
     * @return array Listado de imágenes secundarias del producto, ordenadas por su campo orden.
     */
    public function getImagenesPorProducto($producto_id)
    {
        return $this->where('producto_id', $producto_id)
                    ->orderBy('orden', 'ASC')
                    ->findAll();
    }
}
