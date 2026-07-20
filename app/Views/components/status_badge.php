<?php
/**
 * Componente de Badge de Estado de Pedidos
 * @param string $estado El estado del pedido (ej. PENDIENTE, EN_PROCESO, TERMINADO, ENTREGADO, RECHAZADO, SOLICITADO)
 */

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
<span class="badge px-3 py-2 rounded-pill x-small fw-bold badge-status <?= $badge_class ?>">
    <i class="bi <?= $icon ?> me-1"></i>
    <?= $estado ?>
</span>
