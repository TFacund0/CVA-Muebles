<?php

namespace App\Services;

use App\Models\UsuarioModel;
use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * Class UsuarioService
 *
 * Servicio encargado de la gestión integral de la lógica de negocio asociada a los usuarios y clientes
 * de CVA Muebles. Provee métodos seguros de autenticación, hashes y verificación de contraseñas,
 * registro y actualización de perfiles de usuario, subida de avatares/imágenes, asignación de roles,
 * control de baja lógica (suspensión) y baja física permanente con control de dependencias comerciales.
 *
 * @package App\Services
 */
class UsuarioService
{
    /**
     * @var UsuarioModel Modelo principal de acceso a datos para los usuarios.
     */
    protected $usuarioModel;

    /**
     * Constructor del servicio.
     *
     * Inicializa el modelo requerido para las operaciones de negocio sobre usuarios.
     */
    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    /**
     * Procesa la autenticación (login) de un usuario mediante su email o nombre de usuario
     * y contraseña. Valida el estado de suspensión temporal (baja) del perfil.
     *
     * @param string $login Email o nombre de usuario ingresado en el formulario.
     * @param string $password Contraseña en texto plano para verificación.
     * 
     * @return array Resumen de estado ('status' => 'success'|'error', 'message' => string, 'data' => array si tiene éxito).
     */
    public function autenticar($login, $password)
    {
        $usuario = $this->usuarioModel->where('email', $login)
                                      ->orWhere('usuario', $login)
                                      ->first();

        if (!$usuario) {
            return ['status' => 'error', 'message' => 'Email o nombre de usuario incorrectos'];
        }

        if ($usuario['baja'] == 'SI') {
            return ['status' => 'error', 'message' => 'Usuario dado de baja'];
        }

        if (!password_verify($password, $usuario['pass'])) {
            return ['status' => 'error', 'message' => 'Contraseña Incorrecta'];
        }

        return [
            'status' => 'success',
            'data'   => [
                'id_usuario' => $usuario['id_usuario'],
                'nombre'     => $usuario['nombre'],
                'apellido'   => $usuario['apellido'],
                'email'      => $usuario['email'],
                'usuario'    => $usuario['usuario'],
                'perfil_id'  => $usuario['perfil_id'],
                'imagen'     => $usuario['imagen'],
                'logged_in'  => TRUE
            ]
        ];
    }

    /**
     * Recupera el listado total de usuarios del sistema y computa indicadores clave
     * (totales, activos, administradores, suspendidos) para el panel de administración.
     *
     * @return array Estructura con la lista de usuarios ('usuarios') y métricas estadísticas ('counts').
     */
    public function getUsuariosConStats($search = null, $perfil = null, $filterMode = 'client')
    {
        $counts = $this->usuarioModel->getEstadisticas();
        
        $isServerMode = ($filterMode === 'server');
        
        if ($search || ($perfil && strtolower($perfil) !== 'all') || $isServerMode) {
            $resultado = $this->usuarioModel->getUsuariosAllFiltrados($search, $perfil, $isServerMode);
            $usuarios = $resultado['data'];
            $pager = $resultado['pager'];
        } else {
            $resultado = $this->usuarioModel->getUsuariosAllFiltrados();
            $usuarios = $resultado['data'];
            $pager = null;
        }

        return [
            'usuarios' => $usuarios,
            'pager'    => $pager,
            'counts'   => $counts
        ];
    }

