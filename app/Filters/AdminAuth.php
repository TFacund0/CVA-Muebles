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

        // Check if user has admin profile (perfil_id == 1)
        if (session()->get('perfil_id') != 1) {
            if ($request->isAJAX()) {
                return service('response')->setJSON(['status' => 'error', 'message' => 'No tienes permisos.'])->setStatusCode(403);
            }
            return redirect()->to('/login') // or back() or to('/') depending on desired UX
                ->with('error', 'No tienes permisos para acceder a esta sección.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after request
    }
}
