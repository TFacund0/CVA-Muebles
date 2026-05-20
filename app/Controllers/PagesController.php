<?php

namespace App\Controllers;

/**
 * Controlador para páginas informativas y catálogo público.
 */
class PagesController extends BaseController
{
    protected $productoService;
    protected $favoritosService;
    protected $categoriaService;

    public function __construct() {
        $this->productoService = new \App\Services\ProductoService();
        $this->favoritosService = new \App\Services\FavoritosService();
        $this->categoriaService = new \App\Services\CategoriaService();
    }

    public function quienesSomos() {
        return view('front/pages/quienesSomos', ['title' => 'Quiénes Somos']);
    }

    public function comercializacion() {   
        return view('front/pages/comercializacion', ['title' => 'Comercialización']);
    }

    public function informacionContacto() {
        return view('front/pages/informacionContacto', ['title' => 'Contacto']);
    }

    public function terminosYCondiciones() {
        return view('front/pages/terminosYCondiciones', ['title' => 'Términos y Condiciones']);
    }

    public function beneficios() {
        return view('front/pages/beneficios', ['title' => 'Programa de Fidelidad']);
    }

    /**
     * Muestra el catálogo de productos delegando al servicio.
     */
    public function productos() {
        return view('front/pages/productos', [
            'productos'  => $this->productoService->getProductosPublicos(),
            'categorias' => $this->categoriaService->getCategoriasConStats(true),
            'user_favs'  => session()->get('logged_in') ? $this->favoritosService->getFavoritosIds(session()->get('id_usuario')) : [],
            'title'      => 'Nuestros Productos'
        ]);
    }
}
