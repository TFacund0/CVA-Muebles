/**
 * Script global del sitio (layout/main.php).
 * Contiene utilidades usadas por múltiples vistas, como el envío
 * programático del formulario global de acciones (confirmaciones
 * de borrado, etc.).
 */
function submitAction(url, message) {
    if (confirm(message)) {
        const form = document.getElementById('global-action-form');
        form.action = url;
        form.submit();
    }
}

// Intercepta los forms de "Agregar al carrito" (tarjetas de producto, detalle, favoritos)
// para evitar la recarga completa de la página.
document.addEventListener('submit', function(e) {
    const form = e.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (!form.action.includes('/carrito/add')) return;

    e.preventDefault();
    const button = form.querySelector('button[type="submit"]');
    const originalHtml = button ? button.innerHTML : null;
    if (button) {
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Agregando...';
    }

    fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (typeof showToast === 'function') {
                showToast(data.message, data.status === 'error' ? 'error' : 'success');
            }
            if (data.status !== 'error' && typeof data.totalItems !== 'undefined') {
                document.querySelectorAll('a[href*="/muestro"]').forEach(cartLink => {
                    let badge = cartLink.querySelector('.navbar-cart-badge');
                    if (!badge) {
                        badge = document.createElement('span');
                        badge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger navbar-cart-badge';
                        cartLink.appendChild(badge);
                    }
                    badge.textContent = data.totalItems;
                });
            }
        })
        .catch(() => {
            if (typeof showToast === 'function') {
                showToast('No se pudo agregar el producto al carrito.', 'error');
            }
        })
        .finally(() => {
            if (button) {
                button.disabled = false;
                button.innerHTML = originalHtml;
            }
        });
});
