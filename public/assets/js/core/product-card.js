/**
 * CVA Muebles - Product Card Logic
 * Maneja las interacciones comunes de las tarjetas de producto (ej. Favoritos)
 */

document.addEventListener('DOMContentLoaded', function() {
    initProductCards();
});

let favoriteQueue = Promise.resolve();

function initProductCards() {
    // Usamos delegación de eventos para que funcione con tarjetas inyectadas dinámicamente
    document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-fav-artisan');
        if (!btn) return;
        
        e.preventDefault();
        e.stopPropagation();

        const id = btn.getAttribute('data-id');
        if (!id) return;

        // Evitar múltiples clics
        if (btn.classList.contains('loading')) return;
        btn.classList.add('loading');

        const icon = btn.querySelector('i');
        const wasActive = btn.classList.contains('active');

        // Toggle visual optimista
        btn.classList.toggle('active');
        if (wasActive) {
            icon.classList.remove('bi-heart-fill');
            icon.classList.add('bi-heart');
        } else {
            icon.classList.remove('bi-heart');
            icon.classList.add('bi-heart-fill');
        }

        favoriteQueue = favoriteQueue.then(() => {
            return fetch(`${CVA.baseUrl}favoritos/toggle/${id}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': CVA.csrfHash
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                btn.classList.remove('loading');
                
                if (data.csrf) CVA.csrfHash = data.csrf; // Refrescar token global

                if (data.status === 'error') {
                    // Revertir y mandar a login
                    btn.classList.toggle('active', wasActive);
                    if (wasActive) {
                        icon.classList.remove('bi-heart');
                        icon.classList.add('bi-heart-fill');
                    } else {
                        icon.classList.remove('bi-heart-fill');
                        icon.classList.add('bi-heart');
                    }
                    window.location.href = `${CVA.baseUrl}login`;
                }
            })
            .catch(err => {
                btn.classList.remove('loading');
                console.error('Error:', err);
                // Revertir cambio
                btn.classList.toggle('active', wasActive);
                if (wasActive) {
                    icon.classList.remove('bi-heart');
                    icon.classList.add('bi-heart-fill');
                } else {
                    icon.classList.remove('bi-heart-fill');
                    icon.classList.add('bi-heart');
                }
            });
        });
    });
}
