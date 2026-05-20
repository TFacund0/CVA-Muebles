<?php

namespace App\Cells;

use App\Models\VentasCabeceraModel;

class AdminSidebarCell
{
    public function renderSolicitadosBadge()
    {
        $ventasModel = new VentasCabeceraModel();
        $cant_solicitados = $ventasModel->where('estado_aprobacion', 'SOLICITUD')->countAllResults();

        if ($cant_solicitados > 0) {
            return '<span class="badge rounded-pill bg-danger ms-auto shadow-sm animate__animated animate__pulse animate__infinite" style="font-size: 0.65rem;">' . esc($cant_solicitados) . '</span>';
        }
        
        return '';
    }
}
