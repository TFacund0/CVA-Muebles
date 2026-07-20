<?php

namespace App\Models;
use CodeIgniter\Model;

class UsuarioModel extends Model 
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = ['nombre', 'apellido', 'usuario', 'email', 'pass', 'imagen', 'perfil_id'];
    
    protected $validationRules = [
        'nombre'    => 'required|min_length[2]|max_length[50]',
        'apellido'  => 'required|min_length[2]|max_length[50]',
        'usuario'   => 'required|min_length[3]|max_length[20]|is_unique[usuarios.usuario,id_usuario,{id_usuario}]',
        'email'     => 'required|valid_email|max_length[100]|is_unique[usuarios.email,id_usuario,{id_usuario}]',
        'perfil_id' => 'required|numeric'
    ];

    /**
     * Obtiene todos los usuarios (incluidos los dados de baja) con la descripción de su perfil.
     *
     * @return array Listado de usuarios con los datos de su perfil asociado.
     */
    public function getUsuariosAll() {
        return $this->select('usuarios.*, perfiles.descripcion as perfil')
                    ->join('perfiles', 'perfiles.id = usuarios.perfil_id')
                    ->withDeleted()
                    ->findAll();
    }

    /**
     * Busca un usuario por email o nombre de usuario (incluye dados de baja,
     * para que UsuarioService::autenticar() pueda distinguir ese caso del de
     * "no existe").
     *
     * @param string $login Email o nombre de usuario a buscar.
     * @return array|null Datos del usuario como arreglo asociativo, o null si no existe.
     */
    public function findByEmailOUsuario(string $login): ?array {
        return $this->withDeleted()
                    ->where('email', $login)
                    ->orWhere('usuario', $login)
                    ->first();
    }
}