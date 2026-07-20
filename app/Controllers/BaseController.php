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
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['url', 'date', 'form'];

    /**
     * Inicializa el controlador base: carga configuraciones del framework y
     * expone variables de entorno globales a todas las vistas.
     *
     * @param RequestInterface  $request  Objeto de la petición actual.
     * @param ResponseInterface $response Objeto de la respuesta actual.
     * @param LoggerInterface   $logger   Instancia del logger de la aplicación.
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

    /**
     * Indica si el usuario logueado tiene perfil de administrador (perfil_id == 1).
     *
     * @return bool
     */
    protected function isAdmin(): bool
    {
        return session()->get('logged_in') && session()->get('perfil_id') == 1;
    }

    /**
     * Regla de validación estándar para un campo de imagen subida
     * (jpg/jpeg/png/webp, tamaño máximo en KB). Evita repetir la misma
     * cadena de reglas is_image|mime_in|max_size en cada controlador
     * que procesa una subida de imagen.
     *
     * @param string $field Nombre del campo de formulario que contiene la imagen.
     * @param int    $maxKb Tamaño máximo permitido en kilobytes.
     * @return string
     */
    protected function imageValidationRule(string $field, int $maxKb = 2048): string
    {
        return "is_image[{$field}]|mime_in[{$field},image/jpg,image/jpeg,image/png,image/webp]|max_size[{$field},{$maxKb}]";
    }
}
