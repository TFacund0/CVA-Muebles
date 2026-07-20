/**
 * Comportamiento del layout admin compartido por todas las pantallas del
 * back-office: toggle del sidebar mobile, fallback del avatar de usuario,
 * envío de acciones POST seguras (submitAction) y confirmaciones genéricas
 * (data-confirm / data-confirm-click).
 */
function toggleSidebar(e) {
    if (e) e.preventDefault();
    const body = document.body;
    body.classList.toggle('sidebar-visible');

    // Bloquear scroll cuando está abierto
    if (body.classList.contains('sidebar-visible')) {
        body.style.overflow = 'hidden';
    } else {
        body.style.overflow = '';
    }
}

/**
 * Envía una acción POST simple al formulario global del layout.
 * Algunas vistas (ver tab_submit_action.php) redefinen esta función con
 * una variante que agrega el parámetro `vista` según la pestaña activa.
 */
function submitAction(url, message) {
    if (confirm(message)) {
        const form = document.getElementById('global-action-form');
        form.action = url;
        form.submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);

    const overlay = document.getElementById('sidebarOverlay');
    const closeBtn = document.getElementById('sidebarClose');

    if (overlay) overlay.addEventListener('click', function() {
        document.body.classList.remove('sidebar-visible');
        document.body.style.overflow = '';
    });

    if (closeBtn) closeBtn.addEventListener('click', function() {
        document.body.classList.remove('sidebar-visible');
        document.body.style.overflow = '';
    });

    // Fallback del avatar de usuario si la imagen de perfil no carga
    const avatarImg = document.getElementById('avatar-user-img');
    const avatarFallback = document.getElementById('avatar-fallback');
    if (avatarImg && avatarFallback) {
        avatarImg.addEventListener('error', function() {
            avatarImg.classList.add('d-none');
            avatarFallback.classList.remove('d-none');
        });
    }
});

// Delegado: cualquier elemento con class="js-submit-action" data-url="..." data-confirm-msg="..."
// dispara submitAction(url, mensaje) en vez de repetir onclick="submitAction(...)" en cada vista.
document.addEventListener('click', function(e) {
    const target = e.target.closest('.js-submit-action');
    if (target) {
        submitAction(target.getAttribute('data-url'), target.getAttribute('data-confirm-msg'));
    }
});

// Confirmación genérica: agregar data-confirm="mensaje" a un <form>
// o data-confirm-click="mensaje" a cualquier elemento clickeable,
// en vez de repetir onsubmit/onclick="return confirm(...)" en cada vista.
document.addEventListener('submit', function(e) {
    const msg = e.target.getAttribute && e.target.getAttribute('data-confirm');
    if (msg && !confirm(msg)) e.preventDefault();
});
document.addEventListener('click', function(e) {
    const target = e.target.closest('[data-confirm-click]');
    if (target && !confirm(target.getAttribute('data-confirm-click'))) {
        e.preventDefault();
    }
});
