<?php

namespace App\Controllers;

/**
 * Class HomeController
 *
 * Controlador básico encargado de renderizar la página de inicio o "Landing Page" de CVA Muebles.
 *
 * @package App\Controllers
 */
class HomeController extends BaseController
{
    /**
     * Muestra la pantalla principal (home) del sitio web.
     *
     * @return string Contenido HTML renderizado de la plantilla de inicio.
     */
    public function index()
    {   
        return view('front/home/plantilla', [
            'title' => 'CVA Muebles'
        ]);
    }
}
