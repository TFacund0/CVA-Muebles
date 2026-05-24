<?php

namespace App\Controllers;

use App\Services\CategoriaService;
use App\Services\FavoritosService;
use App\Services\ProductoService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class PagesController
 *
 * Controlador encargado de gestionar las rutas públicas del sitio web, incluyendo páginas informativas
 * (Quiénes Somos, Comercialización, Contacto, Términos y Condiciones, Programa de Fidelidad)
 * y la renderización del Catálogo Público de Productos interactivo.
 *
 * @package App\Controllers
 */
class PagesController extends BaseController
{
    /**
     * Muestra la interfaz informativa sobre la empresa "Quiénes Somos".
     *
     * @return string Contenido HTML de la vista correspondiente.
     */
    public function quienesSomos() 
    {
        $this->cachePage(600);
        return view('front/pages/quienesSomos', ['title' => 'Quiénes Somos']);
    }

    /**
     * Muestra la información sobre envíos, medios de pago y políticas de Comercialización.
     *
     * @return string Contenido HTML de la vista correspondiente.
     */
    public function comercializacion() 
    {   
        $this->cachePage(600);
        return view('front/pages/comercializacion', ['title' => 'Comercialización']);
    }

    /**
     * Muestra la información general de contacto, mapa y canales de atención de CVA Muebles.
     *
     * @return string Contenido HTML de la vista correspondiente.
     */
    public function informacionContacto() 
    {
        return view('front/pages/informacionContacto', ['title' => 'Contacto']);
    }

    /**
     * Muestra los Términos y Condiciones Legales del uso del sitio y contratación de servicios.
     *
     * @return string Contenido HTML de la vista correspondiente.
     */
    public function terminosYCondiciones() 
    {
        $this->cachePage(600);
        return view('front/pages/terminosYCondiciones', ['title' => 'Términos y Condiciones']);
    }

    /**
     * Muestra información de los Beneficios y Programa de Fidelidad para clientes habituales.
     *
     * @return string Contenido HTML de la vista correspondiente.
     */
    public function beneficios() 
    {
        $this->cachePage(600);
        return view('front/pages/beneficios', ['title' => 'Programa de Fidelidad']);
    }

    /**
     * Muestra el catálogo de productos interactivo para el público general.
     *
     * Carga todos los productos en estado activo, las categorías activas que tienen
     * productos asociados, y verifica qué productos tiene marcados como favoritos el
     * usuario actual en caso de estar autenticado.
     *
     * @return string|ResponseInterface Contenido HTML de la vista del catálogo de productos.
     */
    public function productos() 
    {
        // Optimización de concurrencia: Libera la sesión tempranamente para evitar cuellos de botella en peticiones asíncronas.
        session_write_close();
        
        $productoService  = new ProductoService();
        $categoriaService = new CategoriaService();
        $favoritosService = new FavoritosService();
        
        $categoriaParam = $this->request->getGet('categoria');
        $filterMode = env('app.filterMode', 'client');
        
        // Si el modo es servidor, usamos el parámetro para filtrar en BD. 
        // Si es cliente, traemos todos para que JS filtre localmente.
        $categoriaFiltro = ($filterMode === 'server') ? $categoriaParam : null;

        return view('front/pages/productos', [
            'productos'  => $productoService->getProductosPublicos($categoriaFiltro),
            'categorias' => $categoriaService->getCategoriasConStats(true),
            'user_favs'  => session()->get('logged_in') ? $favoritosService->getFavoritosIds(session()->get('id_usuario')) : [],
            'title'      => 'Nuestros Productos',
            'filterMode' => $filterMode,
            'categoriaActiva' => $categoriaParam ?? 'todos'
        ]);
    }
}
