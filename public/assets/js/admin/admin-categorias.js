/**
 * Gestión de categorías (back/products/crud_categorias.php): prepara el
 * modal de edición con los datos de la categoría seleccionada.
 */
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.js-editar-categoria').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = btn.getAttribute('data-id');
            const descripcion = btn.getAttribute('data-descripcion');
            const form = document.getElementById('formEditar');
            document.getElementById('descEditar').value = descripcion;
            form.action = form.getAttribute('data-base-url') + id;
        });
    });
});
