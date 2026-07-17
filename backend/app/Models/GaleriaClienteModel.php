<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Class GaleriaClienteModel
 *
 * Modelo que interactúa con la tabla 'galeria_clientes' de la base de datos de CVA Muebles.
 * Administra el repositorio de fotos de clientes y testimonios gráficos expuestos en el portal.
 *
 * @property int|string $id Identificador único del testimonio gráfico en la galería.
 * @property int|string $usuario_id Identificador del usuario cliente que envía la fotografía.
 * @property string $imagen Nombre del archivo físico de la imagen subida en el servidor.
 * @property string $comentario Breve testimonio o comentario del cliente sobre su experiencia.
 * @property string $fecha Marca de tiempo del registro de la foto.
 * @property string $activo Estado de moderación administrativa ('SI' = expuesta públicamente, 'NO' = pendiente de revisión).
 * 
 * @package App\Models
 */
class GaleriaClienteModel extends Model
{
    /**
     * @var string Nombre de la tabla en la base de datos.
     */
    protected $table = 'galeria_clientes';

    /**
     * @var string Nombre de la columna que actúa como clave primaria de la tabla.
     */
    protected $primaryKey = 'id';

    /**
     * @var array Campos de la tabla que están permitidos para su asignación e inserción masiva.
     */
    protected $allowedFields = ['usuario_id', 'imagen', 'comentario', 'fecha', 'activo'];

    /**
     * @var array Reglas de validación interna que aplica el modelo de forma automática antes de guardar.
     */
    protected $validationRules = [
        'usuario_id' => 'required|numeric',
        'imagen'     => 'required|max_length[255]',
        'activo'     => 'required'
    ];

    /**
     * Recupera el conjunto de fotografías aprobadas por administración ('activo' => 'SI'),
     * incorporando el nombre de pila del cliente que remitió la foto.
     * Ordena cronológicamente en orden descendente.
     *
     * @return array Listado de fotos aprobadas y activas para exhibición.
     */
    public function getActivas()
    {
        return $this->select('galeria_clientes.*, usuarios.nombre')
                    ->join('usuarios', 'usuarios.id_usuario = galeria_clientes.usuario_id')
                    ->where('activo', 'SI')
                    ->orderBy('fecha', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->findAll();
    }
}
