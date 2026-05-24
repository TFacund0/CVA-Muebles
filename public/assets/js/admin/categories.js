/**
 * CVA Muebles - Admin Categories Scripts
 * Lógica para el CRUD de Categorías.
 */

window.prepararEdicion = function(id, descripcion) {
    const descEditar = document.getElementById('descEditar');
    const formEditar = document.getElementById('formEditar');
    
    if (descEditar) descEditar.value = descripcion;
    if (formEditar) formEditar.action = `${CVA.baseUrl}admin/categorias/editar/${id}`;
};
