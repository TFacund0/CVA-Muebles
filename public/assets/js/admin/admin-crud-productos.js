/**
 * Gestión de catálogo de productos (back/products/crud_productos.php):
 * inicializa el filtro de pestañas compartido (admin-tab-filter.js) y
 * el botón "Limpiar búsqueda" de la fila de sin-resultados.
 */
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('products-table');
    const initialView = table ? table.getAttribute('data-initial-view') : 'NO';

    initAdminTabFilter({
        rowSelector: '.product-row',
        searchInputId: 'input-search',
        searchAttr: 'data-name',
        selectId: 'select-category',
        selectAttr: 'data-category',
        selectAllValue: 'all',
        viewAttr: 'data-eliminado',
        initialView: initialView, // 'NO' para Activos, 'SI' para Archivados
        activeTabId: 'activos-tab',
        archivedTabId: 'archivados-tab',
        noResultsId: 'no-results-row',
        emptyActiveId: 'empty-active-row',
        emptyArchivedId: 'empty-archive-row',
        filterStatusId: 'filter-status',
        resetButtonId: 'btn-reset',
    });

    const btnResetFiltersEmpty = document.getElementById('btn-reset-filters-empty');
    if (btnResetFiltersEmpty) {
        btnResetFiltersEmpty.addEventListener('click', function() {
            const btnReset = document.getElementById('btn-reset');
            if (btnReset) btnReset.click();
        });
    }
});
