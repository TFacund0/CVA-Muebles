<?php
/**
 * Componente de Insignias de Estado (Status Badge Component)
 *
 * Renderiza etiquetas visuales autogestionadas con Bootstrap para representar los diferentes
 * estados de un pedido o venta dentro de la cola de trabajo del taller de CVA Muebles.
 *
 * @var string $estado El estado del pedido.
 *                     Estados soportados:
 *                     - 'PENDIENTE' (Amarillo/Espera de inicio de obra)
 *                     - 'EN_PROCESO' (Marrón-Proceso/En fabricación en el taller)
 *                     - 'TERMINADO' (Verde/Mueble finalizado listo para retirar/enviar)
 *                     - 'ENTREGADO' (Negro/Mueble ya recibido por el comprador)
 *                     - 'RECHAZADO' (Rojo/Pedido cancelado o presupuesto no aceptado)
 *                     - 'SOLICITADO' (Azul-Info/Presupuesto enviado bajo aprobación administrativa)
 */
?>
$estado = strtoupper($estado ?? 'PENDIENTE');

$badge_class = "bg-light text-muted border";
$icon = "bi-clock";

if ($estado == 'PENDIENTE') {
    $badge_class = "bg-warning-soft text-warning border-warning";
    $icon = "bi-hourglass-split";
} elseif ($estado == 'EN_PROCESO') {
    $badge_class = "bg-proceso-soft text-proceso border-proceso";
    $icon = "bi-tools";
} elseif ($estado == 'TERMINADO') {
    $badge_class = "bg-success-soft text-success border-success";
    $icon = "bi-check-all";
} elseif ($estado == 'ENTREGADO') {
    $badge_class = "bg-dark text-white";
    $icon = "bi-truck";
} elseif ($estado == 'RECHAZADO') {
    $badge_class = "bg-danger-soft text-danger border-danger";
    $icon = "bi-x-circle";
} elseif ($estado == 'SOLICITADO') {
    $badge_class = "bg-info-soft text-info border-info";
    $icon = "bi-inbox";
}
?>
<span class="badge px-3 py-2 rounded-pill x-small fw-bold <?= $badge_class ?>" style="min-width: 100px;">
    <i class="bi <?= $icon ?> me-1"></i>
    <?= $estado ?>
</span>
