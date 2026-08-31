(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        const root = document.getElementById('catalogo-productos');
        const page = document.getElementById('productos');
        if (!root || !page) return;

        let csrfToken = page.dataset.csrfToken;
        const favoritosToggleUrl = page.dataset.favoritosToggleUrl;
        const loginUrl = page.dataset.loginUrl;
        const loginModalSelector = page.dataset.loginModal;
        let favoriteQueue = Promise.resolve();

        function abrirLoginOnRedirigir() {
            const el = loginModalSelector ? document.querySelector(loginModalSelector) : null;
            if (el && window.bootstrap) {
                new bootstrap.Modal(el).show();
            } else {
                window.location.href = loginUrl;
            }
        }

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
                            abrirLoginOnRedirigir();
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
        const filtroActivoLabel = document.querySelector('.filtro-activo-label');
        const filtrosOffcanvasEl = document.getElementById('filtrosOffcanvas');
        const listaProductos = document.getElementById('lista-productos');

        // Aplica el filtro de categoria: toggle de clase activa en los
        // botones, show/hide de las cards y sync del label mobile. Reusable
        // tanto desde el click del usuario como desde el replay al cargar
        // la pagina (retorno desde el detalle de producto). Devuelve el
        // boton matcheado, o null si la categoria no existe (fallback
        // silencioso para el caller).
        function aplicarFiltro(categoria, opts) {
            const cat = String(categoria || 'todos').toLowerCase();
            const btn = Array.from(botones).find(b => b.dataset.categoria.toLowerCase() === cat) || null;
            if (!btn) return null;

            botones.forEach(b => b.classList.toggle('active', b === btn));

            const items = document.querySelectorAll('#lista-productos > div');
            items.forEach(prod => {
                const catProd = (prod.dataset.categorias || '').toLowerCase();
                prod.style.display = (cat === 'todos' || catProd === cat) ? 'block' : 'none';
            });

            if (filtroActivoLabel) filtroActivoLabel.textContent = btn.textContent.trim();

            // Cerrar el panel mobile solo cuando se pide explicitamente (click
            // real del usuario) -- en el replay de carga no hay offcanvas
            // abierto, y en escritorio el offcanvas-lg lo muestra siempre.
            if (opts && opts.cerrarOffcanvas && filtrosOffcanvasEl && typeof bootstrap !== 'undefined') {
                const instance = bootstrap.Offcanvas.getInstance(filtrosOffcanvasEl);
                if (instance) instance.hide();
            }

            return btn;
        }

        botones.forEach(btn => {
            btn.addEventListener('click', () => {
                aplicarFiltro(btn.dataset.categoria, { cerrarOffcanvas: true });
            });
        });

        // Decora el link "VER DETALLES" al click con la categoria activa y
        // el id del producto, para poder restaurar el filtro y el scroll al
        // volver desde el detalle. Delegado sobre #lista-productos para
        // sobrevivir a cualquier re-render futuro y no afectar
        // mis_favoritos.php, que reusa product_card.php pero no tiene este
        // contenedor.
        if (listaProductos) {
            listaProductos.addEventListener('click', function(e) {
                const link = e.target.closest('a.js-ver-detalles');
                if (!link || !listaProductos.contains(link)) return;

                const activo = document.querySelector('.filtro-categoria.active');
                const cat = activo ? activo.dataset.categoria.toLowerCase() : 'todos';
                const card = link.closest('[data-producto-id]');

                const url = new URL(link.href, window.location.origin);
                if (cat && cat !== 'todos') url.searchParams.set('from_categoria', cat);
                if (card) url.searchParams.set('from_id', card.dataset.productoId);
                link.href = url.toString();
                // Sin preventDefault(): el href queda reescrito antes de
                // navegar, asi que con JS deshabilitado sigue funcionando
                // como un link de detalle plano.
            });
        }

        // Restaura el filtro y el scroll al volver desde el detalle de
        // producto. Ambos params son opcionales y cualquier valor
        // desconocido/removido cae en un fallback silencioso, sin error de
        // consola (requisito: entrada directa, favoritos, o categoria
        // renombrada/eliminada deben seguir funcionando como hoy).
        const qs = new URLSearchParams(window.location.search);
        const catVuelta = qs.get('categoria') || qs.get('from_categoria');
        const idVuelta = qs.get('producto') || qs.get('from_id');

        if (catVuelta) aplicarFiltro(catVuelta);

        if (idVuelta) {
            const card = document.querySelector(`#lista-productos [data-producto-id="${CSS.escape(idVuelta)}"]`);
            const col = card && card.closest('#lista-productos > div');
            if (col && col.style.display !== 'none') {
                // Doble rAF: espera a que el navegador termine el layout
                // post-filtro antes de medir la posicion para el scroll.
                // behavior:'auto' (no smooth) porque las animaciones AOS
                // fade-up todavia pueden estar asentandose.
                requestAnimationFrame(() => requestAnimationFrame(() => {
                    card.scrollIntoView({ block: 'center', behavior: 'auto' });
                }));
            }
        }
    });
})();
