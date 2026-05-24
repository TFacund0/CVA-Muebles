<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Class ConsultaModel
 *
 * Modelo que interactúa con la tabla 'consultas' de la base de datos de CVA Muebles.
 * Permite gestionar los mensajes de contacto, preguntas y solicitudes de presupuestos de los usuarios.
 *
 * @property int|string $id_consulta Identificador único de la consulta.
 * @property string $nombre Nombre del remitente del mensaje.
 * @property string $apellido Apellido del remitente del mensaje.
 * @property string $email Correo electrónico del remitente.
 * @property string $telefono Número de teléfono de contacto.
 * @property string $asunto Asunto descriptivo de la consulta.
 * @property string $descripcion Contenido detallado del mensaje de la consulta.
 * @property string $fecha Marca de tiempo del registro de la consulta.
 * @property string $activo Estado de atención de la consulta ('SI' = pendiente/activa, 'NO' = respondida/archivada).
 * 
 * @package App\Models
 */
class ConsultaModel extends Model
{
    /**
     * @var string Nombre de la tabla en la base de datos.
     */
    protected $table = 'consultas';

    /**
     * @var string Nombre de la columna que actúa como clave primaria de la tabla.
     */
    protected $primaryKey = 'id_consulta';

    /**
     * @var array Campos de la tabla que están permitidos para su asignación e inserción masiva.
     */
    protected $allowedFields = ['nombre', 'apellido', 'email', 'telefono', 'asunto', 'descripcion', 'fecha', 'activo'];

    /**
     * @var array Reglas de validación interna que aplica el modelo de forma automática antes de guardar.
     */
    protected $validationRules = [
        'nombre'      => 'required|min_length[3]',
        'apellido'    => 'required|min_length[3]',
        'email'       => 'required|valid_email',
        'descripcion' => 'required|min_length[10]'
    ];

    /**
     * Calcula las estadísticas generales de las consultas directamente en la base de datos.
     *
     * @return array Estadísticas de consultas (total, mensuales, activos, presupuestos).
     */
    public function getEstadisticas()
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);
        $currentMonth = date('m');
        $currentYear = date('Y');

        $row = $builder->select("
            COUNT(*) as total,
            SUM(CASE WHEN MONTH(fecha) = {$currentMonth} AND YEAR(fecha) = {$currentYear} THEN 1 ELSE 0 END) as mensuales,
            SUM(CASE WHEN activo = 'SI' THEN 1 ELSE 0 END) as activos,
            SUM(CASE WHEN asunto LIKE '%presupuesto%' THEN 1 ELSE 0 END) as presupuestos
        ")->get()->getRowArray();
        
        return [
            'total'        => (int)($row['total'] ?? 0),
            'mensuales'    => (int)($row['mensuales'] ?? 0),
            'activos'      => (int)($row['activos'] ?? 0),
            'presupuestos' => (int)($row['presupuestos'] ?? 0),
        ];
    }

    public function getConsultasFiltradas($search = null, $asunto = null, $paginate = false, $perPage = 15)
    {
        $builder = $this->orderBy('fecha', 'DESC');

        if (!empty($search)) {
            $builder->groupStart()
                        ->like('nombre', $search)
                        ->orLike('apellido', $search)
                        ->orLike('email', $search)
                        ->orLike('asunto', $search)
                    ->groupEnd();
        }

        if (!empty($asunto) && strtoupper($asunto) !== 'ALL') {
            $builder->where('asunto', $asunto);
        }

        if ($paginate) {
            return [
                'data'  => $builder->paginate($perPage, 'consultas'),
                'pager' => $this->pager
            ];
        }

        return [
            'data'  => $builder->findAll(),
            'pager' => null
        ];
    }
}