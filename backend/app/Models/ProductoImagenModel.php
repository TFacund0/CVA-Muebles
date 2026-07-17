<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Class ProductoImagenModel
 *
 * Modelo que interactúa con la tabla 'producto_imagenes' de la base de datos de CVA Muebles.
 * Gestiona el repositorio de fotografías secundarias adicionales pertenecientes a las galerías
 * de cada producto/mueble.
 *
 * @property int|string $id Identificador único del registro de imagen.
 * @property int|string $producto_id Identificador del producto asociado a la imagen.
 * @property string $imagen Nombre del archivo físico de la imagen almacenada.
 * @property int|string $orden Índice numérico para organizar el orden de exposición de las fotos.
 * 
 * @package App\Models
 */
class ProductoImagenModel extends Model
{
    /**
     * @var string Nombre de la tabla en la base de datos.
     */
    protected $table      = 'producto_imagenes';

    /**
     * @var string Nombre de la columna que actúa como clave primaria de la tabla.
     */
    protected $primaryKey = 'id';

    /**
     * @var bool Define si la clave primaria utiliza auto-incremento.
     */
    protected $useAutoIncrement = true;

    /**
     * @var string Tipo de dato devuelto por defecto al consultar la base de datos.
     */
    protected $returnType     = 'array';

    /**
     * @var bool Define si se utiliza borrado lógico automático provisto por el framework.
     */
    protected $useSoftDeletes = false;

    /**
     * @var array Campos de la tabla que están permitidos para su asignación e inserción masiva.
     */
    protected $allowedFields = ['producto_id', 'imagen', 'orden'];

    /**
     * Recupera la colección completa de imágenes secundarias asociadas a un producto
     * específico, ordenadas ascendentemente por su índice de prioridad.
     *
     * @param int|string $producto_id Identificador único del producto.
     * 
     * @return array Listado de imágenes de galería del producto.
     */
    public function getImagenesPorProducto($producto_id)
    {
        return $this->where('producto_id', $producto_id)
                    ->orderBy('orden', 'ASC')
                    ->findAll();
    }
}
