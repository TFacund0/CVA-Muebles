/**
 * Editar producto (back/products/editar_producto.php): preview de la
 * imagen principal al seleccionar un nuevo archivo.
 */
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('imagen-input');
    if (!input) return;

    input.addEventListener('change', function(event) {
        const preview = document.getElementById('main-preview');
        const indicator = document.getElementById('new-badge-indicator');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.add('animate__animated', 'animate__pulse');
                if (indicator) indicator.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    });
});
