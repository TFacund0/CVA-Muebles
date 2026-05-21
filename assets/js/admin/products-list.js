/**
 * CVA Muebles - Admin Products List Scripts
 * Lógica para la vista de listado de productos (Tabs, Filtros, Acciones).
 */

// Sobreescribimos la función global para mantener el tab activo
window.submitAction = function(url, message) {
    if (confirm(message)) {
        const form = document.getElementById('global-action-form');
        const separator = url.includes('?') ? '&' : '?';
        const activosTab = document.getElementById('activos-tab');
        const vista = (activosTab && activosTab.classList.contains('active')) ? 'NO' : 'SI';
        if (form) {
            form.action = url + separator + 'vista=' + vista;
            form.submit();
        }
    }
};

window.resetFilters = function() {
    const btnReset = document.getElementById('btn-reset');
    if (btnReset) btnReset.click();
};

document.addEventListener('DOMContentLoaded', function() {
    const inputSearch = document.getElementById('input-search');
    const selectCategory = document.getElementById('select-category');
    const activosTab = document.getElementById('activos-tab');
    const archivadosTab = document.getElementById('archivados-tab');
    const rows = document.querySelectorAll('.product-row');
    const noResults = document.getElementById('no-results-row');
    const emptyActive = document.getElementById('empty-active-row');
    const emptyArchive = document.getElementById('empty-archive-row');
    const filterStatus = document.getElementById('filter-status');
    const btnReset = document.getElementById('btn-reset');
    const tabsContainer = document.getElementById('productosTab');

    let currentView = tabsContainer ? (tabsContainer.getAttribute('data-vista') || 'NO') : 'NO';

    function filterProducts() {
        if (!inputSearch) return;
        
        const searchTerm = inputSearch.value.toLowerCase();
        const categoryTerm = selectCategory.value;
        let visibleCount = 0;
        let totalInCurrentView = 0;

        if (filterStatus) filterStatus.style.opacity = '1';

        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const category = row.getAttribute('data-category');
            const eliminado = row.getAttribute('data-eliminado');
            
            const isCorrectView = (eliminado === currentView);
            const matchesSearch = name.includes(searchTerm);
            const matchesCategory = (categoryTerm === 'all' || category === categoryTerm);

            if (isCorrectView) {
                totalInCurrentView++;
                if (matchesSearch && matchesCategory) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            } else {
                row.style.display = 'none';
            }
        });

        // Control de filas vacías
        if (noResults) noResults.style.display = (visibleCount === 0 && totalInCurrentView > 0) ? '' : 'none';
        if (emptyActive) emptyActive.style.display = (totalInCurrentView === 0 && currentView === 'NO') ? '' : 'none';
        if (emptyArchive) emptyArchive.style.display = (totalInCurrentView === 0 && currentView === 'SI') ? '' : 'none';
        
        if (filterStatus) {
            setTimeout(() => {
                filterStatus.style.opacity = '0';
            }, 200);
        }
    }

    function switchTab(view) {
        currentView = view;
        if (currentView === 'NO') {
            if (activosTab) {
                activosTab.classList.add('active');
                activosTab.setAttribute('aria-selected', 'true');
            }
            if (archivadosTab) {
                archivadosTab.classList.remove('active');
                archivadosTab.setAttribute('aria-selected', 'false');
            }
        } else {
            if (archivadosTab) {
                archivadosTab.classList.add('active');
                archivadosTab.setAttribute('aria-selected', 'true');
            }
            if (activosTab) {
                activosTab.classList.remove('active');
                activosTab.setAttribute('aria-selected', 'false');
            }
        }
        filterProducts();
    }

    if (activosTab) activosTab.addEventListener('click', () => switchTab('NO'));
    if (archivadosTab) archivadosTab.addEventListener('click', () => switchTab('SI'));

    if (inputSearch) inputSearch.addEventListener('input', filterProducts);
    if (selectCategory) selectCategory.addEventListener('change', filterProducts);
    
    if (btnReset) {
        btnReset.addEventListener('click', function() {
            if (inputSearch) inputSearch.value = '';
            if (selectCategory) selectCategory.value = 'all';
            filterProducts();
        });
    }

    // Inicializar vista
    switchTab(currentView);
});
