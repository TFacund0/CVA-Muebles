<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Class ProductoModel
 *
 * Modelo que interactúa con la tabla 'productos' de la base de datos de CVA Muebles.
 * Centraliza la administración de los muebles y productos del catálogo general del taller.
 *
 * @property int|string $id_producto Identificador único del mueble/producto.
 * @property string $nombre_prod Nombre comercial del mueble.
 * @property string $imagen Nombre del archivo de la imagen principal en el servidor.
 * @property int|string $categoria_id Identificador de la categoría a la que pertenece.
 * @property float|string $precio Costo base o de fabricación del producto.
 * @property float|string $precio_vta Precio de venta al público en general.
 * @property int|string $stock Cantidad física disponible del producto en inventario.
 * @property int|string $stock_min Cantidad mínima requerida del producto para emitir alertas de reabastecimiento.
 * @property string $eliminado Estado de archivado o borrado lógico ('SI' = archivado/oculto, 'NO' = visible).
 * @property string $descripcion Texto explicativo de las características constructivas del mueble.
 * 
 * @package App\Models
 */
class ProductoModel extends Model 
{
    /**
     * @var string Nombre de la tabla en la base de datos.
     */
    protected $table = 'productos';

    /**
     * @var string Nombre de la columna que actúa como clave primaria de la tabla.
     */
    protected $primaryKey = 'id_producto';

    /**
     * @var array Campos de la tabla que están permitidos para su asignación e inserción masiva.
     */
    protected $allowedFields = ['nombre_prod', 'imagen', 'categoria_id', 'precio', 'precio_vta', 'stock', 'stock_min', 'eliminado', 'descripcion'];

    /**
     * @var array Reglas de validación interna que aplica el modelo de forma automática antes de guardar.
     */
    protected $validationRules = [
        'nombre_prod'  => 'required|min_length[3]|max_length[100]',
        'categoria_id' => 'required|numeric',
        'precio'       => 'required|numeric|greater_than_equal_to[0]',
        'precio_vta'   => 'required|numeric|greater_than_equal_to[0]',
        'stock'        => 'required|numeric|greater_than_equal_to[0]'
    ];

    /**
     * Recupera el listado completo de todos los productos de la base de datos,
     * incorporando la descripción corta de su categoría asociada mediante un join.
     *
     * @return array Listado de productos totales.
     */
    public function getProductoAll()
    {
        return $this->select('productos.*, categorias.descripcion as categoria')
                    ->join('categorias', 'categorias.id_categoria = productos.categoria_id')
                    ->findAll();
    }

    /**
     * Recupera la lista de productos destinados a la exposición pública (catálogo frontal).
     * Excluye productos marcados como archivados/eliminados lógicamente o asociados a categorías inactivas.
     *
     * @return array Listado de productos aptos para exposición.
     */
    public function getProductosPublicos()
    {
        return $this->select('productos.*, categorias.descripcion as categoria')
                    ->join('categorias', 'categorias.id_categoria = productos.categoria_id')
                    ->where('productos.eliminado', 'NO')
                    ->where('categorias.activo', 1)
                    ->findAll();
    }

    /**
     * Genera y retorna una consulta base de Query Builder pre-configurada para consultar productos
     * cruzando datos con sus respectivas categorías y el estado de habilitación de estas.
     *
     * @return \CodeIgniter\Database\BaseBuilder Instancia del Query Builder para encadenar condiciones.
     */
    public function getBuilderProductos()
    {
        return $this->select('productos.*, categorias.descripcion as categoria, categorias.activo as categoria_activa')
                    ->join('categorias', 'categorias.id_categoria = productos.categoria_id');
    }

    /**
     * Recupera los detalles completos de un producto a partir de su identificador único.
     *
     * @param int|string|null $id Identificador del producto.
     * 
     * @return array|null Estructura del producto mapeada en un arreglo asociativo o null si no se encuentra.
     */
    public function getProducto($id = null)
    {
        $builder = $this->getBuilderProductos();
        $builder->where('productos.id_producto', $id);
        $query = $builder->get();

        return $query->getRowArray();
    }
}