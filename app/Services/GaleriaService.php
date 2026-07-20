<?php

namespace App\Services;

use App\Models\GaleriaClienteModel;

/**
 * Servicio para manejar la galería de fotos de clientes.
 */
class GaleriaService
{
    protected $galeriaModel;

    public function __construct(?GaleriaClienteModel $galeriaModel = null)
    {
        $this->galeriaModel = $galeriaModel ?? new GaleriaClienteModel();
    }

    /**
     * Obtiene fotos aprobadas para la vista pública.
     *
     * @return array Fotos activas de la galería
     */
    public function getAprobadas()
    {
        return $this->galeriaModel->getActivas();
    }

    /**
     * Obtiene todas las fotos con datos de usuario para el admin.
     *
     * @return array Fotos de la galería con el nombre del usuario, ordenadas por estado y fecha
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
     * Obtiene la cantidad de fotos pendientes de moderación.
     *
     * @return int Cantidad de fotos con estado inactivo
     */
    public function getPendientesCount()
    {
        return $this->galeriaModel->where('activo', 'NO')->countAllResults();
    }

    /**
     * Procesa la subida de una foto por parte de un cliente.
     *
     * @param int $usuario_id Identificador del usuario que sube la foto
     * @param \CodeIgniter\HTTP\Files\UploadedFile $img Archivo de imagen subido
     * @param string $comentario Comentario asociado a la foto
     * @return int|bool ID insertado en éxito, false si el archivo no es válido
     */
    public function subir($usuario_id, $img, $comentario)
    {
        if ($img->isValid() && !$img->hasMoved()) {
            $newName = $img->getRandomName();
            $img->move(FCPATH . 'assets/uploads/galeria', $newName);

            return $this->galeriaModel->insert([
                'usuario_id' => $usuario_id,
                'imagen'     => $newName,
                'comentario' => $comentario,
                'fecha'      => date('Y-m-d H:i:s'),
                'activo'     => 'NO'
            ]);
        }
        return false;
    }

    /**
     * Aprueba una foto.
     *
     * @param int $id Identificador de la foto
     * @return bool Resultado de la actualización
     */
    public function aprobar($id)
    {
        return $this->galeriaModel->update($id, ['activo' => 'SI']);
    }

    /**
     * Elimina una foto, incluyendo el archivo físico si existe.
     *
     * @param int $id Identificador de la foto
     * @return bool Resultado de la eliminación, false si la foto no existe
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