    /**
     * Procesa el registro e inserción de un nuevo usuario en la plataforma.
     * Encripta la contraseña de forma segura mediante `password_hash` con el algoritmo predeterminado.
     *
     * @param array $data Datos de registro del usuario (nombre, apellido, email, usuario, password).
     * 
     * @return array Resumen de estado ('status' => 'success'|'error', 'message' => string).
     */
    public function registrarUsuario($data)
    {
        try {
            if (empty($data['pass']) || strlen($data['pass']) < 6) {
                return ['status' => 'error', 'message' => 'La contraseña es obligatoria y debe tener al menos 6 caracteres.'];
            }

            $userData = [
                'nombre'    => $data['name'],
                'apellido'  => $data['surname'],
                'usuario'   => $data['user'],
                'email'     => $data['email'],
                'pass'      => password_hash($data['pass'], PASSWORD_DEFAULT),
                'perfil_id' => 2,
                'baja'      => 'NO'
            ];

            if ($this->usuarioModel->insert($userData) === false) {
                return ['status' => 'error', 'message' => 'Errores: ' . implode(', ', $this->usuarioModel->errors())];
            }
            return ['status' => 'success', 'message' => 'Usuario registrado con éxito.'];

        } catch (\Exception $e) {
            log_message('error', '[UsuarioService::registrarUsuario] ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Ocurrió un error interno al registrar el usuario. Intente nuevamente.'];
        }
    }

    /**
     * Procesa la actualización del perfil de un usuario.
     * Maneja el reemplazo seguro y borrado físico del avatar o imagen de perfil previa si se carga una nueva.
     *
     * @param int|string $userId Identificador único del usuario.
     * @param array $data Nuevos campos textuales de perfil (usuario, nombre, apellido, email).
     * @param UploadedFile|null $image Archivo físico de la nueva imagen de perfil (opcional).
     * 
     * @return array Resumen de estado ('status' => 'success'|'error', 'message' => string, 'updated_data' => array si tiene éxito).
     */
    public function actualizarPerfil($userId, $data, UploadedFile $image = null)
    {
        try {
            $updateData = [
                'usuario'  => $data['usuario'],
                'nombre'   => $data['nombre'],
                'apellido' => $data['apellido'],
                'email'    => $data['email'],
            ];

            if ($image && $image->isValid() && !$image->hasMoved()) {
                // Borrar imagen anterior si existe
                $user_actual = $this->usuarioModel->find($userId);
                if ($user_actual && !empty($user_actual['imagen'])) {
                    $old_path = FCPATH . 'assets/uploads/perfil/' . $user_actual['imagen'];
                    if (file_exists($old_path)) {
                        @unlink($old_path);
                    }
                }

                $nombre_imagen = $image->getRandomName();
                $image->move(FCPATH . 'assets/uploads/perfil', $nombre_imagen);
                $updateData['imagen'] = $nombre_imagen;
            }

            if ($this->usuarioModel->update($userId, $updateData) === false) {
                return ['status' => 'error', 'message' => 'Errores: ' . implode(', ', $this->usuarioModel->errors())];
            }

            return ['status' => 'success', 'message' => 'Perfil actualizado correctamente.', 'updated_data' => $updateData];

        } catch (\Exception $e) {
            log_message('error', '[UsuarioService::actualizarPerfil] ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Ocurrió un error interno al actualizar el perfil. Intente nuevamente.'];
        }
    }

    /**
     * Permite a un usuario cambiar su contraseña validando rigurosamente su contraseña actual.
     *
     * @param int|string $userId Identificador del usuario.
     * @param string $currentPassword Contraseña actual provista por el usuario.
     * @param string $newPassword Nueva contraseña propuesta.
     * @param string $confirmPassword Confirmación de la nueva contraseña.
     * 
     * @return array Resumen de estado ('status' => 'success'|'error', 'message' => string).
     */
    public function cambiarPassword($userId, $currentPassword, $newPassword, $confirmPassword)
    {
        if ($newPassword !== $confirmPassword) {
            return ['status' => 'error', 'message' => 'Las nuevas contraseñas no coinciden.'];
        }

        $usuario = $this->usuarioModel->find($userId);
        if (!$usuario) {
            return ['status' => 'error', 'message' => 'Usuario no encontrado.'];
        }

        if (!password_verify($currentPassword, $usuario['pass'])) {
            return ['status' => 'error', 'message' => 'La contraseña actual es incorrecta.'];
        }

        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        if ($this->usuarioModel->update($userId, ['pass' => $hash]) === false) {
            return ['status' => 'error', 'message' => 'No se pudo actualizar la contraseña.'];
        }

        return ['status' => 'success', 'message' => 'Contraseña actualizada correctamente.'];
    }

