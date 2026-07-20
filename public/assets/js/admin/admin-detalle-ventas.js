/**
 * Gestión de pedidos (back/sales/detalleVentas.php): filtro de KPI cards,
 * búsqueda/filtro de la tabla de pedidos activos y reordenamiento
 * asíncrono de prioridad.
 */
function filterByStatus(status) {
    const tabEl = document.getElementById('activos-tab');
    if (tabEl) {
        tabEl.click();
    }
    const selectStatus = document.getElementById('select-status');
    selectStatus.value = status;
    selectStatus.dispatchEvent(new Event('change'));
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.js-filter-status').forEach(function(card) {
        card.addEventListener('click', function() {
            filterByStatus(card.getAttribute('data-status'));
        });
    });

    const inputSearch = document.getElementById('input-search');
    const selectStatus = document.getElementById('select-status');
    const rows = document.querySelectorAll('.order-row');
    const noResults = document.getElementById('no-results-row');
    const filterStatus = document.getElementById('filter-status');
    const btnReset = document.getElementById('btn-reset');

    if (!inputSearch || !selectStatus) return;

    function filterOrders() {
        const searchTerm = inputSearch.value.toLowerCase();
        const statusFilter = selectStatus.value;
        let visibleCount = 0;

        filterStatus.style.opacity = '1';

        rows.forEach(row => {
            const searchData = row.getAttribute('data-search');
            const estado = row.getAttribute('data-estado');

            const matchesSearch = searchData.includes(searchTerm);
            const matchesStatus = (statusFilter === 'ALL' || estado === statusFilter);

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        noResults.style.display = (visibleCount === 0) ? '' : 'none';

        setTimeout(() => {
            filterStatus.style.opacity = '0';
        }, 300);
    }

    inputSearch.addEventListener('input', filterOrders);
    selectStatus.addEventListener('change', filterOrders);

    btnReset.addEventListener('click', function() {
        inputSearch.value = '';
        selectStatus.value = 'ALL';
        filterOrders();
    });

    // Check for initial filter in URL
    const urlParams = new URLSearchParams(window.location.search);
    const initialStatus = urlParams.get('estado');
    if (initialStatus) {
        selectStatus.value = initialStatus;
    }

    // Lógica de reordenamiento asíncrono y UI optimista para las flechitas de prioridad
    document.querySelectorAll('.btn-prioridad-subir, .btn-prioridad-bajar').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();

            const url = this.getAttribute('href');
            const currentRow = this.closest('.order-row');
            if (!currentRow) return;

            const isSubir = this.classList.contains('btn-prioridad-subir');
            let targetSibling = null;

            // Buscar el vecino visible (arriba o abajo)
            let sibling = isSubir ? currentRow.previousElementSibling : currentRow.nextElementSibling;
            while (sibling) {
                if (sibling.classList.contains('order-row') && sibling.style.display !== 'none') {
                    targetSibling = sibling;
                    break;
                }
                sibling = isSubir ? sibling.previousElementSibling : sibling.nextElementSibling;
            }

            if (targetSibling) {
                // Animación suave de fade-out temporal para el swap visual
                currentRow.style.transition = 'background-color 0.3s ease';
                targetSibling.style.transition = 'background-color 0.3s ease';
                currentRow.style.backgroundColor = '#fdf8f0';
                targetSibling.style.backgroundColor = '#fdf8f0';

                // Realizar el intercambio en el DOM de forma optimista
                if (isSubir) {
                    currentRow.parentNode.insertBefore(currentRow, targetSibling);
                } else {
                    currentRow.parentNode.insertBefore(targetSibling, currentRow);
                }

                // Limpiar colores de fondo después de la animación
                setTimeout(() => {
                    currentRow.style.backgroundColor = '';
                    targetSibling.style.backgroundColor = '';
                }, 800);

                // Ejecutar la petición asíncrona en segundo plano sin recargar
                fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al actualizar prioridad en el servidor');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Revertir el intercambio si falla
                    if (isSubir) {
                        currentRow.parentNode.insertBefore(targetSibling, currentRow);
                    } else {
                        currentRow.parentNode.insertBefore(currentRow, targetSibling);
                    }
                    alert('No se pudo guardar el cambio de prioridad. Por favor, intenta de nuevo.');
                });
            }
        });
    });

    // Inicializar
    filterOrders();
});
