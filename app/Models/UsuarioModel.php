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
                    ->join('perfiles', 'perfiles.id_perfiles = usuarios.perfil_id')
                    ->findAll();
    }

    /**
     * Calcula las estadísticas generales de los usuarios directamente en la base de datos.
     * Esto evita cargar todos los registros en memoria, mejorando el rendimiento.
     *
     * @return array Estadísticas de usuarios (total, activos, admins, suspendidos).
     */
    public function getEstadisticas()
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);
        $row = $builder->select('
            COUNT(*) as total,
            SUM(CASE WHEN baja = "NO" THEN 1 ELSE 0 END) as activos,
            SUM(CASE WHEN perfil_id = 1 THEN 1 ELSE 0 END) as admins,
            SUM(CASE WHEN baja = "SI" THEN 1 ELSE 0 END) as suspendidos
        ')->get()->getRowArray();
        
        return [
            'total'       => (int)($row['total'] ?? 0),
            'activos'     => (int)($row['activos'] ?? 0),
            'admins'      => (int)($row['admins'] ?? 0),
            'suspendidos' => (int)($row['suspendidos'] ?? 0),
        ];
    }

    public function getUsuariosAllFiltrados($search = null, $perfil = null, $paginate = false, $perPage = 15)
    {
        $builder = $this->select('usuarios.*, perfiles.descripcion as perfil')
                        ->join('perfiles', 'perfiles.id_perfiles = usuarios.perfil_id');
        
        if (!empty($search)) {
            $builder->groupStart()
                        ->like('usuarios.nombre', $search)
                        ->orLike('usuarios.apellido', $search)
                        ->orLike('usuarios.email', $search)
                        ->orLike('usuarios.usuario', $search)
                    ->groupEnd();
        }

        if (!empty($perfil) && strtolower($perfil) !== 'all') {
            $perfil_id = ($perfil === 'ADMIN') ? 1 : 2;
            $builder->where('usuarios.perfil_id', $perfil_id);
        }

        if ($paginate) {
            return [
                'data'  => $builder->paginate($perPage, 'usuarios'),
                'pager' => $this->pager
            ];
        }

        return [
            'data'  => $builder->findAll(),
            'pager' => null
        ];
    }
}