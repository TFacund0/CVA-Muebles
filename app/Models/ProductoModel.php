<?php

namespace App\Models;
use CodeIgniter\Model;

class ProductoModel extends Model 
{
    protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    protected $allowedFields = ['nombre_prod', 'imagen', 'categoria_id', 'precio', 'precio_vta', 'stock', 'stock_min', 'eliminado', 'descripcion'];

    protected $validationRules = [
        'nombre_prod'  => 'required|min_length[3]|max_length[100]',
        'categoria_id' => 'required|numeric',
        'precio'       => 'required|numeric|greater_than_equal_to[0]',
        'precio_vta'   => 'required|numeric|greater_than_equal_to[0]',
        'stock'        => 'required|numeric|greater_than_equal_to[0]'
    ];

    public function getProductoAll() {
        return $this->select('productos.*, categorias.descripcion as categoria')
                    ->join('categorias', 'categorias.id_categoria = productos.categoria_id')
                    ->findAll();
    }

    public function getProductosPublicos() {
        return $this->select('productos.*, categorias.descripcion as categoria')
                    ->join('categorias', 'categorias.id_categoria = productos.categoria_id')
                    ->where('productos.eliminado', 'NO')
                    ->where('categorias.activo', 1)
                    ->findAll();
    }

    public function getBuilderProductos() {
        return $this->select('productos.*, categorias.descripcion as categoria, categorias.activo as categoria_activa')
                    ->join('categorias', 'categorias.id_categoria = productos.categoria_id');
    }

    public function getProducto($id = null) {
        $builder = $this->getBuilderProductos();
        $builder->where('productos.id_producto', $id);
        $query = $builder->get();

        return $query->getRowArray();
    }

    public function updateStock($id = null, $stock = null) {
        $builder = $this->getBuilderProductos();
        $builder->where('productos.id_producto', $id);
        $builder->set('productos.stock', $stock);
        
        $builder->update();
    }
}