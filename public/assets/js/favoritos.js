/**
 * Helper compartido para togglear favoritos vía fetch con token CSRF.
 * Usado por el catálogo de productos y por la página "Mis Favoritos" —
 * cada una decide qué hacer con la respuesta (animar el ícono, quitar
 * la tarjeta, etc.), pero la llamada de red y la renovación del token
 * son idénticas, así que viven acá una sola vez.
 */
function toggleFavorito(toggleUrl, id, csrfToken, onTokenRefresh) {
    return fetch(toggleUrl + id, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Response error');
            return response.json();
        })
        .then(data => {
            if (data.csrf && onTokenRefresh) onTokenRefresh(data.csrf);
            return data;
        });
}
