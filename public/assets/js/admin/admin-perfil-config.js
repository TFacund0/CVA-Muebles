/**
 * Configuración de perfil (back/users/perfil_config.php): habilita la
 * edición de los campos del formulario al hacer click en "Editar Datos".
 */
document.addEventListener('DOMContentLoaded', function () {
    const toggleEdit = document.getElementById('toggleEdit');
    const cancelEdit = document.getElementById('cancelEdit');
    const btnGroup = document.getElementById('btnGroup');
    const fileGroup = document.getElementById('fileGroup');
    const inputs = document.querySelectorAll('.artisan-control-v3');

    toggleEdit.addEventListener('click', () => {
        inputs.forEach(input => input.disabled = false);
        btnGroup.classList.remove('d-none');
        fileGroup.classList.remove('d-none');
        toggleEdit.classList.add('d-none');
    });

    cancelEdit.addEventListener('click', () => {
        inputs.forEach(input => input.disabled = true);
        btnGroup.classList.add('d-none');
        fileGroup.classList.add('d-none');
        toggleEdit.classList.remove('d-none');
    });
});
