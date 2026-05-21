/**
 * CVA Muebles - Admin Users Scripts
 * Lógica para la vista de listado de usuarios y perfil de usuario.
 */

// Override global submitAction for users list if needed
window.submitActionUsers = function(url, message) {
    if (confirm(message)) {
        const form = document.getElementById('action-form') || document.getElementById('global-action-form');
        const separator = url.includes('?') ? '&' : '?';
        const activosTab = document.getElementById('activos-tab');
        const vista = (activosTab && activosTab.classList.contains('active')) ? 'NO' : 'SI';
        if (form) {
            form.action = url + separator + 'vista=' + vista;
            form.submit();
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    initUsersList();
    initProfileConfig();
});

function initUsersList() {
    const inputSearch = document.getElementById('input-search');
    const selectPerfil = document.getElementById('select-perfil');
    const activosTab = document.getElementById('activos-tab');
    const suspendidosTab = document.getElementById('suspendidos-tab');
    const rows = document.querySelectorAll('.user-row');
    const noResults = document.getElementById('no-results-row');
    const emptyActive = document.getElementById('empty-active-row');
    const emptySuspended = document.getElementById('empty-suspended-row');
    const filterStatus = document.getElementById('filter-status');
    const btnReset = document.getElementById('btn-reset');
    const tabsContainer = document.getElementById('usuariosTab');

    if (!tabsContainer && (!activosTab || !suspendidosTab)) return; // Not on users list page

    let currentView = tabsContainer ? (tabsContainer.getAttribute('data-vista') || 'NO') : 'NO';

    function filterUsers() {
        if (!inputSearch) return;
        const searchTerm = inputSearch.value.toLowerCase();
        const perfilTerm = selectPerfil.value;
        let visibleCount = 0;
        let totalInCurrentView = 0;

        if (filterStatus) filterStatus.style.opacity = '1';

        rows.forEach(row => {
            const searchData = row.getAttribute('data-search') || '';
            const baja = row.getAttribute('data-baja');
            const perfil = row.getAttribute('data-perfil');

            const isCorrectView = (baja === currentView);
            const matchesSearch = searchData.includes(searchTerm);
            const matchesPerfil = (perfilTerm === 'all' || perfil === perfilTerm);

            if (isCorrectView) {
                totalInCurrentView++;
                if (matchesSearch && matchesPerfil) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            } else {
                row.style.display = 'none';
            }
        });

        // Control de estados vacíos
        if (noResults) noResults.style.display = (visibleCount === 0 && totalInCurrentView > 0) ? '' : 'none';
        if (emptyActive) emptyActive.style.display = (totalInCurrentView === 0 && currentView === 'NO') ? '' : 'none';
        if (emptySuspended) emptySuspended.style.display = (totalInCurrentView === 0 && currentView === 'SI') ? '' : 'none';

        if (filterStatus) {
            setTimeout(() => {
                filterStatus.style.opacity = '0';
            }, 300);
        }
    }

    function switchTab(view) {
        currentView = view;

        if (currentView === 'NO') {
            if (activosTab) {
                activosTab.classList.add('active');
                activosTab.setAttribute('aria-selected', 'true');
            }
            if (suspendidosTab) {
                suspendidosTab.classList.remove('active');
                suspendidosTab.setAttribute('aria-selected', 'false');
            }
        } else {
            if (suspendidosTab) {
                suspendidosTab.classList.add('active');
                suspendidosTab.setAttribute('aria-selected', 'true');
            }
            if (activosTab) {
                activosTab.classList.remove('active');
                activosTab.setAttribute('aria-selected', 'false');
            }
        }

        filterUsers();
    }

    if (activosTab) activosTab.addEventListener('click', () => switchTab('NO'));
    if (suspendidosTab) suspendidosTab.addEventListener('click', () => switchTab('SI'));

    if (inputSearch) inputSearch.addEventListener('input', filterUsers);
    if (selectPerfil) selectPerfil.addEventListener('change', filterUsers);

    if (btnReset) {
        btnReset.addEventListener('click', function() {
            if (inputSearch) inputSearch.value = '';
            if (selectPerfil) selectPerfil.value = 'all';
            filterUsers();
        });
    }

    // Inicializar vista
    switchTab(currentView);
}

function initProfileConfig() {
    const toggleEdit = document.getElementById('toggleEdit');
    const cancelEdit = document.getElementById('cancelEdit');
    const btnGroup = document.getElementById('btnGroup');
    const fileGroup = document.getElementById('fileGroup');
    const inputs = document.querySelectorAll('.artisan-control-v3');

    if (!toggleEdit || !cancelEdit) return;

    toggleEdit.addEventListener('click', () => {
        inputs.forEach(input => input.disabled = false);
        if (btnGroup) btnGroup.classList.remove('d-none');
        if (fileGroup) fileGroup.classList.remove('d-none');
        toggleEdit.classList.add('d-none');
    });

    cancelEdit.addEventListener('click', () => {
        inputs.forEach(input => input.disabled = true);
        if (btnGroup) btnGroup.classList.add('d-none');
        if (fileGroup) fileGroup.classList.add('d-none');
        toggleEdit.classList.remove('d-none');
    });
}
