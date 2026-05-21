/**
 * CVA Muebles - Admin Messages Scripts
 * Lógica para la vista "Lista de Consultas" (Tabs, Filtros de búsqueda, modal de eliminación).
 */

document.addEventListener('DOMContentLoaded', function() {
    const inputSearch = document.getElementById('input-search');
    const selectAsunto = document.getElementById('select-asunto');
    const pendientesTab = document.getElementById('pendientes-tab');
    const contestadosTab = document.getElementById('contestados-tab');
    const listTitle = document.getElementById('inquiry-list-title');
    const rows = document.querySelectorAll('.inquiry-row');
    const noResults = document.getElementById('no-results-row');
    const emptyViewRow = document.getElementById('empty-view-row');
    const filterStatus = document.getElementById('filter-status');
    const btnReset = document.getElementById('btn-reset');
    
    // Obtenemos cuentas de los data attributes inyectados en el html
    const headerStatusBadge = document.getElementById('inquiry-header-status-badge');
    const numActivos = headerStatusBadge ? headerStatusBadge.getAttribute('data-activos') : 0;
    const numContestados = headerStatusBadge ? headerStatusBadge.getAttribute('data-contestados') : 0;

    // Detectar vista inicial desde query parameter para mantener el foco
    const urlParams = new URLSearchParams(window.location.search);
    let currentView = urlParams.get('vista') === 'NO' ? 'NO' : 'SI';

    function filterInquiries() {
        if(!inputSearch) return;
        const searchTerm = inputSearch.value.toLowerCase();
        const asuntoFilter = selectAsunto.value;
        let visibleCount = 0;
        let totalInCurrentView = 0;

        if (filterStatus) filterStatus.style.opacity = '1';

        rows.forEach(row => {
            const searchData = row.getAttribute('data-search') || '';
            const asunto = row.getAttribute('data-asunto');
            const activo = row.getAttribute('data-activo');

            const isCorrectView = (activo === currentView);
            const matchesSearch = searchData.includes(searchTerm);
            const matchesAsunto = (asuntoFilter === 'ALL' || asunto === asuntoFilter);

            if (isCorrectView) {
                totalInCurrentView++;
                if (matchesSearch && matchesAsunto) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            } else {
                row.style.display = 'none';
            }
        });

        if (noResults) {
            noResults.style.display = (visibleCount === 0 && totalInCurrentView > 0) ? '' : 'none';
        }

        if (emptyViewRow) {
            if (totalInCurrentView === 0) {
                emptyViewRow.style.display = '';
                const emptyText = document.getElementById('empty-view-text');
                const emptyIcon = document.getElementById('empty-view-icon');
                if (currentView === 'SI') {
                    if (emptyText) emptyText.innerText = "¡Excelente! No tienes consultas pendientes de respuesta.";
                    if (emptyIcon) emptyIcon.className = "bi bi-check2-all display-4 text-success opacity-75";
                } else {
                    if (emptyText) emptyText.innerText = "No tienes consultas archivadas o contestadas en el historial.";
                    if (emptyIcon) emptyIcon.className = "bi bi-archive display-4 text-muted opacity-50";
                }
            } else {
                emptyViewRow.style.display = 'none';
            }
        }

        if (filterStatus) {
            setTimeout(() => {
                filterStatus.style.opacity = '0';
            }, 300);
        }
    }

    function switchView(view) {
        currentView = view;

        const activeBadge = document.getElementById('inquiry-active-badge');

        if (currentView === 'SI') {
            if (pendientesTab) {
                pendientesTab.classList.add('active');
                pendientesTab.setAttribute('aria-selected', 'true');
            }
            if (contestadosTab) {
                contestadosTab.classList.remove('active');
                contestadosTab.setAttribute('aria-selected', 'false');
            }

            if (activeBadge) {
                activeBadge.className = "badge bg-gold text-brown fs-6 px-3 py-1 rounded-pill border border-gold shadow-sm";
                activeBadge.innerHTML = '<i class="bi bi-envelope-open-fill me-1"></i> PENDIENTES';
            }
            if (headerStatusBadge) {
                headerStatusBadge.className = "badge bg-gold-soft text-gold px-4 py-2 rounded-pill fs-6 fw-bold border border-gold shadow-sm w-sm-100 justify-content-center";
                headerStatusBadge.innerHTML = `<i class="bi bi-hourglass-split me-2"></i>${numActivos} PENDIENTES`;
            }
            if (listTitle) {
                listTitle.innerHTML = '<span class="status-dot status-dot-pulse-gold"></span> <i class="bi bi-envelope-open me-1 text-gold"></i> Listado de Consultas Pendientes';
            }
        } else {
            if (contestadosTab) {
                contestadosTab.classList.add('active');
                contestadosTab.setAttribute('aria-selected', 'true');
            }
            if (pendientesTab) {
                pendientesTab.classList.remove('active');
                pendientesTab.setAttribute('aria-selected', 'false');
            }

            if (activeBadge) {
                activeBadge.className = "badge bg-brown text-gold fs-6 px-3 py-1 rounded-pill border border-gold shadow-sm";
                activeBadge.innerHTML = '<i class="bi bi-check2-all me-1"></i> CONTESTADAS / ARCHIVADAS';
            }
            if (headerStatusBadge) {
                headerStatusBadge.className = "badge bg-light text-muted px-4 py-2 rounded-pill fs-6 fw-bold border shadow-sm w-sm-100 justify-content-center";
                headerStatusBadge.innerHTML = `<i class="bi bi-archive-fill me-2"></i>${numContestados} CONTESTADOS`;
            }
            if (listTitle) {
                listTitle.innerHTML = '<span class="status-dot status-dot-green"></span> <i class="bi bi-check2-all me-1 text-gold"></i> Listado de Consultas Contestadas / Archivadas';
            }
        }

        filterInquiries();
    }

    if (pendientesTab) {
        pendientesTab.addEventListener('click', () => switchView('SI'));
    }
    if (contestadosTab) {
        contestadosTab.addEventListener('click', () => switchView('NO'));
    }
    
    if (inputSearch) inputSearch.addEventListener('input', filterInquiries);
    if (selectAsunto) selectAsunto.addEventListener('change', filterInquiries);

    if (btnReset) {
        btnReset.addEventListener('click', function() {
            if(inputSearch) inputSearch.value = '';
            if(selectAsunto) selectAsunto.value = 'ALL';
            filterInquiries();
        });
    }

    // Modal de Eliminación Permanente
    const modalDeleteEl = document.getElementById('modalConfirmarEliminarConsulta');
    const modalDelete = modalDeleteEl ? new bootstrap.Modal(modalDeleteEl) : null;
    const formDelete = document.getElementById('form-eliminar-permanente');
    const delName = document.getElementById('del-inquiry-name');
    const delDetails = document.getElementById('del-inquiry-details');
    const inputConfirmWord = document.getElementById('confirm-delete-word');
    const btnSubmitDelete = document.getElementById('btn-submit-delete-permanente');
    const radioReasons = document.querySelectorAll('input[name="razon_eliminacion"]');

    window.mostrarModalEliminarConsulta = function(id, nombre, asunto, fecha) {
        if (!modalDelete) return;
        if (formDelete) formDelete.action = `${CVA.baseUrl}consultas/eliminar-permanente/${id}?vista=NO`;
        if (delName) delName.textContent = nombre;
        if (delDetails) delDetails.textContent = `${asunto} (${fecha})`;

        // Resetear inputs del modal
        if (inputConfirmWord) inputConfirmWord.value = '';
        if (btnSubmitDelete) btnSubmitDelete.disabled = true;
        radioReasons.forEach(radio => radio.checked = false);

        modalDelete.show();
    };

    function validarConfirmacionEliminar() {
        if (!btnSubmitDelete) return;
        let reasonSelected = false;
        radioReasons.forEach(radio => {
            if (radio.checked) reasonSelected = true;
        });

        const textMatches = (inputConfirmWord && inputConfirmWord.value.trim().toUpperCase() === 'ELIMINAR');

        btnSubmitDelete.disabled = !(reasonSelected && textMatches);
    }

    if (inputConfirmWord) {
        inputConfirmWord.addEventListener('input', validarConfirmacionEliminar);
    }
    radioReasons.forEach(radio => radio.addEventListener('change', validarConfirmacionEliminar));

    // Inicializar con la vista correcta
    switchView(currentView);
});
