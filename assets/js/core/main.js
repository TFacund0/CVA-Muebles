/**
 * CVA Muebles - Core Scripts (Frontend Global)
 * Contiene la lógica global para layouts y componentes compartidos.
 */

// Inicialización de componentes cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    initOffcanvasNavbar();
    initFloatingAlerts();
});

/**
 * Ajusta la posición del menú lateral (Offcanvas) según el tamaño de la pantalla
 */
function initOffcanvasNavbar() {
    function adjustOffcanvasPosition() {
        const offcanvas = document.getElementById('offcanvasNavbar');
        if (!offcanvas) return;
        
        if (window.innerWidth < 992) {
            offcanvas.classList.remove('offcanvas-end');
            offcanvas.classList.add('offcanvas-start');
        } else {
            offcanvas.classList.remove('offcanvas-start');
            offcanvas.classList.add('offcanvas-end');
        }
    }
    
    window.addEventListener('resize', adjustOffcanvasPosition);
    adjustOffcanvasPosition(); // Ejecución inicial
}

/**
 * Inicializa y maneja el cierre automático de las alertas flotantes globales (Toasts)
 */
function initFloatingAlerts() {
    const toast = document.getElementById('liveToast');
    if (toast) {
        // Obtenemos la duración del atributo data-duration
        const durationMs = parseInt(toast.getAttribute('data-duration'), 10) || 4000;
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 500);
        }, durationMs);
    }
}

/**
 * Función global para enviar formularios de acción seguros (CSRF protected)
 * Requiere que exista un formulario con id "global-action-form"
 * @param {string} url - URL de destino para la acción POST
 * @param {string} message - Mensaje de confirmación
 */
window.submitAction = function(url, message) {
    if (confirm(message)) {
        const form = document.getElementById('global-action-form');
        if (form) {
            form.action = url;
            form.submit();
        } else {
            console.error('El formulario global-action-form no existe en el DOM.');
        }
    }
};
