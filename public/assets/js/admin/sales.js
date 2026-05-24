/**
 * CVA Muebles - Admin & User Sales Scripts
 * Lógica para la gestión de ventas (detalleVentas, nuevo pedido) y compras de usuario (vistaCompras, ver_factura_usuario).
 */

document.addEventListener('DOMContentLoaded', function() {
    initSalesAdmin();
    initSalesUser();
});

function initSalesAdmin() {
    const inputSearch = document.getElementById('input-search');
    const selectStatus = document.getElementById('select-status');
    const rows = document.querySelectorAll('.order-row');
    const noResults = document.getElementById('no-results-row');
    const filterStatus = document.getElementById('filter-status');
    const btnReset = document.getElementById('btn-reset');
    
    window.filterByStatus = function(status) {
        const tabEl = document.getElementById('activos-tab');
        if (tabEl) {
            tabEl.click();
        }
        if (selectStatus) {
            selectStatus.value = status;
            selectStatus.dispatchEvent(new Event('change'));
        }
    };

    // Solo inicializar el resto si estamos en la vista de administración con tabla (detalleVentas)
    if (!inputSearch || !selectStatus) return;

    const filterForm = document.getElementById('filter-form');
    const filterMode = filterForm ? (filterForm.getAttribute('data-filter-mode') || 'client') : 'client';
    let timeoutId;

    function filterOrders() {
        if (filterMode === 'server') {
            if (filterStatus) filterStatus.style.opacity = '1';
            clearTimeout(timeoutId);
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
                        
                        const newGrid = doc.getElementById('pedidosTabContent');
                        const currentGrid = document.getElementById('pedidosTabContent');
                        if (newGrid && currentGrid) {
                            currentGrid.innerHTML = newGrid.innerHTML;
                        }

                        const newPagination = doc.getElementById('pagination-container');
                        const currentPagination = document.getElementById('pagination-container');
                        if (newPagination && currentPagination) {
                            currentPagination.innerHTML = newPagination.innerHTML;
                        } else if (newPagination && !currentPagination) {
                            currentGrid.insertAdjacentHTML('afterend', newPagination.outerHTML);
                        } else if (!newPagination && currentPagination) {
                            currentPagination.remove();
                        }
                        
                        if (filterStatus) filterStatus.style.opacity = '0';
                    })
                    .catch(error => {
                        console.error('Error filtering sales:', error);
                        if (filterStatus) filterStatus.style.opacity = '0';
                    });
                }
            }, 500);
            return;
        }

        const searchTerm = inputSearch.value.toLowerCase();
        const statusFilter = selectStatus.value;
        let visibleCount = 0;

        if (filterStatus) filterStatus.style.opacity = '1';

        rows.forEach(row => {
            const searchData = row.getAttribute('data-search') || '';
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

        if (noResults) noResults.style.display = (visibleCount === 0) ? '' : 'none';

        if (filterStatus) {
            setTimeout(() => {
                filterStatus.style.opacity = '0';
            }, 300);
        }
    }

    inputSearch.addEventListener('input', filterOrders);
    selectStatus.addEventListener('change', filterOrders);

    if (btnReset) {
        btnReset.addEventListener('click', function() {
            inputSearch.value = '';
            selectStatus.value = 'ALL';
            filterOrders();
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
                
                const newGrid = doc.getElementById('pedidosTabContent');
                const currentGrid = document.getElementById('pedidosTabContent');
                if (newGrid && currentGrid) {
                    currentGrid.innerHTML = newGrid.innerHTML;
                }

                const newPagination = doc.getElementById('pagination-container');
                const currentPagination = document.getElementById('pagination-container');
                if (newPagination && currentPagination) {
                    currentPagination.innerHTML = newPagination.innerHTML;
                } else if (newPagination && !currentPagination) {
                    currentGrid.insertAdjacentHTML('afterend', newPagination.outerHTML);
                } else if (!newPagination && currentPagination) {
                    currentPagination.remove();
                }

                if (filterStatus) filterStatus.style.opacity = '0';
            })
            .catch(error => {
                console.error('Error paginating sales:', error);
                if (filterStatus) filterStatus.style.opacity = '0';
            });
        }
    });

    // Check for initial filter in URL
    const urlParams = new URLSearchParams(window.location.search);
    const initialStatus = urlParams.get('estado');
    if (initialStatus) {
        selectStatus.value = initialStatus;
    }

    // Lógica de reordenamiento asíncrono y UI optimista para las flechitas de prioridad (usando event delegation para que sobreviva al AJAX)
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-prioridad-subir, .btn-prioridad-bajar');
        if (!btn) return;
        
        e.preventDefault();
        
        const url = btn.getAttribute('href');
        const currentRow = btn.closest('.order-row');
        if (!currentRow) return;

        const isSubir = btn.classList.contains('btn-prioridad-subir');
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

    filterOrders();
}

