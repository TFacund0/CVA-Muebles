<?php

namespace App\Services;

use App\Models\ConsultaModel;

/**
 * Class ConsultaService
 *
 * Servicio encargado de la gestión y procesamiento de la lógica de negocio asociada
 * a las consultas de clientes (mensajes de contacto y solicitudes de presupuestos),
 * proveyendo filtrado, estadísticas de actividad y operaciones de ciclo de vida (activo/inactivo/baja).
 *
 * @package App\Services
 */
class ConsultaService
{
    /**
     * @var ConsultaModel Modelo para interactuar con la tabla de consultas en la base de datos.
     */
    protected $consultaModel;

    /**
     * Constructor del servicio.
     *
     * Inicializa la instancia del modelo de acceso a datos para las consultas.
     */
    public function __construct()
    {
        $this->consultaModel = new ConsultaModel();
    }

    /**
     * Recupera todas las consultas registradas en orden descendente por fecha de envío,
     * calculando indicadores estadísticos (mensuales, activas, presupuestos) para el panel de administración.
     *
     * @return array Resumen conteniendo el listado de consultas ('consultas') y métricas ('counts').
     */
    public function getConsultasConStats()
    {
        $consultas = $this->consultaModel->orderBy('fecha', 'DESC')->findAll();
        $currentMonth = date('m');
        $currentYear = date('Y');

        $counts = [
            'total'        => count($consultas),
            'mensuales'    => 0,
            'activos'      => 0,
            'presupuestos' => 0
        ];

        foreach ($consultas as &$c) {
            $cDate = strtotime($c['fecha']);
            if (date('m', $cDate) == $currentMonth && date('Y', $cDate) == $currentYear) {
                $counts['mensuales']++;
            }

            if ($c['activo'] == 'SI') {
                $counts['activos']++;
            }
            
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
     * Procesa y registra un nuevo mensaje de consulta en el sistema.
     * Inserta por defecto el estado activo 'SI' y la marca de tiempo actual del servidor.
     *
     * @param array $data Datos de la consulta remitidos por el usuario.
     * 
     * @return array Resumen de estado ('status' => 'success'|'error', 'message' => string).
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
     * Realiza la baja lógica de una consulta marcándola como inactiva ('NO') en la base de datos.
     *
     * @param int|string $id Identificador de la consulta.
     * 
     * @return bool|int|string Retorna el resultado de la actualización de la consulta.
     */
    public function desactivar($id)
    {
        return $this->consultaModel->update($id, ['activo' => 'NO']);
    }

    /**
     * Restaura una consulta desactivada a estado activo ('SI') o pendiente de atención.
     *
     * @param int|string $id Identificador de la consulta.
     * 
     * @return bool|int|string Retorna el resultado de la actualización de la consulta.
     */
    public function restaurar($id)
    {
        return $this->consultaModel->update($id, ['activo' => 'SI']);
    }

    /**
     * Procesa la eliminación física permanente de una consulta de la base de datos.
     *
     * @param int|string $id Identificador de la consulta.
     * 
     * @return bool|int|string Retorna el resultado de la operación de eliminación física.
     */
    public function eliminarPermanente($id)
    {
        return $this->consultaModel->delete($id);
    }

    /**
     * Cuenta el número total de consultas en estado activo ('SI') que aún requieren atención.
     *
     * @return int Cantidad de consultas activas/pendientes.
     */
    public function countActivas()
    {
        return $this->consultaModel->where('activo', 'SI')->countAllResults();
    }
}
