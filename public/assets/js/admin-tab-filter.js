/**
 * Filtro de tabla admin con 2 pestañas (Activos/Archivados) + buscador +
 * select opcional. Extraído porque crud_productos.php y crud_usuarios.php
 * tenían la misma lógica copiada con distintos nombres de variable/ID.
 *
 * config = {
 *   rowSelector: '.product-row',
 *   searchInputId: 'input-search',
 *   searchAttr: 'data-name',           // atributo del row a comparar con el buscador
 *   selectId: 'select-category',       // opcional
 *   selectAttr: 'data-category',       // opcional, atributo del row a comparar con el select
 *   selectAllValue: 'all',
 *   viewAttr: 'data-eliminado',        // atributo del row que indica Activo/Archivado
 *   initialView: 'NO',                 // 'NO' = Activos, 'SI' = Archivados
 *   activeTabId: 'activos-tab',
 *   archivedTabId: 'archivados-tab',
 *   noResultsId: 'no-results-row',
 *   emptyActiveId: 'empty-active-row',
 *   emptyArchivedId: 'empty-archive-row',
 *   filterStatusId: 'filter-status',
 *   resetButtonId: 'btn-reset',
 * }
 */
function initAdminTabFilter(config) {
    const inputSearch = document.getElementById(config.searchInputId);
    const select = config.selectId ? document.getElementById(config.selectId) : null;
    const activosTab = document.getElementById(config.activeTabId);
    const archivadosTab = document.getElementById(config.archivedTabId);
    const rows = document.querySelectorAll(config.rowSelector);
    const noResults = document.getElementById(config.noResultsId);
    const emptyActive = document.getElementById(config.emptyActiveId);
    const emptyArchived = document.getElementById(config.emptyArchivedId);
    const filterStatus = config.filterStatusId ? document.getElementById(config.filterStatusId) : null;
    const btnReset = config.resetButtonId ? document.getElementById(config.resetButtonId) : null;

    let currentView = config.initialView;

    function filter() {
        const searchTerm = inputSearch.value.toLowerCase();
        const selectTerm = select ? select.value : null;
        let visibleCount = 0;
        let totalInCurrentView = 0;

        if (filterStatus) filterStatus.style.opacity = '1';

        rows.forEach(row => {
            const searchValue = row.getAttribute(config.searchAttr) || '';
            const view = row.getAttribute(config.viewAttr);

            const isCorrectView = (view === currentView);
            const matchesSearch = searchValue.includes(searchTerm);
            const matchesSelect = !select || selectTerm === config.selectAllValue || row.getAttribute(config.selectAttr) === selectTerm;

            if (isCorrectView) {
                totalInCurrentView++;
                if (matchesSearch && matchesSelect) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            } else {
                row.style.display = 'none';
            }
        });

        if (noResults) noResults.style.display = (visibleCount === 0 && totalInCurrentView > 0) ? '' : 'none';
        if (emptyActive) emptyActive.style.display = (totalInCurrentView === 0 && currentView === 'NO') ? '' : 'none';
        if (emptyArchived) emptyArchived.style.display = (totalInCurrentView === 0 && currentView === 'SI') ? '' : 'none';

        if (filterStatus) {
            setTimeout(() => { filterStatus.style.opacity = '0'; }, 300);
        }
    }

    function switchTab(view) {
        currentView = view;
        if (currentView === 'NO') {
            activosTab.classList.add('active');
            activosTab.setAttribute('aria-selected', 'true');
            archivadosTab.classList.remove('active');
            archivadosTab.setAttribute('aria-selected', 'false');
        } else {
            archivadosTab.classList.add('active');
            archivadosTab.setAttribute('aria-selected', 'true');
            activosTab.classList.remove('active');
            activosTab.setAttribute('aria-selected', 'false');
        }
        filter();
    }

    if (activosTab) activosTab.addEventListener('click', () => switchTab('NO'));
    if (archivadosTab) archivadosTab.addEventListener('click', () => switchTab('SI'));

    inputSearch.addEventListener('input', filter);
    if (select) select.addEventListener('change', filter);

    if (btnReset) {
        btnReset.addEventListener('click', function() {
            inputSearch.value = '';
            if (select) select.value = config.selectAllValue;
            filter();
        });
    }

    switchTab(currentView);
}