function initSalesUser() {
    // 1. Preview de imagen para nuevo pedido
    const refImgInput = document.getElementById('ref_img');
    const fileNameDisplay = document.getElementById('file-name');
    if (refImgInput && fileNameDisplay) {
        refImgInput.addEventListener('change', function() {
            const fileName = this.files[0] ? this.files[0].name : '';
            fileNameDisplay.innerHTML = fileName ? '<i class="bi bi-file-earmark-check me-1"></i> Seleccionado: ' + fileName : '';
        });
    }

    // 2. togglePreview global para facturas/compras
    window.togglePreview = function(id, element) {
        // Cerrar otros abiertos
        document.querySelectorAll('.product-preview-card').forEach(card => {
            if(card.id !== id) card.classList.remove('active');
        });
        
        const card = document.getElementById(id);
        if (card) {
            card.classList.toggle('active');
            
            // Cerrar al hacer clic fuera
            document.addEventListener('click', function close(e) {
                if (!card.contains(e.target) && !element.contains(e.target)) {
                    card.classList.remove('active');
                    document.removeEventListener('click', close);
                }
            });
        }
    };

    // 3. Filtrado y Ordenamiento (vistaCompras)
    const filterBtns = document.querySelectorAll('.btn-filter-artisan');
    const sortSelect = document.getElementById('sort-purchases');
    const container = document.getElementById('purchases-list');
    const cards = Array.from(document.querySelectorAll('.purchase-card'));
    const noResults = document.getElementById('no-results-purchases');
    
    const userFilterForm = document.getElementById('user-filter-form');
    const filterMode = userFilterForm ? (userFilterForm.getAttribute('data-filter-mode') || 'client') : 'client';
    const filterInput = document.getElementById('user-filter-input');

    if (cards.length > 0 || filterMode === 'server') {
        let currentFilter = filterInput ? filterInput.value : 'todos';

        function filterAndSort() {
            if (filterMode === 'server') {
                if (userFilterForm) {
                    const formData = new FormData(userFilterForm);
                    const params = new URLSearchParams(formData);
                    const url = window.location.pathname + '?' + params.toString();
                    
                    window.history.pushState({}, '', url);

                    fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        const newGrid = doc.getElementById('purchases-list');
                        const currentGrid = document.getElementById('purchases-list');
                        if (newGrid && currentGrid) {
                            currentGrid.innerHTML = newGrid.innerHTML;
                        }
                    })
                    .catch(error => {
                        console.error('Error filtering user sales:', error);
                    });
                }
                return;
            }

            let visibleCount = 0;

            // Filtrar
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
            if (noResults) {
                if (visibleCount === 0) {
                    noResults.classList.remove('d-none');
                } else {
                    noResults.classList.add('d-none');
                }
            }

            // Ordenar
            const sortBy = sortSelect ? sortSelect.value : 'recent';
            const sorted = [...cards].sort((a, b) => {
                const idA = parseInt(a.dataset.id) || 0;
                const idB = parseInt(b.dataset.id) || 0;
                const priceA = parseFloat(a.dataset.total) || 0;
                const priceB = parseFloat(b.dataset.total) || 0;
                const dateA = parseInt(a.dataset.fecha) || 0;
                const dateB = parseInt(b.dataset.fecha) || 0;

                if (sortBy === 'recent') {
                    return dateB - dateA;
                } else if (sortBy === 'oldest') {
                    return dateA - dateB;
                } else if (sortBy === 'high-price') {
                    return priceB - priceA;
                } else if (sortBy === 'low-price') {
                    return priceA - priceB;
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
                if (filterMode !== 'server') {
                    filterBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                }
                currentFilter = this.dataset.filter;
                if (filterInput) filterInput.value = currentFilter;
                filterAndSort();
            });
        });

        if (sortSelect) {
            sortSelect.addEventListener('change', filterAndSort);
        }
        
        if (filterMode === 'client') {
            filterAndSort();
        }
    }
}
