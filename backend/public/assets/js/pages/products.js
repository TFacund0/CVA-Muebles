/**
 * CVA Muebles - Products Catalog Scripts
 * Maneja la lógica de filtrado de productos por categoría en el catálogo principal.
 */

document.addEventListener('DOMContentLoaded', function() {
    initProductFilters();
});

function initProductFilters() {
    const botones = document.querySelectorAll('.filtro-categoria');
    const items = document.querySelectorAll('#lista-productos > div');

    if (botones.length === 0 || items.length === 0) return;

    botones.forEach(btn => {
        btn.addEventListener('click', () => {
            // Manejo de clase activa
            botones.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const categoria = btn.dataset.categoria.toLowerCase();

            items.forEach(prod => {
                const catProd = prod.dataset.categorias ? prod.dataset.categorias.toLowerCase() : '';
                if (categoria === 'todos' || catProd === categoria) {
                    prod.style.display = 'block';
                } else {
                    prod.style.display = 'none';
                }
            });
        });
    });
}
