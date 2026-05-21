<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * Clase Auth
 *
 * Filtro de autenticación (Middleware) para la capa de clientes en CVA Muebles.
 * Se encarga de restringir el acceso a rutas protegidas verificando activamente
 * que el usuario cuente con una sesión activa e iniciada en el sistema.
 *
 * Soporta respuestas híbridas:
 * - Para peticiones web normales, realiza redirecciones HTTP con mensajes flash.
 * - Para peticiones asíncronas (AJAX), retorna JSON estructurado con código 401.
 *
 * @package App\Filters
 */
class Auth implements FilterInterface 
{
    /**
     * Ejecuta la validación de autenticación antes de procesar la solicitud del controlador.
     *
     * Valida la existencia de una sesión de usuario activa.
     *
     * @param RequestInterface $request   Instancia de la petición HTTP actual.
     * @param array|null       $arguments Argumentos adicionales opcionales pasados al filtro.
     *
     * @return ResponseInterface|string|void Retorna una redirección o respuesta JSON en caso de fallo; nada si tiene éxito.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Si el usuario no está logueado
        if (!session()->get('logged_in')) {
            if ($request->isAJAX()) {
                return service('response')->setJSON(['status' => 'error', 'message' => 'Debes iniciar sesión para realizar esta acción.'])->setStatusCode(401);
            }
            // Redirecciona a la página de login
            return redirect()->to('/login')
                ->with('error', 'Por favor inicia sesión para acceder a esta página');
        }
    }

    /**
     * Ejecuta acciones después de que el controlador ha procesado la solicitud.
     *
     * No se requiere ninguna acción post-solicitud en este filtro.
     *
     * @param RequestInterface  $request   Instancia de la petición HTTP actual.
     * @param ResponseInterface $response  Instancia de la respuesta HTTP saliente.
     * @param array|null        $arguments Argumentos adicionales opcionales pasados al filtro.
     *
     * @return void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Método requerido pero no necesitamos hacer nada después de la solicitud
    }
}