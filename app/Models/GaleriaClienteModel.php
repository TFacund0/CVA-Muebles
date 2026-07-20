<?php
namespace App\Models;
use CodeIgniter\Model;

class GaleriaClienteModel extends Model {
    protected $table = 'galeria_clientes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['usuario_id', 'imagen', 'comentario', 'fecha', 'activo'];

    protected $validationRules = [
        'usuario_id' => 'required|numeric',
        'imagen'     => 'required|max_length[255]',
        'activo'     => 'required'
    ];

    /**
     * Obtiene las imágenes de la galería activas, ordenadas de la más reciente a la más antigua.
     *
     * @return array Listado de imágenes de galería con el nombre del usuario asociado.
     */
    public function getActivas() {
        return $this->select('galeria_clientes.*, usuarios.nombre')
                    ->join('usuarios', 'usuarios.id_usuario = galeria_clientes.usuario_id')
                    ->where('activo', 'SI')
                    ->orderBy('fecha', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->findAll();
    }
}
