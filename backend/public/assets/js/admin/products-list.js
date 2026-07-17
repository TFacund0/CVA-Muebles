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

    const filterForm = document.getElementById('filter-form');
    const filterMode = filterForm ? (filterForm.getAttribute('data-filter-mode') || 'client') : 'client';
    let timeoutId;

    function filterProducts(isTabChange = false) {
        if (!inputSearch) return;
        
        if (filterMode === 'server') {
            if (filterStatus) filterStatus.style.opacity = '1';
            clearTimeout(timeoutId);
            // Si es un cambio de pestaña, enviamos instantáneamente. Si es texto, esperamos 500ms.
            const delay = isTabChange ? 0 : 500;
            timeoutId = setTimeout(() => {
                if (filterForm) {
                    const formData = new FormData(filterForm);
                    const params = new URLSearchParams(formData);
                    const url = window.location.pathname + '?' + params.toString();
                    
                    window.history.pushState({}, '', url);

                    fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        const newGrid = doc.getElementById('product-grid');
                        const currentGrid = document.getElementById('product-grid');
                        if (newGrid && currentGrid) {
                            currentGrid.innerHTML = newGrid.innerHTML;
                        }

                        // Actualizar tabs y conteos
                        ['activos-tab', 'archivados-tab'].forEach(id => {
                            const newTab = doc.getElementById(id);
                            const currentTab = document.getElementById(id);
                            if (newTab && currentTab) {
                                currentTab.innerHTML = newTab.innerHTML;
                            }
                        });

                        if (filterStatus) filterStatus.style.opacity = '0';
                    })
                    .catch(error => {
                        console.error('Error filtering:', error);
                        if (filterStatus) filterStatus.style.opacity = '0';
                    });
                }
            }, delay);
            return;
        }

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

    function switchTab(view, isUserClick = false) {
        if (currentView === view && isUserClick) return; // Ya estamos en esa vista
        currentView = view;
        const inputVista = document.getElementById('input-vista');
        if (inputVista) inputVista.value = view;

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

        if (isUserClick && filterMode === 'server') {
            filterProducts(true);
        } else if (filterMode === 'client') {
            filterProducts(false);
        }
    }

    if (activosTab) activosTab.addEventListener('click', () => switchTab('NO', true));
    if (archivadosTab) archivadosTab.addEventListener('click', () => switchTab('SI', true));

    if (inputSearch) inputSearch.addEventListener('input', () => filterProducts(false));
    if (selectCategory) selectCategory.addEventListener('change', () => filterProducts(false));
    
    if (btnReset) {
        btnReset.addEventListener('click', function() {
            if(inputSearch) inputSearch.value = '';
            if(selectCategory) selectCategory.value = 'all';
            filterProducts(false);
        });
    }

    // Interceptar clics de paginación para cargar vía AJAX en modo server
    document.addEventListener('click', function(e) {
        const paginationLink = e.target.closest('.pagination a');
        if (paginationLink && filterMode === 'server') {
            e.preventDefault();
            const url = paginationLink.href;
            
            if (filterStatus) filterStatus.style.opacity = '1';
            
            window.history.pushState({}, '', url);

            fetch(url)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                const newGrid = doc.getElementById('product-grid');
                const currentGrid = document.getElementById('product-grid');
                if (newGrid && currentGrid) {
                    currentGrid.innerHTML = newGrid.innerHTML;
                }

                if (filterStatus) filterStatus.style.opacity = '0';
                
                // Recalcular visibilidad de los tabs si es necesario
                switchTab(currentView);
            })
            .catch(error => {
                console.error('Error paginating:', error);
                if (filterStatus) filterStatus.style.opacity = '0';
            });
        }
    });

    // Inicializar vista solo visualmente si estamos en modo servidor, en cliente hace filtrado
    switchTab(currentView, false);
});
