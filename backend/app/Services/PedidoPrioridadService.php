<?php

namespace App\Services;

use App\Models\VentasCabeceraModel;

/**
 * Class PedidoPrioridadService
 *
 * Extraído de VentasService (que mezclaba creación de pedidos, pagos, estados,
 * reportes y esta cola de prioridad en un solo archivo de 569 líneas). Aísla
 * específicamente el reordenamiento de la cola de producción activa del taller.
 *
 * VentasService lo sigue exponiendo vía subirPrioridad()/bajarPrioridad() para no
 * romper a los controladores que ya lo consumen (delegación interna, misma API pública).
 *
 * @package App\Services
 */
class PedidoPrioridadService
{
    protected VentasCabeceraModel $ventasModel;

    public function __construct()
    {
        $this->ventasModel = new VentasCabeceraModel();
    }

    /**
     * Incrementa la prioridad constructiva de un pedido en la cola de producción activa
     * de los artesanos en el taller.
     * Intercambia la prioridad con el pedido inmediatamente superior.
     *
     * @param int|string $venta_id Identificador único del pedido.
     *
     * @return void
     */
    public function subir($venta_id)
    {
        $ventas_activas = $this->ventasModel->getVentasActivas();

        // Encontrar la posición del pedido en la lista de prioridades activas
        $index = -1;
        for ($i = 0; $i < count($ventas_activas); $i++) {
            if ($ventas_activas[$i]['id'] == $venta_id) {
                $index = $i;
                break;
            }
        }

        if ($index > 0) {
            $item_current = $ventas_activas[$index];
            $item_above   = $ventas_activas[$index - 1];

            $p_current = (int) ($item_current['prioridad'] ?? 0);
            $p_above   = (int) ($item_above['prioridad'] ?? 0);

            if ($p_current != $p_above) {
                // Intercambiar las prioridades para reordenar la cola
                $this->ventasModel->update($item_current['id'], ['prioridad' => $p_above]);
                $this->ventasModel->update($item_above['id'], ['prioridad' => $p_current]);
            } else {
                // Si coinciden en prioridad, subimos el pedido actual una unidad arriba
                $this->ventasModel->update($item_current['id'], ['prioridad' => $p_above + 1]);
            }
        } elseif ($index == 0 && !empty($ventas_activas)) {
            // Caso en el que el pedido ya encabeza la cola activa; se eleva preventivamente su puntuación
            $item_current = $ventas_activas[0];
            $p_current = (int) ($item_current['prioridad'] ?? 0);
            $this->ventasModel->update($item_current['id'], ['prioridad' => $p_current + 1]);
        }
    }

    /**
     * Disminuye la prioridad constructiva de un pedido en la cola de producción activa
     * de los artesanos en el taller.
     * Intercambia la prioridad con el pedido inmediatamente inferior.
     *
     * @param int|string $venta_id Identificador único del pedido.
     *
     * @return void
     */
    public function bajar($venta_id)
    {
        $ventas_activas = $this->ventasModel->getVentasActivas();

        // Encontrar la posición del pedido en la lista de prioridades activas
        $index = -1;
        for ($i = 0; $i < count($ventas_activas); $i++) {
            if ($ventas_activas[$i]['id'] == $venta_id) {
                $index = $i;
                break;
            }
        }

        if ($index != -1 && $index < count($ventas_activas) - 1) {
            $item_current = $ventas_activas[$index];
            $item_below   = $ventas_activas[$index + 1];

            $p_current = (int) ($item_current['prioridad'] ?? 0);
            $p_below   = (int) ($item_below['prioridad'] ?? 0);

            if ($p_current != $p_below) {
                // Intercambiar las prioridades para reordenar la cola
                $this->ventasModel->update($item_current['id'], ['prioridad' => $p_below]);
                $this->ventasModel->update($item_below['id'], ['prioridad' => $p_current]);
            } else {
                // Si coinciden en prioridad, bajamos el pedido inferior asignándole la del actual + 1
                $this->ventasModel->update($item_below['id'], ['prioridad' => $p_current + 1]);
            }
        } elseif ($index == count($ventas_activas) - 1 && $index != -1) {
            // Caso en el que el pedido ya está al final de la cola activa; se disminuye preventivamente
            $item_current = $ventas_activas[$index];
            $p_current = (int) ($item_current['prioridad'] ?? 0);
            $this->ventasModel->update($item_current['id'], ['prioridad' => $p_current - 1]);
        }
    }
}
