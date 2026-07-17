<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController proporciona un espacio centralizado para inicializar componentes, precargar
 * modelos, helpers y bibliotecas compartidas que son requeridos por todos los controladores
 * de la aplicación CVA Muebles.
 *
 * Todos los controladores del sistema deben extender esta clase.
 *
 * @package App\Controllers
 */
abstract class BaseController extends Controller
{
    /**
     * Instancia principal de la petición HTTP.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * Helpers que se cargan automáticamente al instanciarse la clase.
     * Estarán disponibles en todos los controladores que extiendan de BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['url', 'date', 'form'];

    /**
     * Inicializador de controladores.
     *
     * Carga el constructor padre y establece variables globales compartidas con el renderizador
     * de vistas (Arquitectura Pura) para toda la aplicación.
     *
     * @param RequestInterface  $request  Petición HTTP entrante.
     * @param ResponseInterface $response Respuesta HTTP saliente.
     * @param LoggerInterface   $logger   Librería de logs PSR-3.
     * 
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // Variables de entorno globales para todas las Vistas (Arquitectura Pura)
        $renderer = \Config\Services::renderer();
        $renderer->setVar('env_cart_enabled', env('SHOPPING_CART_ENABLED'));
        $renderer->setVar('env_whatsapp', env('WHATSAPP_NUMBER') ?? '5493794098511');
    }
}
