(function() {
    // El offcanvas siempre abre desde la derecha (offcanvas-end, fijo en el
    // HTML): tanto la hamburguesa mobile como el boton de perfil desktop
    // viven del lado derecho del navbar, asi que ya no hace falta alternar
    // el lado segun el ancho de pantalla.

    // Fallback del avatar de perfil: si la imagen no carga, se oculta
    // y se muestra el ícono de persona genérico que la acompaña.
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.js-avatar-img').forEach(function(img) {
            img.addEventListener('error', function() {
                img.style.display = 'none';
                if (img.nextElementSibling) {
                    img.nextElementSibling.style.display = 'block';
                }
            });
        });
    });
})();
