/**
 * Aplica el ancho de las barras de progreso cuyo valor es dinámico
 * (calculado en PHP) a través de data-progress, ya que el CSP no
 * permite style="width: ...%" inline. Compartido por estadisticas.php
 * y gestion_pedido_admin.php.
 */
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.js-progress-width').forEach(function(bar) {
        const progress = bar.getAttribute('data-progress');
        if (progress !== null) {
            bar.style.width = progress + '%';
        }
    });
});
