/**
 * CVA Muebles - Admin Core Scripts
 * Lógica global para el layout y componentes del panel de administración.
 */

document.addEventListener('DOMContentLoaded', function() {
    initSidebarToggle();
});

/**
 * Inicializa los eventos para alternar el menú lateral (Sidebar) del panel de administración
 */
function initSidebarToggle() {
    const overlay = document.getElementById('sidebarOverlay');
    const closeBtn = document.getElementById('sidebarClose');
    
    // Asignar función global
    window.toggleSidebar = function(e) {
        if(e) e.preventDefault();
        const body = document.body;
        body.classList.toggle('sidebar-visible');
        
        // Bloquear scroll cuando está abierto
        if (body.classList.contains('sidebar-visible')) {
            body.style.overflow = 'hidden';
        } else {
            body.style.overflow = '';
        }
    };

    if (overlay) {
        overlay.addEventListener('click', function() {
            document.body.classList.remove('sidebar-visible');
            document.body.style.overflow = '';
        });
    }
    
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            document.body.classList.remove('sidebar-visible');
            document.body.style.overflow = '';
        });
    }
}
