<?php

namespace App\Services;

use App\Models\FavoritoModel;

/**
 * Class FavoritosService
 *
 * Servicio encargado de gestionar la lista de deseos ("Favoritos") de los usuarios,
 * permitiendo alternar (toggle) de forma asíncrona o tradicional la asociación entre clientes
 * y productos, y recuperar listados detallados o simplificados para la interfaz web.
 *
 * @package App\Services
 */
class FavoritosService
{
    /**
     * @var FavoritoModel Modelo para interactuar con la tabla de favoritos en la base de datos.
     */
    protected $favoritosModel;

    /**
     * Constructor del servicio.
     *
     * Inicializa la instancia del modelo de acceso a datos para favoritos.
     */
    public function __construct()
    {
        $this->favoritosModel = new FavoritoModel();
    }

    /**
     * Alterna la marca de favorito para un producto y usuario específicos.
     * Si la relación ya existe, la elimina; de lo contrario, registra una nueva marca con
     * la marca de tiempo actual del servidor.
     *
     * @param int|string $usuario_id Identificador único del usuario.
     * @param int|string $producto_id Identificador único del producto.
     * 
     * @return array Resumen de la acción realizada ('status' => 'added'|'removed', 'message' => string).
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
     * Recupera de forma simplificada los identificadores de productos que un usuario
     * específico ha marcado como favoritos. Útil para verificar estados en las vistas.
     *
     * @param int|string $usuario_id Identificador único del usuario.
     * 
     * @return array Lista de identificadores numéricos de productos marcados como favoritos.
     */
    public function getFavoritosIds($usuario_id)
    {
        $favs = $this->favoritosModel->where('usuario_id', $usuario_id)->findAll();
        return array_column($favs, 'producto_id');
    }

    /**
     * Recupera el listado completo de productos marcados como favoritos por el usuario,
     * incluyendo detalles descriptivos de los productos (nombre, precio, imágenes, etc.).
     *
     * @param int|string $usuario_id Identificador del usuario.
     * 
     * @return array Colección detallada de productos favoritos del usuario.
     */
    public function getFavoritosConDetalle($usuario_id)
    {
        return $this->favoritosModel->getFavoritosByUser($usuario_id);
    }
}
