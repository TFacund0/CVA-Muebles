/**
 * Ver factura de usuario (back/sales/ver_factura_usuario.php): muestra/oculta
 * la tarjeta de preview de cada producto al hacer click en su badge.
 */
function togglePreview(id, element) {
    document.querySelectorAll('.product-preview-card').forEach(card => {
        if (card.id !== id) card.classList.remove('active');
    });
    const card = document.getElementById(id);
    card.classList.toggle('active');
    document.addEventListener('click', function close(e) {
        if (!card.contains(e.target) && !element.contains(e.target)) {
            card.classList.remove('active');
            document.removeEventListener('click', close);
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.js-toggle-preview').forEach(function(trigger) {
        trigger.addEventListener('click', function() {
            togglePreview(trigger.getAttribute('data-preview-target'), trigger);
        });
    });
});
