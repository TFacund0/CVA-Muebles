<?php 
/**
 * Componente de Alertas Inline (Inline Alerts)
 *
 * Muestra alertas estáticas/inline utilizando clases de Bootstrap.
 * Recupera mensajes flash temporales de la sesión activa para notificar al usuario
 * sobre el éxito, error o advertencias en formularios o flujos transaccionales.
 *
 * Datos de Sesión Flash Esperados:
 * - session()->getFlashdata('success'): Mensaje de éxito de la operación.
 * - session()->getFlashdata('error'): Mensaje de error/excepción crítica.
 * - session()->getFlashdata('fail'): Mensaje de advertencia o validación fallida.
 */
$session = session();
?>

<?php if ($session->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4 animate-slide-down" role="alert" style="background: rgba(40, 167, 69, 0.1); border: 1px solid rgba(40, 167, 69, 0.2) !important; color: #155724;">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill fs-4 me-3"></i>
            <div>
                <strong>¡Éxito!</strong> <?= $session->getFlashdata('success') ?>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($session->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4 animate-slide-down" role="alert" style="background: rgba(220, 53, 69, 0.1); border: 1px solid rgba(220, 53, 69, 0.2) !important; color: #721c24;">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
            <div>
                <strong>Error:</strong> <?= $session->getFlashdata('error') ?>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($session->getFlashdata('fail')): ?>
    <div class="alert alert-warning alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4 animate-slide-down" role="alert" style="background: rgba(255, 193, 7, 0.1); border: 1px solid rgba(255, 193, 7, 0.2) !important; color: #856404;">
        <div class="d-flex align-items-center">
            <i class="bi bi-info-circle-fill fs-4 me-3"></i>
            <div>
                <strong>Atención:</strong> <?= $session->getFlashdata('fail') ?>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
