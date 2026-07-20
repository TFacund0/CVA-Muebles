<?php

namespace App\Models;
use CodeIgniter\Model;

class ProductoModel extends Model 
{
    protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = ['nombre_prod', 'imagen', 'categoria_id', 'precio', 'precio_vta', 'stock', 'stock_min', 'descripcion'];

    protected $validationRules = [
        'nombre_prod'  => 'required|min_length[3]|max_length[100]',
        'categoria_id' => 'required|numeric',
        'precio'       => 'required|numeric|greater_than_equal_to[0]',
        'precio_vta'   => 'required|numeric|greater_than_equal_to[0]',
        'stock'        => 'required|numeric|greater_than_equal_to[0]'
    ];

    /**
     * Obtiene todos los productos (incluidos los archivados) con su categoría.
     *
     * @return array Listado de productos con los campos de su categoría asociada.
     */
    public function getProductoAll() {
        return $this->getBuilderProductos()->findAll();
    }

    /**
     * Obtiene los productos visibles al público (solo de categorías activas).
     *
     * @return array Listado de productos con la descripción de su categoría.
     */
    public function getProductosPublicos() {
        return $this->select('productos.*, categorias.descripcion as categoria')
                    ->join('categorias', 'categorias.id_categoria = productos.categoria_id')
                    ->where('categorias.activo', 1)
                    ->findAll();
    }

    /**
     * Arma el query builder base de productos unidos con su categoría (incluye eliminados).
     *
     * @return \CodeIgniter\Database\BaseBuilder Builder configurado con el join y withDeleted().
     */
    public function getBuilderProductos() {
        return $this->select('productos.*, categorias.descripcion as categoria, categorias.activo as categoria_activa')
                    ->join('categorias', 'categorias.id_categoria = productos.categoria_id')
                    ->withDeleted();
    }

    /**
     * Obtiene un producto puntual por su ID, junto con los datos de su categoría.
     *
     * @param int|null $id ID del producto a buscar.
     * @return array|null Datos del producto como arreglo asociativo, o null si no existe.
     */
    public function getProducto($id = null) {
        $builder = $this->getBuilderProductos();
        $builder->where('productos.id_producto', $id);
        $query = $builder->get();

        return $query->getRowArray();
    }

    /**
     * Cuenta los productos activos (no archivados) de una categoría.
     *
     * @param int $categoriaId ID de la categoría.
     * @return int Cantidad de productos activos en la categoría.
     */
    public function countByCategoria($categoriaId): int {
        return $this->where('categoria_id', $categoriaId)->countAllResults();
    }

    /**
     * Cuenta todos los productos de una categoría, incluidos los archivados.
     *
     * @param int $categoriaId ID de la categoría.
     * @return int Cantidad total de productos (activos y archivados) en la categoría.
     */
    public function countByCategoriaConArchivados($categoriaId): int {
        return $this->where('categoria_id', $categoriaId)->withDeleted()->countAllResults();
    }
}