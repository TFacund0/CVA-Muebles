<?php

namespace App\Models;

use CodeIgniter\Model;

class ConsultaModel extends Model {
    protected $table = 'consultas';
    protected $primaryKey = 'id_consulta';
    protected $allowedFields = ['nombre', 'apellido', 'email', 'telefono', 'asunto', 'descripcion', 'fecha', 'activo'];

    protected $validationRules = [
        'nombre'      => 'required|min_length[3]',
        'apellido'    => 'required|min_length[3]',
        'email'       => 'required|valid_email',
        'descripcion' => 'required|min_length[10]'
    ];

    /**
     * Todas las consultas ordenadas de la más reciente a la más antigua.
     */
    public function getAllOrdenadas(): array {
        return $this->orderBy('fecha', 'DESC')->findAll();
    }
}