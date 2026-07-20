(function() {
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
    window.addEventListener('DOMContentLoaded', adjustOffcanvasPosition);

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
