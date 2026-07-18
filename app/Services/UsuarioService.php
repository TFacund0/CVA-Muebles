<?php

namespace App\Services;

use App\Models\UsuarioModel;

/**
 * Servicio para manejar la lógica de negocio relacionada con los usuarios.
 */
class UsuarioService
{
    protected $usuarioModel;

    public function __construct(?UsuarioModel $usuarioModel = null)
    {
        $this->usuarioModel = $usuarioModel ?? new UsuarioModel();
    }

    /**
     * Autentica a un usuario.
     */
    public function autenticar($login, $password)
    {
        $usuario = $this->usuarioModel->findByEmailOUsuario($login);

        if (!$usuario) {
            return ['status' => 'error', 'message' => 'Email o nombre de usuario incorrectos'];
        }

        if ($usuario['deleted_at'] !== null) {
            return ['status' => 'error', 'message' => 'Usuario dado de baja'];
        }

        if (!password_verify($password, $usuario['pass'])) {
            return ['status' => 'error', 'message' => 'Contraseña Incorrecta'];
        }

        return [
            'status' => 'success',
            'data' => [
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
     * Obtiene el listado de usuarios con estadísticas procesadas para el panel.
     */
    public function getUsuariosConStats()
    {
        $usuarios = $this->usuarioModel->getUsuariosAll();

        $counts = [
            'total' => count($usuarios),
            'activos' => 0,
            'admins' => 0,
            'suspendidos' => 0
        ];

        foreach ($usuarios as $u) {
            if ($u['deleted_at'] === null) $counts['activos']++;
            else $counts['suspendidos']++;
            
            if ($u['perfil_id'] == 1) $counts['admins']++;
        }

        return [
            'usuarios' => $usuarios,
            'counts' => $counts
        ];
    }

    /**
     * Registra un nuevo usuario.
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
            ];

            if ($this->usuarioModel->insert($userData) === false) {
                return ['status' => 'error', 'message' => 'Errores: ' . implode(', ', $this->usuarioModel->errors())];
            }
            return ['status' => 'success', 'message' => 'Usuario registrado con éxito.'];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Actualiza el perfil de un usuario.
     */
    public function actualizarPerfil($userId, $data, $image = null)
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
                    if (file_exists($old_path)) @unlink($old_path);
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
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Cambia la contraseña de un usuario validando su contraseña actual.
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
     * Cambia el rol de un usuario (Admin <-> Cliente).
     */
    public function cambiarPerfil($id)
    {
        $usuario = $this->usuarioModel->withDeleted()->find($id);
        if (!$usuario) return false;

        $nuevo_perfil = ($usuario['perfil_id'] == 1) ? 2 : 1;
        return $this->usuarioModel->update($id, ['perfil_id' => $nuevo_perfil]);
    }

    /**
     * Da de baja (soft delete) a un usuario.
     */
    public function darDeBaja($id)
    {
        return $this->usuarioModel->delete($id);
    }

    /**
     * Reactiva a un usuario dado de baja.
     */
    public function reactivar($id)
    {
        // deleted_at no está en $allowedFields (es un campo protegido, gestionado
        // por el soft-delete de CI4), así que Model::update() lo descartaría.
        // Se restaura vía el query builder directo.
        return $this->usuarioModel->builder()->where('id_usuario', $id)->update(['deleted_at' => null]);
    }

    /**
     * Busca un usuario por ID (incluye dados de baja).
     */
    public function getUsuario($id)
    {
        return $this->usuarioModel->withDeleted()->find($id);
    }

    /**
     * Obtiene todos los clientes activos (perfil_id = 2, no dados de baja).
     */
    public function getClientesActivos()
    {
        return $this->usuarioModel
            ->where('perfil_id', 2)
            ->findAll();
    }

    /**
     * Elimina permanentemente a un usuario si no tiene compras asociadas.
     */
    public function eliminarPermanente($id)
    {
        $db = \Config\Database::connect();
        
        // 1. Verificar si tiene compras o pedidos asociados en ventas_cabecera
        $compras = $db->table('ventas_cabecera')->where('usuario_id', $id)->countAllResults();
        if ($compras > 0) {
            return [
                'status' => 'error',
                'message' => 'No se puede eliminar permanentemente este usuario porque tiene compras o pedidos asociados en el sistema. Puedes mantenerlo como Suspendido para resguardar el historial comercial.'
            ];
        }

        try {
            // 2. Eliminar favoritos en favoritos
            $db->table('favoritos')->where('usuario_id', $id)->delete();

            // 3. Eliminar de la tabla usuarios (purge=true, ignora el soft-delete)
            $this->usuarioModel->delete($id, true);

            return [
                'status' => 'success',
                'message' => 'Usuario eliminado permanentemente del sistema.'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Ocurrió un error al intentar eliminar el usuario: ' . $e->getMessage()
            ];
        }
    }
}
