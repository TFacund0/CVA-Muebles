<?php

namespace App\Controllers;

/**
 * Controlador para páginas informativas y catálogo público.
 */
class PagesController extends BaseController
{
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
        $productoService  = new \App\Services\ProductoService();
        $categoriaService = new \App\Services\CategoriaService();
        $favoritosService = new \App\Services\FavoritosService();

        return view('front/pages/productos', [
            'productos'  => $productoService->getProductosPublicos(),
            'categorias' => $categoriaService->getCategoriasConStats(true),
            'user_favs'  => session()->get('logged_in') ? $favoritosService->getFavoritosIds(session()->get('id_usuario')) : [],
            'title'      => 'Nuestros Productos'
        ]);
    }
}
