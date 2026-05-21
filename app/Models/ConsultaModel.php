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
}