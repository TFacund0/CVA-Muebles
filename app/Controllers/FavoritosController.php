<?php

namespace App\Controllers;

/**
 * Controlador para favoritos refactorizado para usar Capa de Servicios.
 */
class FavoritosController extends BaseController {

    protected $favoritosService;

    public function __construct() {
        $this->favoritosService = new \App\Services\FavoritosService();
    }

    /**
     * Alterna el estado de favorito de un producto (AJAX).
     *
     * @param int|string $id_producto Identificador del producto.
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function toggleFavorito($id_producto) {
        $resultado = $this->favoritosService->toggle(session()->get('id_usuario'), $id_producto);
        $resultado['csrf'] = csrf_hash();
        return $this->response->setJSON($resultado);
    }

    /**
     * Muestra la lista de favoritos del usuario.
     *
     * @return string
     */
    public function misFavoritos() {
        return view('front/pages/mis_favoritos', [
            'favoritos' => $this->favoritosService->getFavoritosConDetalle(session()->get('id_usuario')),
            'title'     => 'Mis Favoritos - CVA Muebles'
        ]);
    }
}
