/**
 * Nuevo pedido personalizado (back/sales/nuevo_pedido_personalizado.php):
 * dispara el selector de archivo al hacer click en la zona de preview y
 * muestra el nombre del archivo elegido.
 */
document.addEventListener('DOMContentLoaded', function() {
    const trigger = document.getElementById('ref-img-trigger');
    const refImg = document.getElementById('ref_img');
    const fileName = document.getElementById('file-name');

    if (trigger && refImg) {
        trigger.addEventListener('click', function() {
            refImg.click();
        });
    }

    if (refImg && fileName) {
        refImg.addEventListener('change', function() {
            const name = this.files[0] ? this.files[0].name : '';
            fileName.innerHTML = name ? '<i class="bi bi-file-earmark-check me-1"></i> Seleccionado: ' + name : '';
        });
    }
});
