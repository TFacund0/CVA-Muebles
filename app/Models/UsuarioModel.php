<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Class UsuarioModel
 *
 * Modelo que interactúa con la tabla 'usuarios' de la base de datos de CVA Muebles.
 * Administra el perfil, credenciales, avatares de imagen, roles de acceso y estados de actividad
 * de los clientes y administradores de la plataforma.
 *
 * @property int|string $id_usuario Identificador único del usuario.
 * @property string $nombre Nombre del usuario.
 * @property string $apellido Apellido del usuario.
 * @property string $usuario Nombre de usuario único para inicio de sesión alternativo.
 * @property string $email Dirección de correo electrónico único del usuario.
 * @property string $pass Hash de la contraseña del usuario.
 * @property string $imagen Nombre del archivo físico del avatar de perfil.
 * @property int|string $perfil_id Identificador de rol o perfil de acceso (1 = Admin, 2 = Cliente).
 * @property string $baja Estado de suspensión o baja lógica ('SI' = cuenta suspendida, 'NO' = cuenta activa).
 * 
 * @package App\Models
 */
class UsuarioModel extends Model 
{
    /**
     * @var string Nombre de la tabla en la base de datos.
     */
    protected $table = 'usuarios';

    /**
     * @var string Nombre de la columna que actúa como clave primaria de la tabla.
     */
    protected $primaryKey = 'id_usuario';

    /**
     * @var array Campos de la tabla que están permitidos para su asignación e inserción masiva.
     */
    protected $allowedFields = ['nombre', 'apellido', 'usuario', 'email', 'pass', 'imagen', 'perfil_id', 'baja'];
    
    /**
     * @var array Reglas de validación interna que aplica el modelo de forma automática antes de guardar.
     * Incorpora validación condicional de unicidad (is_unique) excluyendo el ID actual en actualizaciones.
     */
    protected $validationRules = [
        'nombre'    => 'required|min_length[2]|max_length[50]',
        'apellido'  => 'required|min_length[2]|max_length[50]',
        'usuario'   => 'required|min_length[3]|max_length[20]|is_unique[usuarios.usuario,id_usuario,{id_usuario}]',
        'email'     => 'required|valid_email|max_length[100]|is_unique[usuarios.email,id_usuario,{id_usuario}]',
        'perfil_id' => 'required|numeric'
    ];

    /**
     * Recupera el listado completo de todos los usuarios registrados en el sistema,
     * incorporando la descripción corta de su rol de acceso (perfil) mediante un join.
     *
     * @return array Listado detallado de usuarios.
     */
    public function getUsuariosAll()
    {
        return $this->select('usuarios.*, perfiles.descripcion as perfil')
                    ->join('perfiles', 'perfiles.id = usuarios.perfil_id')
                    ->findAll();
    }
}