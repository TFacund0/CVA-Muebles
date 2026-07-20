(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        const page = document.getElementById('favoritos-page');
        if (!page) return;

        let csrfToken = page.dataset.csrfToken;
        const favoritosToggleUrl = page.dataset.favoritosToggleUrl;

        function toggleFav(event, id, btn) {
            event.preventDefault();
            event.stopPropagation();
            if (!confirm('¿Quitar este mueble de tus favoritos?')) return;

            toggleFavorito(favoritosToggleUrl, id, csrfToken, (newToken) => csrfToken = newToken)
                .then(data => {
                    if (data.status === 'removed') {
                        const item = btn.closest('.fav-item');
                        item.style.opacity = '0';
                        item.style.transform = 'scale(0.8)';
                        setTimeout(() => {
                            item.remove();
                            if (document.querySelectorAll('.fav-item').length === 0) {
                                location.reload();
                            }
                        }, 400);
                    }
                })
                .catch(err => console.error('Error:', err));
        }

        document.querySelectorAll('.js-remove-fav').forEach(btn => {
            btn.addEventListener('click', function(event) {
                toggleFav(event, this.dataset.productoId, this);
            });
        });

        const searchInput = document.getElementById('search-favs');
        const clearBtn = document.getElementById('clear-search');
        const filterBtns = document.querySelectorAll('.btn-filter-artisan');
        const cards = document.querySelectorAll('.fav-item');
        const noResults = document.getElementById('no-results-fav');

        let currentSearch = '';
        let currentFilter = 'todos';

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

            if (visibleCount === 0) {
                noResults.classList.remove('d-none');
            } else {
                noResults.classList.add('d-none');
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
    });
})();
