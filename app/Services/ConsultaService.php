<?php

namespace App\Services;

use App\Models\ConsultaModel;

/**
 * Servicio para manejar la lógica de las consultas (mensajes de contacto).
 */
class ConsultaService
{
    protected $consultaModel;

    public function __construct(?ConsultaModel $consultaModel = null)
    {
        $this->consultaModel = $consultaModel ?? new ConsultaModel();
    }

    /**
     * Obtiene todas las consultas con estadísticas procesadas.
     */
    public function getConsultasConStats()
    {
        $consultas = $this->consultaModel->getAllOrdenadas();
        $currentMonth = date('m');
        $currentYear = date('Y');

        $counts = [
            'total'     => count($consultas),
            'mensuales' => 0,
            'activos'   => 0,
            'presupuestos' => 0
        ];

        foreach ($consultas as &$c) {
            $cDate = strtotime($c['fecha']);
            if (date('m', $cDate) == $currentMonth && date('Y', $cDate) == $currentYear) {
                $counts['mensuales']++;
            }

            if ($c['activo'] == 'SI') $counts['activos']++;
            if (stripos($c['asunto'], 'presupuesto') !== false) {
                $counts['presupuestos']++;
            }

            $c['search_data'] = strtolower(esc($c['nombre'] . ' ' . $c['apellido'] . ' ' . $c['email'] . ' ' . $c['asunto']));
        }

        return [
            'consultas' => $consultas,
            'counts'    => $counts
        ];
    }

    /**
     * Registra una nueva consulta.
     */
    public function registrar($data)
    {
        $data['fecha'] = date('Y-m-d H:i:s');
        $data['activo'] = 'SI';
        
        if ($this->consultaModel->insert($data) === false) {
            return ['status' => 'error', 'message' => 'Errores de validación: ' . implode(', ', $this->consultaModel->errors())];
        }
        
        return ['status' => 'success', 'message' => 'Consulta enviada correctamente.'];
    }

    /**
     * Marca una consulta como inactiva.
     */
    public function desactivar($id)
    {
        return $this->consultaModel->update($id, ['activo' => 'NO']);
    }

    /**
     * Restaura una consulta a estado activo (Pendiente).
     */
    public function restaurar($id)
    {
        return $this->consultaModel->update($id, ['activo' => 'SI']);
    }

    /**
     * Elimina físicamente una consulta de la base de datos.
     */
    public function eliminarPermanente($id)
    {
        return $this->consultaModel->delete($id);
    }

    /**
     * Cuenta las consultas activas (pendientes de responder).
     */
    public function countActivas()
    {
        return $this->consultaModel->where('activo', 'SI')->countAllResults();
    }
}
