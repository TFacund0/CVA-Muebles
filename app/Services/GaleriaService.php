<?php

namespace App\Services;

use App\Models\GaleriaClienteModel;
use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * Class GaleriaService
 *
 * Servicio encargado de la gestión de la galería de fotos subidas por los clientes.
 * Provee la lógica de negocio para la subida de archivos físicos al servidor con nombres únicos,
 * moderación y aprobación administrativa de imágenes, y borrado permanente de archivos del disco.
 *
 * @package App\Services
 */
class GaleriaService
{
    /**
     * @var GaleriaClienteModel Modelo para interactuar con la tabla de galería de clientes en la base de datos.
     */
    protected $galeriaModel;

    /**
     * Constructor del servicio.
     *
     * Inicializa el modelo de acceso a datos para la galería de clientes.
     */
    public function __construct()
    {
        $this->galeriaModel = new GaleriaClienteModel();
    }

    /**
     * Recupera todas las fotos de la galería que han sido moderadas y aprobadas ('activo' => 'SI')
     * para su exposición en el catálogo público de CVA Muebles.
     *
     * @return array Listado de fotos aprobadas con comentarios e información asociada.
     */
    public function getAprobadas()
    {
        return $this->galeriaModel->getActivas();
    }

    /**
     * Recupera todas las fotos registradas en el sistema (aprobadas y pendientes), enriquecidas
     * con el nombre de usuario del cliente que las subió.
     * Ordena las fotos priorizando las pendientes de aprobación ('activo' => 'NO') y luego las más recientes.
     *
     * @return array Listado detallado de todas las fotos de la galería de clientes.
     */
    public function getAllConUsuarios()
    {
        return $this->galeriaModel->select('galeria_clientes.*, usuarios.nombre')
                                  ->join('usuarios', 'usuarios.id_usuario = galeria_clientes.usuario_id')
                                  ->orderBy('activo', 'ASC')
                                  ->orderBy('fecha', 'DESC')
                                  ->findAll();
    }

    /**
     * Obtiene el número total de fotos subidas por clientes que aún no han sido
     * moderadas ni aprobadas para la exhibición pública.
     *
     * @return int Cantidad de fotos pendientes de aprobación.
     */
    public function getPendientesCount()
    {
        return $this->galeriaModel->where('activo', 'NO')->countAllResults();
    }

    /**
     * Procesa de forma segura la carga física y el registro de una nueva imagen enviada por un cliente.
     * Asigna un nombre aleatorio seguro, mueve la imagen a la carpeta física de subidas
     * del taller y registra la relación en base de datos bajo un estado inactivo ('NO') por defecto.
     *
     * @param int|string $usuario_id Identificador único del usuario que sube la imagen.
     * @param UploadedFile $img Instancia del archivo cargado a través del formulario.
     * @param string $comentario Breve comentario descriptivo o de agradecimiento provisto por el usuario.
     * 
     * @return bool|int|string Retorna el identificador de inserción si es exitoso, o false en caso de fallo.
     */
    public function subir($usuario_id, UploadedFile $img, $comentario)
    {
        if ($img->isValid() && !$img->hasMoved()) {
            try {
                $tempName = $img->getRandomName();
                $nombre_imagen = pathinfo($tempName, PATHINFO_FILENAME) . '.webp';
                
                $img->move(FCPATH . 'assets/uploads/galeria', $tempName);
                
                $originalPath = FCPATH . 'assets/uploads/galeria/' . $tempName;
                $destPath = FCPATH . 'assets/uploads/galeria/' . $nombre_imagen;
                
                $imageService = \Config\Services::image();
                $imageService->withFile($originalPath);
                
                $width = $imageService->getWidth();
                $height = $imageService->getHeight();
                
                if ($width > 1200 || $height > 1200) {
                    $imageService->resize(1200, 1200, true, 'auto');
                }
                
                $imageService->save($destPath, 80);
                
                if ($originalPath !== $destPath && file_exists($originalPath)) {
                    @unlink($originalPath);
                }

                return $this->galeriaModel->insert([
                    'usuario_id' => $usuario_id,
                    'imagen'     => $nombre_imagen,
                    'comentario' => $comentario,
                    'fecha'      => date('Y-m-d H:i:s'),
                    'activo'     => 'NO'
                ]);
            } catch (\Exception $e) {
                if (isset($originalPath) && file_exists($originalPath)) {
                    @unlink($originalPath);
                }
                log_message('error', 'Error al optimizar imagen en galería de cliente: ' . $e->getMessage());
                return false;
            }
        }
        return false;
    }

    /**
     * Aprueba la exhibición pública de una foto en el panel de testimonios del sitio web.
     *
     * @param int|string $id Identificador único de la foto en la galería.
     * 
     * @return bool|int|string Retorna el resultado del update de la base de datos.
     */
    public function aprobar($id)
    {
        return $this->galeriaModel->update($id, ['activo' => 'SI']);
    }

    /**
     * Procesa la eliminación física del archivo de imagen del almacenamiento del servidor
     * y elimina su registro correspondiente de la base de datos.
     *
     * @param int|string $id Identificador único de la foto a eliminar.
     * 
     * @return bool True si se eliminó correctamente física y lógicamente; false si no existe la imagen.
     */
    public function eliminar($id)
    {
        $foto = $this->galeriaModel->find($id);
        if ($foto) {
            $path = FCPATH . 'assets/uploads/galeria/' . $foto['imagen'];
            if (file_exists($path)) {
                @unlink($path);
            }
            return $this->galeriaModel->delete($id);
        }
        return false;
    }
}
