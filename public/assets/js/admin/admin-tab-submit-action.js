/**
 * Envía una acción POST (archivar/restaurar/eliminar) al formulario global
 * del layout admin, agregando `vista=SI|NO` según la pestaña activa
 * (Activos/Archivados). Compartido por las pantallas CRUD con ese patrón
 * de pestañas (usuarios, productos) para no duplicar la misma función.
 *
 * Redefine el submitAction genérico de admin-layout.js con la variante
 * consciente de la pestaña activa.
 */
function submitAction(url, message) {
    if (confirm(message)) {
        const form = document.getElementById('global-action-form');
        const separator = url.includes('?') ? '&' : '?';
        const activosTab = document.getElementById('activos-tab');
        const vista = (activosTab && activosTab.classList.contains('active')) ? 'NO' : 'SI';
        form.action = url + separator + 'vista=' + vista;
        form.submit();
    }
}
