<?php

namespace App\Services;

use App\Models\FavoritoModel;

/**
 * Servicio para manejar la lógica de favoritos.
 */
class FavoritosService
{
    protected $favoritosModel;

    public function __construct()
    {
        $this->favoritosModel = new FavoritoModel();
    }

    /**
     * Alterna un producto como favorito para un usuario.
     */
    public function toggle($usuario_id, $producto_id)
    {
        $existe = $this->favoritosModel->where('usuario_id', $usuario_id)
                                       ->where('producto_id', $producto_id)
                                       ->first();

        if ($existe) {
            $this->favoritosModel->delete($existe['id']);
            return ['status' => 'removed', 'message' => 'Eliminado de favoritos'];
        } else {
            $this->favoritosModel->insert([
                'usuario_id'  => $usuario_id,
                'producto_id' => $producto_id,
                'fecha'       => date('Y-m-d H:i:s')
            ]);
            return ['status' => 'added', 'message' => 'Agregado a favoritos'];
        }
    }

    /**
     * Obtiene los IDs de productos favoritos de un usuario.
     */
    public function getFavoritosIds($usuario_id)
    {
        $favs = $this->favoritosModel->where('usuario_id', $usuario_id)->findAll();
        return array_column($favs, 'producto_id');
    }

    /**
     * Obtiene los productos favoritos con sus detalles.
     */
    public function getFavoritosConDetalle($usuario_id)
    {
        return $this->favoritosModel->getFavoritosByUser($usuario_id);
    }
}
