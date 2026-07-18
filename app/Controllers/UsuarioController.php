<?php 

namespace App\Controllers;

/**
 * Controlador para gestión de usuarios refactorizado para usar Capa de Servicios.
 */
class UsuarioController extends BaseController {

    protected $usuarioService;

    public function __construct() {
        helper(['form', 'url']);
        $this->usuarioService = new \App\Services\UsuarioService();
    }

    /**
     * Muestra la vista de registro.
     * Nota: los clientes logueados (perfil_id != 1) son redirigidos al inicio.
     * Los admins logueados SÍ pueden acceder para dar de alta nuevos usuarios.
     */
    public function index_registrar() {    
        if (session()->get('logged_in') && session()->get('perfil_id') != 1) return redirect()->to('/');
        return view('front/pages/registro', ['title' => 'Registro']);
    }

    /**
     * Valida y registra un nuevo usuario delegando al servicio.
     */
    public function formValidation() {
        $resultado = $this->usuarioService->registrarUsuario($this->request->getPost());

        if ($resultado['status'] === 'success') {
            if (session()->get('logged_in') && session()->get('perfil_id') == 1) {
                return redirect()->to('/crud-usuarios')->with('success', 'Usuario registrado exitosamente');
            }
            return redirect()->to('/login')->with('success', $resultado['message']);
        } else {
            return redirect()->back()->withInput()->with('fail', $resultado['message']);
        }
    }

    /**
     * Muestra el listado de usuarios para administración.
     */
    public function index() {


        $resultado = $this->usuarioService->getUsuariosConStats();
        
        return view('back/users/crud_usuarios', [
            'usuarios' => $resultado['usuarios'],
            'counts'   => $resultado['counts'],
            'vista'    => $this->request->getVar('vista') ?? 'NO',
            'title'    => 'Gestión de Usuarios'
        ]);
    }

    /**
     * Muestra la configuración del perfil.
     */
    public function index_perfil() {
        return view('back/users/perfil_config', ['title' => 'Configuración Perfil']);
    }

    /**
     * Guarda cambios en el perfil delegando al servicio.
     */
    public function guardarCambios() {
        $image = $this->request->getFile('image');
        
        // Validación estricta de la imagen para prevenir subida de archivos maliciosos (RCE)
        $rulesImage = [
            'image' => 'is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png,image/webp]|max_size[image,2048]'
        ];
        
        if ($image && $image->isValid() && !$this->validate($rulesImage)) {
            return redirect()->back()->with('fail', 'La imagen de perfil no es válida o supera los 2MB.');
        }

        $resultado = $this->usuarioService->actualizarPerfil(
            session()->get('id_usuario'),
            [
                'usuario'  => $this->request->getVar('username'),
                'nombre'   => $this->request->getVar('name'),
                'apellido' => $this->request->getVar('surname'),
                'email'    => $this->request->getVar('email'),
            ],
            $image
        );

        if ($resultado['status'] === 'success') {
            session()->set($resultado['updated_data']);
            return redirect()->to('/perfil')->with('success', $resultado['message']);
        } else {
            return redirect()->back()->with('fail', $resultado['message']);
        }
    }

    /**
     * Cambia la contraseña del perfil.
     */
    public function cambiarPassword() {
        $resultado = $this->usuarioService->cambiarPassword(
            session()->get('id_usuario'),
            $this->request->getPost('current_password'),
            $this->request->getPost('new_password'),
            $this->request->getPost('confirm_password')
        );

        if ($resultado['status'] === 'success') {
            return redirect()->to('/perfil')->with('success', $resultado['message']);
        } else {
            return redirect()->to('/perfil')->with('fail', $resultado['message']);
        }
    }

    /**
     * Da de baja a un usuario delegando al servicio.
     */
    public function delete_usuario($id) {

        $this->usuarioService->darDeBaja($id);
        return redirect()->to('/crud-usuarios?vista=' . ($this->request->getGet('vista') ?? 'NO'));
    }

    /**
     * Reactiva a un usuario delegando al servicio.
     */
    public function activar_usuario($id) {

        $this->usuarioService->reactivar($id);
        return redirect()->to('/crud-usuarios?vista=' . ($this->request->getGet('vista') ?? 'SI'));
    }

    /**
     * Cambia el perfil de un usuario delegando al servicio.
     */
    public function editar_usuario($id) {

        $this->usuarioService->cambiarPerfil($id);
        return redirect()->to('/crud-usuarios')->with('success', 'Modificación exitosa');
    }

    /**
     * Elimina permanentemente a un usuario delegando al servicio.
     */
    public function eliminar_permanente($id) {

        
        $resultado = $this->usuarioService->eliminarPermanente($id);
        
        if ($resultado['status'] === 'success') {
            return redirect()->to('/crud-usuarios?vista=SI')->with('success', $resultado['message']);
        } else {
            return redirect()->to('/crud-usuarios?vista=SI')->with('fail', $resultado['message']);
        }
    }
}
