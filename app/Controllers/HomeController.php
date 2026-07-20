<?php

namespace App\Controllers;

use App\Services\ProductoService;

/**
 * Controlador de la página de inicio del sitio.
 */
class HomeController extends BaseController
{
    protected $productoService;

    public function __construct()
    {
        $this->productoService = new ProductoService();
    }

    /**
     * Muestra la página de inicio con una selección aleatoria de productos destacados.
     *
     * @return string
     */
    public function index()
    {
        $productos = $this->productoService->getProductosPublicos();
        shuffle($productos);

        return view('front/home/plantilla', [
            'title' => 'CVA Muebles',
            'destacados' => array_slice($productos, 0, 8)
        ]);
    }
}
