(function() {
    function updateFavoriteButton(btn, status) {
        if (!btn) return;
        btn.classList.toggle('active', status === 'added');
        const icon = btn.querySelector('i');
        if (!icon) return;
        icon.classList.toggle('bi-heart-fill', status === 'added');
        icon.classList.toggle('bi-heart', status !== 'added');
    }

    window.toggleFav = function(event, id, btn) {
        event.preventDefault();
        event.stopPropagation();

        fetch(window.cvaPage.favoritosToggleUrl + id, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.cvaPage.csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'added' || data.status === 'removed') {
                updateFavoriteButton(btn, data.status);
                return;
            }
            if (data.status === 'error') {
                window.location.href = window.cvaPage.loginUrl;
            }
        })
        .catch(err => {
            console.error('Error toggling favorite:', err);
            if (window.confirm('No se pudo actualizar tu favorito. Quieres iniciar sesión?')) {
                window.location.href = window.cvaPage.loginUrl;
            }
        });
    };

    function filterProducts() {
        const filterContainer = document.querySelector('.filter-container');
        if (filterContainer && filterContainer.dataset.filterMode === 'server') {
            return; // En modo servidor, los enlaces navegan al backend, no hay filtrado JS
        }

        const buttons = document.querySelectorAll('button.filtro-categoria');
        const items = document.querySelectorAll('#lista-productos > div');

        buttons.forEach(btn => {
            btn.addEventListener('click', function() {
                buttons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const categoria = this.dataset.categoria.toLowerCase();

                items.forEach(prod => {
                    const catProd = prod.dataset.categorias.toLowerCase();
                    prod.style.display = (categoria === 'todos' || catProd === categoria) ? 'block' : 'none';
                });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', filterProducts);
})();
