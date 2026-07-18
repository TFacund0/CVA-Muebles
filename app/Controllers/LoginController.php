<?php

namespace App\Controllers;

/**
 * Controlador para login refactorizado para usar Capa de Servicios.
 */
class LoginController extends BaseController {
    
    protected $usuarioService;

    public function __construct() {
        $this->usuarioService = new \App\Services\UsuarioService();
    }

    /**
     * Muestra la vista del formulario de login.
     */
    public function create() {
        if (session()->get('logged_in')) return redirect()->to('/');
        return view('front/pages/login', ['title' => 'Login']);
    }

    /**
     * Autentica al usuario delegando al servicio con protección de Throttling.
     */
    public function auth() {
        $throttler = \Config\Services::throttler();

        // Limitar a 5 intentos por minuto por cada IP
        if ($throttler->check(md5($this->request->getIPAddress()), 5, MINUTE) === false) {
            return redirect()->back()->withInput()->with('error', 'Demasiados intentos. Por favor, espera un minuto.');
        }

        $resultado = $this->usuarioService->autenticar(
            $this->request->getVar('email'),
            $this->request->getVar('pass')
        );

        if($resultado['status'] === 'success') {
            session()->regenerate();
            session()->set($resultado['data']);
            return redirect()->to('/')->with('success', '¡Bienvenido de nuevo!');
        } else {
            return redirect()->back()->withInput()->with('error', $resultado['message']);
        }
    }

    /**
     * Destruye la sesión.
     */
    public function logout() {
        session()->destroy();
        return redirect()->to('/');
    }
}
