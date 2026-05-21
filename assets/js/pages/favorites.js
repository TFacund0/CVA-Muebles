/**
 * CVA Muebles - Favorites Scripts
 * Lógica para la vista de "Mis Favoritos": Filtros, Búsqueda en tiempo real, 
 * y eliminación mediante AJAX.
 */

document.addEventListener('DOMContentLoaded', function() {
    initFavorites();
});

function initFavorites() {
    const searchInput = document.getElementById('search-favs');
    const clearBtn = document.getElementById('clear-search');
    const filterBtns = document.querySelectorAll('.btn-filter-artisan');
    const cards = document.querySelectorAll('.fav-item');
    const noResults = document.getElementById('no-results-fav');

    let currentSearch = '';
    let currentFilter = 'todos';

    // Manejar el toggle de Favoritos
    const removeBtns = document.querySelectorAll('.remove-fav-btn');
    removeBtns.forEach(btn => {
        btn.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            if (!confirm('¿Quitar este mueble de tus favoritos?')) return;

            const id = this.getAttribute('data-id');
            if (!id) return;

            // Usamos las variables globales de CVA definidas en el layout principal
            fetch(`${CVA.baseUrl}favoritos/toggle/${id}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': CVA.csrfHash
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.csrf) CVA.csrfHash = data.csrf; // Refrescar token

                if (data.status === 'removed') {
                    const item = this.closest('.fav-item');
                    item.style.opacity = '0';
                    item.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        item.remove();
                        // Refrescar si ya no quedan favoritos
                        if (document.querySelectorAll('.fav-item').length === 0) {
                            location.reload();
                        }
                    }, 400);
                }
            })
            .catch(err => console.error('Error al remover favorito:', err));
        });
    });

    // Función de filtrado
    function filterFavorites() {
        let visibleCount = 0;

        cards.forEach(card => {
            const nombre = card.dataset.nombre.toLowerCase();
            const category = card.dataset.categorias.toLowerCase();

            const matchesSearch = nombre.includes(currentSearch);
            const matchesFilter = currentFilter === 'todos' || category === currentFilter;

            if (matchesSearch && matchesFilter) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (noResults) {
            if (visibleCount === 0) {
                noResults.classList.remove('d-none');
            } else {
                noResults.classList.add('d-none');
            }
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            currentSearch = this.value.toLowerCase().trim();
            if (currentSearch.length > 0) {
                clearBtn.classList.remove('d-none');
            } else {
                clearBtn.classList.add('d-none');
            }
            filterFavorites();
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            currentSearch = '';
            this.classList.add('d-none');
            filterFavorites();
        });
    }

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            filterFavorites();
        });
    });
}