    /**
     * Alterna administrativamente el perfil o rol asignado a un usuario (Admin <-> Cliente/Usuario).
     *
     * @param int|string $id Identificador del usuario.
     * 
     * @return bool Retorna true si la actualización fue exitosa, false si el usuario no existe.
     */
    public function cambiarPerfil($id)
    {
        $usuario = $this->usuarioModel->find($id);
        if (!$usuario) {
            return false;
        }

        $nuevo_perfil = ($usuario['perfil_id'] == 1) ? 2 : 1;
        return $this->usuarioModel->update($id, ['perfil_id' => $nuevo_perfil]);
    }

    /**
     * Realiza una baja lógica (suspensión temporal) sobre el perfil de un usuario marcando 'baja' => 'SI'.
     *
     * @param int|string $id Identificador del usuario.
     * 
     * @return bool|int|string Retorna el resultado de la actualización en base de datos.
     */
    public function darDeBaja($id)
    {
        return $this->usuarioModel->update($id, ['baja' => 'SI']);
    }

    /**
     * Reactiva la cuenta de un usuario previamente suspendido marcando 'baja' => 'NO'.
     *
     * @param int|string $id Identificador del usuario.
     * 
     * @return bool|int|string Retorna el resultado de la actualización en base de datos.
     */
    public function reactivar($id)
    {
        return $this->usuarioModel->update($id, ['baja' => 'NO']);
    }

    /**
     * Recupera la información detallada de un usuario por su identificador único.
     *
     * @param int|string $id Identificador del usuario.
     * 
     * @return array|null Datos de usuario o null si no se encuentra.
     */
    public function getUsuario($id)
    {
        return $this->usuarioModel->find($id);
    }

    /**
     * Recupera la lista de todos los clientes activos del sistema (no administradores y no suspendidos).
     *
     * @return array Listado de usuarios clientes activos.
     */
    public function getClientesActivos()
    {
        return $this->usuarioModel
            ->where('perfil_id', 2)
            ->where('baja', 'NO')
            ->findAll();
    }

    /**
     * Procesa la eliminación física permanente de un usuario de la base de datos.
     * Restringe severamente el borrado si existen transacciones o facturas de compras históricas
     * asociadas en el sistema para garantizar la integridad fiscal y referencial.
     * Si no hay dependencias, elimina las marcas de favoritos antes de la eliminación final del usuario.
     *
     * @param int|string $id Identificador único del usuario.
     * 
     * @return array Resumen de estado ('status' => 'success'|'error', 'message' => string).
     */
    public function eliminarPermanente($id)
    {
        $db = \Config\Database::connect();
        
        // 1. Verificar si tiene compras o pedidos asociados en ventas_cabecera
        $compras = $db->table('ventas_cabecera')->where('usuario_id', $id)->countAllResults();
        if ($compras > 0) {
            return [
                'status'  => 'error',
                'message' => 'No se puede eliminar permanentemente este usuario porque tiene compras o pedidos asociados en el sistema. Puedes mantenerlo como Suspendido para resguardar el historial comercial.'
            ];
        }

        try {
            // 2. Eliminar favoritos en favoritos
            $db->table('favoritos')->where('usuario_id', $id)->delete();

            // 3. Eliminar de la tabla usuarios
            $this->usuarioModel->delete($id);

            return [
                'status'  => 'success',
                'message' => 'Usuario eliminado permanentemente del sistema.'
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Ocurrió un error al intentar eliminar el usuario: ' . $e->getMessage()
            ];
        }
    }
}
