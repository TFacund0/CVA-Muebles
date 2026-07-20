/**
 * Mis Compras / historial de pedidos (back/sales/vistaCompras.php):
 * sistema de filtrado y ordenamiento de las tarjetas de pedido.
 */
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.btn-filter-artisan');
    const sortSelect = document.getElementById('sort-purchases');
    const container = document.getElementById('purchases-list');
    const cards = Array.from(document.querySelectorAll('.purchase-card'));
    const noResults = document.getElementById('no-results-purchases');

    if (cards.length === 0) return;

    let currentFilter = 'todos';

    function filterAndSort() {
        let visibleCount = 0;

        // 1. Filtrar
        cards.forEach(card => {
            const status = card.dataset.status;

            let matchesFilter = false;
            if (currentFilter === 'todos') {
                matchesFilter = true;
            } else if (currentFilter === 'entregado') {
                matchesFilter = (status === 'entregado' || status === 'terminado');
            } else {
                matchesFilter = (status === currentFilter);
            }

            if (matchesFilter) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Mostrar / ocultar estado vacío
        if (visibleCount === 0) {
            noResults.classList.remove('d-none');
        } else {
            noResults.classList.add('d-none');
        }

        // 2. Ordenar
        const sortBy = sortSelect ? sortSelect.value : 'recent';
        const sorted = [...cards].sort((a, b) => {
            const priceA = parseFloat(a.dataset.total) || 0;
            const priceB = parseFloat(b.dataset.total) || 0;

            const dateA = parseInt(a.dataset.fecha) || 0;
            const dateB = parseInt(b.dataset.fecha) || 0;

            if (sortBy === 'recent') {
                return dateB - dateA; // Más reciente primero
            } else if (sortBy === 'oldest') {
                return dateA - dateB; // Más antiguo primero
            } else if (sortBy === 'high-price') {
                return priceB - priceA; // Mayor precio primero
            } else if (sortBy === 'low-price') {
                return priceA - priceB; // Menor precio primero
            }
            return 0;
        });

        // Reordenar en el DOM
        sorted.forEach(el => {
            if (container) {
                container.appendChild(el);
            }
        });
    }

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            filterAndSort();
        });
    });

    if (sortSelect) {
        sortSelect.addEventListener('change', filterAndSort);
    }

    // Inicializar ordenamiento y filtrado por defecto
    filterAndSort();
});
