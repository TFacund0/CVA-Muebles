<?php

namespace App\Cells;

use App\Models\VentasCabeceraModel;

/**
 * Clase AdminSidebarCell
 *
 * Celda de Vista (View Cell) de CodeIgniter 4 dedicada a la barra lateral de administración.
 * Se encarga de procesar de manera aislada y dinámica la cantidad de pedidos o ventas
 * pendientes en estado "SOLICITUD" para renderizar un indicador de notificación (badge).
 *
 * @package App\Cells
 */
class AdminSidebarCell
{
    /**
     * Renderiza dinámicamente un badge HTML con la cantidad de pedidos pendientes de aprobación.
     *
     * Si la cantidad es mayor a cero, genera una insignia roja con animación de pulso infinito.
     * Si no hay elementos, retorna una cadena vacía.
     *
     * @return string Código HTML del badge o cadena vacía.
     */
    public function renderSolicitadosBadge(): string
    {
        $ventasModel = new VentasCabeceraModel();
        $cant_solicitados = $ventasModel->where('estado_aprobacion', 'SOLICITUD')->countAllResults();

        if ($cant_solicitados > 0) {
            return '<span class="badge rounded-pill bg-danger ms-auto shadow-sm animate__animated animate__pulse animate__infinite" style="font-size: 0.65rem;">' . esc($cant_solicitados) . '</span>';
        }
        
        return '';
    }
}
