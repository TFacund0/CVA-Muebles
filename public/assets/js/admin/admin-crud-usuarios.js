/**
 * Gestión de usuarios (back/users/crud_usuarios.php): inicializa el
 * filtro de pestañas compartido (admin-tab-filter.js).
 */
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('users-table');
    const initialView = table ? table.getAttribute('data-initial-view') : 'NO';

    initAdminTabFilter({
        rowSelector: '.user-row',
        searchInputId: 'input-search',
        searchAttr: 'data-search',
        selectId: 'select-perfil',
        selectAttr: 'data-perfil',
        selectAllValue: 'all',
        viewAttr: 'data-baja',
        initialView: initialView, // 'NO' para Activos, 'SI' para Suspendidos
        activeTabId: 'activos-tab',
        archivedTabId: 'suspendidos-tab',
        noResultsId: 'no-results-row',
        emptyActiveId: 'empty-active-row',
        emptyArchivedId: 'empty-suspended-row',
        filterStatusId: 'filter-status',
        resetButtonId: 'btn-reset',
    });
});
