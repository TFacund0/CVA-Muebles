<?php

namespace App\Controllers;

use App\Services\UsuarioService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class LoginController
 *
 * Controlador encargado de la autenticación de usuarios y gestión de sesiones activas en CVA Muebles.
 * Implementa protección de Throttling en los accesos para mitigar ataques de fuerza bruta.
 * Delega la validación de credenciales a la capa de servicios (`UsuarioService`).
 *
 * @package App\Controllers
 */
class LoginController extends BaseController 
{
    /**
     * @var UsuarioService Servicio que gestiona la lógica de autenticación de usuarios.
     */
    protected $usuarioService;

    /**
     * Constructor del controlador.
     *
     * Inicializa el servicio de gestión de usuarios.
     */
    public function __construct() 
    {
        $this->usuarioService = new UsuarioService();
    }

    /**
     * Muestra el formulario para el inicio de sesión (Login).
     *
     * Si el usuario ya está autenticado en la sesión, es redirigido automáticamente a la página principal.
     *
     * @return string|ResponseInterface Contenido HTML del formulario de login.
     */
    public function create() 
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/');
        }
        return view('back/users/login', ['title' => 'Login']);
    }

    /**
     * Realiza la autenticación del usuario mediante sus credenciales (email y contraseña).
     *
     * Implementa medidas de seguridad críticas:
     * 1. **Throttler (Limitador de Tasa):** Restringe las solicitudes a un máximo de 5 intentos por minuto por cada IP para evitar ataques de fuerza bruta.
     * 2. **Regeneración de ID de Sesión:** Tras una autenticación exitosa, regenera el identificador de sesión para mitigar vulnerabilidades de fijación de sesión.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección a la raíz en caso de éxito o de vuelta al formulario con mensajes flash de error.
     */
    public function auth() 
    {
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
     * Cierra la sesión activa del usuario y elimina toda la información de sesión almacenada.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección a la pantalla de inicio.
     */
    public function logout() 
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
