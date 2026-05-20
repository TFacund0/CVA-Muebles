<?php
namespace App\Models;
use CodeIgniter\Model;

class FavoritoModel extends Model {
    protected $table = 'favoritos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['usuario_id', 'producto_id', 'fecha'];

    protected $validationRules = [
        'usuario_id'  => 'required|numeric',
        'producto_id' => 'required|numeric'
    ];

    /**
     * Verifica si un producto ya está en favoritos del usuario
     */
    public function esFavorito($usuario_id, $producto_id) {
        return $this->where(['usuario_id' => $usuario_id, 'producto_id' => $producto_id])->first() !== null;
    }

    /**
     * Obtiene los productos favoritos de un usuario con sus detalles
     */
    public function getFavoritosByUser($usuario_id) {
        return $this->select('favoritos.*, productos.nombre_prod, productos.imagen, productos.precio_vta, productos.descripcion, productos.categoria_id, categorias.descripcion as categoria')
                    ->join('productos', 'productos.id_producto = favoritos.producto_id')
                    ->join('categorias', 'categorias.id_categoria = productos.categoria_id')
                    ->where('productos.eliminado', 'NO')
                    ->where('categorias.activo', 1)
                    ->where('usuario_id', $usuario_id)
                    ->findAll();
    }
}
