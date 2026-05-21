<?php

namespace App\Controllers;

use App\Services\FavoritosService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class FavoritosController
 *
 * Controlador encargado de la gestión de la lista de deseos ("Favoritos") de los usuarios.
 * Permite agregar/quitar productos de favoritos de manera asíncrona (AJAX) y
 * visualizar los favoritos del usuario autenticado.
 * Delega la lógica de negocio a la capa de servicios (`FavoritosService`).
 *
 * @package App\Controllers
 */
class FavoritosController extends BaseController 
{
    /**
     * @var FavoritosService Servicio para procesar la lógica de negocio de los favoritos.
     */
    protected $favoritosService;

    /**
     * Constructor del controlador.
     *
     * Inicializa la capa de servicios de favoritos.
     */
    public function __construct() 
    {
        $this->favoritosService = new FavoritosService();
    }

    /**
     * Alterna (agrega o quita) el estado de favorito de un producto específico para el usuario activo.
     *
     * Diseñado exclusivamente para ser consumido de forma asíncrona mediante peticiones AJAX,
     * retornando un objeto JSON estructurado y regenerando el token CSRF para robustecer la seguridad.
     *
     * @param int|string $id_producto Identificador único del producto a alternar.
     * 
     * @return ResponseInterface Respuesta JSON con el estado de la operación y el nuevo hash CSRF.
     */
    public function toggleFavorito($id_producto) 
    {
        $resultado = $this->favoritosService->toggle(session()->get('id_usuario'), $id_producto);
        $resultado['csrf'] = csrf_hash();
        return $this->response->setJSON($resultado);
    }

    /**
     * Muestra la interfaz del listado de favoritos del usuario actualmente autenticado.
     *
     * @return string|ResponseInterface Contenido HTML de la vista de mis favoritos.
     */
    public function misFavoritos() 
    {
        return view('front/pages/mis_favoritos', [
            'favoritos' => $this->favoritosService->getFavoritosConDetalle(session()->get('id_usuario')),
            'title'     => 'Mis Favoritos - CVA Muebles'
        ]);
    }
}
