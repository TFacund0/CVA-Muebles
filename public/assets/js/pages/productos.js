(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        const root = document.getElementById('catalogo-productos');
        const page = document.getElementById('productos');
        if (!root || !page) return;

        let csrfToken = page.dataset.csrfToken;
        const favoritosToggleUrl = page.dataset.favoritosToggleUrl;
        const loginUrl = page.dataset.loginUrl;
        let favoriteQueue = Promise.resolve();

        function toggleFav(event, id, btn) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            // Evitar múltiples clics en el mismo botón mientras está procesándose
            if (btn.classList.contains('loading')) return;
            btn.classList.add('loading');

            const icon = btn.querySelector('i');
            const wasActive = btn.classList.contains('active');

            // Toggle visual optimista inmediato para una respuesta instantánea
            btn.classList.toggle('active');
            if (wasActive) {
                icon.classList.remove('bi-heart-fill');
                icon.classList.add('bi-heart');
            } else {
                icon.classList.remove('bi-heart');
                icon.classList.add('bi-heart-fill');
            }

            function revertToggle() {
                btn.classList.toggle('active', wasActive);
                if (wasActive) {
                    icon.classList.remove('bi-heart');
                    icon.classList.add('bi-heart-fill');
                } else {
                    icon.classList.remove('bi-heart-fill');
                    icon.classList.add('bi-heart');
                }
            }

            // Encolar la petición de forma secuencial para garantizar consistencia del token CSRF
            favoriteQueue = favoriteQueue.then(() => {
                return toggleFavorito(favoritosToggleUrl, id, csrfToken, (newToken) => csrfToken = newToken)
                    .then(data => {
                        btn.classList.remove('loading');
                        if (data.status === 'error') {
                            // Revertir cambio optimista si no está autenticado y mandar a login
                            revertToggle();
                            window.location.href = loginUrl;
                        }
                    })
                    .catch(err => {
                        btn.classList.remove('loading');
                        console.error('Error:', err);
                        revertToggle();
                    });
            });
        }

        root.querySelectorAll('.js-toggle-fav').forEach(btn => {
            btn.addEventListener('click', function(event) {
                toggleFav(event, this.dataset.productoId, this);
            });
        });

        const botones = document.querySelectorAll('.filtro-categoria');

        botones.forEach(btn => {
            btn.addEventListener('click', () => {
                // Manejo de clase activa
                botones.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const categoria = btn.dataset.categoria.toLowerCase();
                const items = document.querySelectorAll('#lista-productos > div');

                items.forEach(prod => {
                    const catProd = prod.dataset.categorias.toLowerCase();
                    if (categoria === 'todos' || catProd === categoria) {
                        prod.style.display = 'block';
                    } else {
                        prod.style.display = 'none';
                    }
                });
            });
        });
    });
})();
