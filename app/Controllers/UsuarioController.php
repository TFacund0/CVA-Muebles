<?php 

namespace App\Controllers;

use App\Services\UsuarioService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class UsuarioController
 *
 * Controlador encargado de la gestión de usuarios, perfiles, registro de cuentas,
 * actualizaciones de perfiles e imágenes de avatar, y administración de usuarios por parte de administradores (CRUD).
 * Delega la lógica de negocio a la capa de servicios (`UsuarioService`).
 *
 * @package App\Controllers
 */
class UsuarioController extends BaseController 
{
    /**
     * @var UsuarioService Servicio que encapsula la lógica de negocio para la gestión de usuarios.
     */
    protected $usuarioService;

    /**
     * Constructor del controlador.
     *
     * Inicializa los helpers esenciales y el servicio de gestión de usuarios.
     */
    public function __construct() 
    {
        helper(['form', 'url']);
        $this->usuarioService = new \App\Services\UsuarioService();
    }

    /**
     * Muestra la interfaz para el registro de nuevos usuarios.
     *
     * Si un usuario ya ha iniciado sesión y no es administrador (perfil_id != 1),
     * es redirigido automáticamente a la página principal. Los administradores sí pueden acceder
     * para realizar el alta de usuarios en frío.
     *
     * @return string|ResponseInterface Contenido HTML de la vista de registro.
     */
    public function index_registrar() 
    {    
        if (session()->get('logged_in') && session()->get('perfil_id') != 1) {
            return redirect()->to('/');
        }
        return view('back/users/registro', ['title' => 'Registro']);
    }

    /**
     * Valida y procesa el formulario para el registro de un nuevo usuario en el sistema.
     *
     * Obtiene los datos del formulario enviados por POST, los valida y registra delegando en `UsuarioService`.
     * Si el registro es exitoso y el autor de la petición es un administrador, se le redirige a la
     * pantalla del CRUD de usuarios. Si es un usuario público en proceso de registro, se le redirige al login.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección a la ruta correspondiente con datos de estado.
     */
    public function formValidation() 
    {
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
     * Muestra la interfaz de administración general (CRUD) de usuarios.
     *
     * Permite filtrar la visualización de los usuarios de acuerdo al parámetro 'vista' (bajas o activos).
     *
     * @return string|ResponseInterface Contenido HTML de la vista CRUD de usuarios.
     */
    public function index() 
    {
        // Optimización de concurrencia: Libera la sesión tempranamente para evitar cuellos de botella en peticiones asíncronas.
        session_write_close();
        
        $search = $this->request->getVar('search');
        $perfil = $this->request->getVar('perfil');
        $filterMode = env('app.filterMode', 'client');
        $vista = $this->request->getVar('vista') ?? 'NO';
        
        $searchFilter = ($filterMode === 'server') ? $search : null;
        $perfilFilter = ($filterMode === 'server') ? $perfil : null;

        $resultado = $this->usuarioService->getUsuariosConStats($searchFilter, $perfilFilter, $filterMode);
        
        return view('back/users/crud_usuarios', [
            'usuarios'   => $resultado['usuarios'],
            'pager'      => $resultado['pager'],
            'counts'     => $resultado['counts'],
            'vista'      => $vista,
            'search'     => $search,
            'perfil'     => $perfil,
            'filterMode' => $filterMode,
            'title'      => 'Gestión de Usuarios'
        ]);
    }

    /**
     * Muestra la pantalla de configuración del perfil del usuario actualmente autenticado.
     *
     * @return string|ResponseInterface Contenido HTML de la pantalla de configuración de perfil.
     */
    public function index_perfil() 
    {
        return view('back/users/perfil_config', ['title' => 'Configuración Perfil']);
    }

    /**
     * Procesa los cambios en los datos del perfil y la imagen de avatar del usuario.
     *
     * Valida estrictamente la carga del archivo de imagen para mitigar riesgos de seguridad
     * (e.g., ejecución remota de código - RCE) y delega la persistencia al servicio de negocio.
     * Si los cambios son aplicados con éxito, actualiza los datos persistidos en la sesión activa del usuario.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección de vuelta con estado del proceso.
     */
    public function guardarCambios() 
    {
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
     * Cambia la contraseña de acceso del usuario autenticado.
     *
     * Requiere que el usuario provea su contraseña actual por razones de seguridad, además de la
     * nueva contraseña y su correspondiente confirmación para evitar errores de escritura.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección al perfil con el mensaje de estado.
     */
    public function cambiarPassword() 
    {
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
     * Realiza la baja lógica (desactivación) de un usuario en el sistema.
     *
     * @param int|string $id Identificador único del usuario a dar de baja.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección al CRUD de usuarios.
     */
    public function delete_usuario($id) 
    {
        $this->usuarioService->darDeBaja($id);
        return redirect()->to('/crud-usuarios?vista=' . ($this->request->getGet('vista') ?? 'NO'));
    }

    /**
     * Reactiva o da de alta nuevamente a un usuario que se encontraba inactivo.
     *
     * @param int|string $id Identificador único del usuario a reactivar.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección al CRUD de usuarios.
     */
    public function activar_usuario($id) 
    {
        $this->usuarioService->reactivar($id);
        return redirect()->to('/crud-usuarios?vista=' . ($this->request->getGet('vista') ?? 'SI'));
    }

    /**
     * Permite editar o alternar el tipo de perfil de un usuario (ej: Administrador <-> Cliente).
     *
     * @param int|string $id Identificador único del usuario a modificar.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección al CRUD de usuarios con mensaje flash.
     */
    public function editar_usuario($id) 
    {
        $this->usuarioService->cambiarPerfil($id);
        return redirect()->to('/crud-usuarios')->with('success', 'Modificación exitosa');
    }

    /**
     * Elimina permanentemente de la base de datos a un usuario seleccionado.
     *
     * @param int|string $id Identificador único del usuario a eliminar definitivamente.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección al CRUD de usuarios con mensaje de estado.
     */
    public function eliminar_permanente($id) 
    {
        $resultado = $this->usuarioService->eliminarPermanente($id);
        
        if ($resultado['status'] === 'success') {
            return redirect()->to('/crud-usuarios?vista=SI')->with('success', $resultado['message']);
        } else {
            return redirect()->to('/crud-usuarios?vista=SI')->with('fail', $resultado['message']);
        }
    }
}
