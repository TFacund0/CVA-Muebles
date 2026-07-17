<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Class FavoritoModel
 *
 * Modelo que interactúa con la tabla 'favoritos' de la base de datos de CVA Muebles.
 * Representa la relación muchos a muchos entre usuarios y sus productos preferidos (Lista de Deseos).
 *
 * @property int|string $id Identificador único del registro de favorito.
 * @property int|string $usuario_id Identificador del usuario que marca el favorito.
 * @property int|string $producto_id Identificador del producto marcado como favorito.
 * @property string $fecha Marca de tiempo del registro del favorito.
 * 
 * @package App\Models
 */
class FavoritoModel extends Model
{
    /**
     * @var string Nombre de la tabla en la base de datos.
     */
    protected $table = 'favoritos';

    /**
     * @var string Nombre de la columna que actúa como clave primaria de la tabla.
     */
    protected $primaryKey = 'id';

    /**
     * @var array Campos de la tabla que están permitidos para su asignación e inserción masiva.
     */
    protected $allowedFields = ['usuario_id', 'producto_id', 'fecha'];

    /**
     * @var array Reglas de validación interna que aplica el modelo de forma automática antes de guardar.
     */
    protected $validationRules = [
        'usuario_id'  => 'required|numeric',
        'producto_id' => 'required|numeric'
    ];

    /**
     * Verifica si un producto específico se encuentra en la lista de favoritos de un usuario.
     *
     * @param int|string $usuario_id Identificador del usuario.
     * @param int|string $producto_id Identificador del producto.
     * 
     * @return bool True si el producto ya está marcado como favorito, false en caso contrario.
     */
    public function esFavorito($usuario_id, $producto_id)
    {
        return $this->where(['usuario_id' => $usuario_id, 'producto_id' => $producto_id])->first() !== null;
    }

    /**
     * Recupera la colección detallada de productos favoritos del usuario,
     * incorporando detalles descriptivos de los muebles y su categoría asociada.
     * Excluye productos archivados o asociados a categorías inactivas.
     *
     * @param int|string $usuario_id Identificador del usuario.
     * 
     * @return array Listado de productos favoritos detallado.
     */
    public function getFavoritosByUser($usuario_id)
    {
        return $this->select('favoritos.*, productos.nombre_prod, productos.imagen, productos.precio_vta, productos.descripcion, productos.categoria_id, categorias.descripcion as categoria')
                    ->join('productos', 'productos.id_producto = favoritos.producto_id')
                    ->join('categorias', 'categorias.id_categoria = productos.categoria_id')
                    ->where('productos.eliminado', 'NO')
                    ->where('categorias.activo', 1)
                    ->where('usuario_id', $usuario_id)
                    ->findAll();
    }
}
