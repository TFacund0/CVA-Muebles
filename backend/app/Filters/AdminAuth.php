<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * Clase AdminAuth
 *
 * Filtro de seguridad (Middleware) para la capa de administración de CVA Muebles.
 * Se encarga de restringir el acceso a rutas protegidas verificando activamente
 * que el usuario cuente con una sesión iniciada y que posea el rol de administrador
 * (representado por perfil_id == 1).
 *
 * Soporta respuestas híbridas:
 * - Para peticiones web normales, realiza redirecciones HTTP con mensajes flash.
 * - Para peticiones asíncronas (AJAX), retorna JSON estructurado con códigos 401/403.
 *
 * @package App\Filters
 */
class AdminAuth implements FilterInterface 
{
    /**
     * Ejecuta la validación de seguridad antes de procesar la solicitud del controlador.
     *
     * Valida la existencia de la sesión y verifica el ID de perfil del usuario.
     *
     * @param RequestInterface $request   Instancia de la petición HTTP actual.
     * @param array|null       $arguments Argumentos adicionales opcionales pasados al filtro.
     *
     * @return ResponseInterface|string|void Retorna una redirección o respuesta JSON en caso de fallo; nada si tiene éxito.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            if ($request->isAJAX()) {
                return service('response')->setJSON(['status' => 'error', 'message' => 'Debes iniciar sesión.'])->setStatusCode(401);
            }
            return redirect()->to('/login')
                ->with('error', 'Por favor inicia sesión para acceder a esta página');
        }

        // Verifica que el usuario tenga perfil de administrador (perfil_id == 1).
        // Si está logueado pero no es admin, se redirige al inicio (no al login,
        // porque la sesión sigue activa — esto sería un 403, no un 401).
        if (session()->get('perfil_id') != 1) {
            if ($request->isAJAX()) {
                return service('response')->setJSON(['status' => 'error', 'message' => 'No tienes permisos.'])->setStatusCode(403);
            }
            return redirect()->to('/')
                ->with('error', 'No tienes permisos para acceder a esta sección.');
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
        // No action needed after request
    }
}
