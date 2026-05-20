<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Auth implements FilterInterface 
{
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

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Método requerido pero no necesitamos hacer nada después de la solicitud
    }
}