<?php 
    $session = session();
    $msg = $session->getFlashdata('success') ?? $session->getFlashdata('error') ?? $session->getFlashdata('fail');
    $type = $session->getFlashdata('success') ? 'success' : ($session->getFlashdata('error') ? 'danger' : 'warning');
    $icon = $session->getFlashdata('success') ? 'bi-check-circle-fill' : ($session->getFlashdata('error') ? 'bi-exclamation-triangle-fill' : 'bi-info-circle-fill');
    
    // Dynamic duration: Base 1s + 60ms per character, clamped between 4s and 12s
    $duration = 4;
    if ($msg) {
        $char_count = mb_strlen($msg);
        $duration = max(4, min(12, round(1 + ($char_count * 0.06))));
    }
?>

<?php if ($msg): ?>
    <div class="toast-container position-fixed bottom-0 end-0 p-4" style="z-index: 9999;">
        <div id="liveToast" class="toast show border-0 rounded-4 shadow-lg animate-slide-up" role="alert" aria-live="assertive" aria-atomic="true" style="--toast-duration: <?= $duration ?>s;">
            <div class="toast-header bg-white border-0 py-3 rounded-top-4">
                <div class="d-flex align-items-center w-100">
                    <div class="rounded-circle bg-<?= $type ?> p-2 d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="bi <?= $icon ?> text-white fs-5"></i>
                    </div>
                    <div class="me-auto">
                        <strong class="text-cva-brown fs-6"><?= $type === 'success' ? '¡Éxito!' : 'Notificación' ?></strong>
                        <div class="text-muted small">CVA Muebles - Sistema de Pedidos</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
            <div class="toast-body bg-white py-3 px-4 rounded-bottom-4 border-top" style="border-top: 1px dashed #eee !important;">
                <p class="mb-0 text-muted fw-500" style="line-height: 1.5; font-size: 0.95rem;"><?= $msg ?></p>
            </div>
            <div class="toast-progress-bar bg-<?= $type ?>" id="toast-progress"></div>
        </div>
    </div>


    <script>
        setTimeout(() => {
            const toast = document.getElementById('liveToast');
            if (toast) {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 500);
            }
        }, <?= $duration * 1000 ?>);
    </script>
<?php endif; ?>
