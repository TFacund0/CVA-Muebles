/**
 * Lógica de la pantalla de Gestión de Consultas (back/messages/lista_consultas.php):
 * filtro de búsqueda/pestañas y modal de eliminación permanente.
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
    const headerStatusBadge = document.getElementById('inquiry-header-status-badge');
    const countActivos = headerStatusBadge ? headerStatusBadge.getAttribute('data-count-activos') : '0';
    const countContestados = headerStatusBadge ? headerStatusBadge.getAttribute('data-count-contestados') : '0';

    if (!inputSearch || !selectAsunto || !pendientesTab || !contestadosTab || !btnReset) return;

    // Detectar vista inicial desde query parameter para mantener el foco
    const urlParams = new URLSearchParams(window.location.search);
    let currentView = urlParams.get('vista') === 'NO' ? 'NO' : 'SI';

    function filterInquiries() {
        const searchTerm = inputSearch.value.toLowerCase();
        const asuntoFilter = selectAsunto.value;
        let visibleCount = 0;
        let totalInCurrentView = 0;

        if (filterStatus) filterStatus.style.opacity = '1';

        rows.forEach(row => {
            const searchData = row.getAttribute('data-search');
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
                    emptyText.innerText = "¡Excelente! No tienes consultas pendientes de respuesta.";
                    emptyIcon.className = "bi bi-check2-all display-4 text-success opacity-75";
                } else {
                    emptyText.innerText = "No tienes consultas archivadas o contestadas en el historial.";
                    emptyIcon.className = "bi bi-archive display-4 text-muted opacity-50";
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
            pendientesTab.classList.add('active');
            pendientesTab.setAttribute('aria-selected', 'true');
            contestadosTab.classList.remove('active');
            contestadosTab.setAttribute('aria-selected', 'false');

            if (activeBadge) {
                activeBadge.className = "badge bg-gold text-brown fs-6 px-3 py-1 rounded-pill border border-gold shadow-sm";
                activeBadge.innerHTML = '<i class="bi bi-envelope-open-fill me-1"></i> PENDIENTES';
            }
            if (headerStatusBadge) {
                headerStatusBadge.className = "badge bg-gold-soft text-gold px-4 py-2 rounded-pill fs-6 fw-bold border border-gold shadow-sm w-sm-100 justify-content-center";
                headerStatusBadge.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>' + countActivos + ' PENDIENTES';
            }
            if (listTitle) {
                listTitle.innerHTML = '<span class="status-dot status-dot-pulse-gold"></span> <i class="bi bi-envelope-open me-1 text-gold"></i> Listado de Consultas Pendientes';
            }
        } else {
            contestadosTab.classList.add('active');
            contestadosTab.setAttribute('aria-selected', 'true');
            pendientesTab.classList.remove('active');
            pendientesTab.setAttribute('aria-selected', 'false');

            if (activeBadge) {
                activeBadge.className = "badge bg-brown text-gold fs-6 px-3 py-1 rounded-pill border border-gold shadow-sm";
                activeBadge.innerHTML = '<i class="bi bi-check2-all me-1"></i> CONTESTADAS / ARCHIVADAS';
            }
            if (headerStatusBadge) {
                headerStatusBadge.className = "badge bg-light text-muted px-4 py-2 rounded-pill fs-6 fw-bold border shadow-sm w-sm-100 justify-content-center";
                headerStatusBadge.innerHTML = '<i class="bi bi-archive-fill me-2"></i>' + countContestados + ' CONTESTADOS';
            }
            if (listTitle) {
                listTitle.innerHTML = '<span class="status-dot status-dot-green"></span> <i class="bi bi-check2-all me-1 text-gold"></i> Listado de Consultas Contestadas / Archivadas';
            }
        }

        filterInquiries();
    }

    pendientesTab.addEventListener('click', () => switchView('SI'));
    contestadosTab.addEventListener('click', () => switchView('NO'));

    inputSearch.addEventListener('input', filterInquiries);
    selectAsunto.addEventListener('change', filterInquiries);

    btnReset.addEventListener('click', function() {
        inputSearch.value = '';
        selectAsunto.value = 'ALL';
        filterInquiries();
    });

    // Modal de Eliminación Permanente
    const modalDeleteEl = document.getElementById('modalConfirmarEliminarConsulta');
    const modalDelete = modalDeleteEl ? new bootstrap.Modal(modalDeleteEl) : null;
    const formDelete = document.getElementById('form-eliminar-permanente');
    const delName = document.getElementById('del-inquiry-name');
    const delDetails = document.getElementById('del-inquiry-details');
    const inputConfirmWord = document.getElementById('confirm-delete-word');
    const btnSubmitDelete = document.getElementById('btn-submit-delete-permanente');
    const radioReasons = document.querySelectorAll('input[name="razon_eliminacion"]');

    function mostrarModalEliminarConsulta(id, nombre, asunto, fecha) {
        if (!modalDelete || !formDelete) return;
        formDelete.action = formDelete.getAttribute('data-base-url') + '/' + id + '?vista=NO';
        delName.textContent = nombre;
        delDetails.textContent = asunto + ' (' + fecha + ')';

        // Resetear inputs del modal
        inputConfirmWord.value = '';
        btnSubmitDelete.disabled = true;
        radioReasons.forEach(radio => radio.checked = false);

        modalDelete.show();
    }

    document.querySelectorAll('.js-eliminar-consulta').forEach(function(btn) {
        btn.addEventListener('click', function() {
            mostrarModalEliminarConsulta(
                btn.getAttribute('data-id'),
                btn.getAttribute('data-nombre'),
                btn.getAttribute('data-asunto'),
                btn.getAttribute('data-fecha')
            );
        });
    });

    function validarConfirmacionEliminar() {
        if (!btnSubmitDelete) return;
        let reasonSelected = false;
        radioReasons.forEach(radio => {
            if (radio.checked) reasonSelected = true;
        });

        const textMatches = (inputConfirmWord.value.trim().toUpperCase() === 'ELIMINAR');

        btnSubmitDelete.disabled = !(reasonSelected && textMatches);
    }

    if (inputConfirmWord) {
        inputConfirmWord.addEventListener('input', validarConfirmacionEliminar);
    }
    radioReasons.forEach(radio => radio.addEventListener('change', validarConfirmacionEliminar));

    // Inicializar con la vista correcta
    switchView(currentView);
});
