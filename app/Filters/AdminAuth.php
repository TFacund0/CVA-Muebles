<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AdminAuth implements FilterInterface 
{
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

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after request
    }
}
