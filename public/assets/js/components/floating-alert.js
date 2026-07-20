(function() {
    function armToast(toast) {
        const duration = parseFloat(toast.dataset.toastDuration) || 4;
        toast.style.setProperty('--toast-duration', duration + 's');

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 500);
        }, duration * 1000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const toast = document.getElementById('liveToast');
        if (toast) armToast(toast);
    });

    // Muestra un toast igual al renderizado por el servidor, sin recargar la página.
    window.showToast = function(message, type) {
        type = type === 'error' ? 'danger' : (type || 'success');
        const icon = type === 'success' ? 'bi-check-circle-fill' : (type === 'danger' ? 'bi-exclamation-triangle-fill' : 'bi-info-circle-fill');
        const title = type === 'success' ? '¡Éxito!' : 'Notificación';
        const duration = Math.max(4, Math.min(12, Math.round(1 + (message.length * 0.06))));

        let container = document.querySelector('.toast-container-cva');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container position-fixed bottom-0 end-0 p-4 toast-container-cva';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'toast show border-0 rounded-4 shadow-lg animate-slide-up';
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        toast.dataset.toastDuration = duration;
        toast.innerHTML = `
            <div class="toast-header bg-white border-0 py-3 rounded-top-4">
                <div class="d-flex align-items-center w-100">
                    <div class="rounded-circle bg-${type} p-2 d-flex align-items-center justify-content-center me-3 toast-icon-circle">
                        <i class="bi ${icon} text-white fs-5"></i>
                    </div>
                    <div class="me-auto">
                        <strong class="text-cva-brown fs-6">${title}</strong>
                        <div class="text-muted small">CVA Muebles - Sistema de Pedidos</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
            <div class="toast-body bg-white py-3 px-4 rounded-bottom-4 border-top toast-body-dashed">
                <p class="mb-0 text-muted fw-500 toast-message-text"></p>
            </div>
            <div class="toast-progress-bar bg-${type}" id="toast-progress"></div>
        `;
        toast.querySelector('.toast-message-text').textContent = message;
        toast.querySelector('.btn-close').addEventListener('click', () => toast.remove());

        container.appendChild(toast);
        armToast(toast);
    };
})();
