<!-- Modal Login -->
<div class="modal fade" id="modalLogin" tabindex="-1" aria-labelledby="modalLoginLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h2 class="modal-title fs-5" id="modalLoginLabel">Iniciar Sesión</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <?= view('partials/auth/_form_login', [
                      'idPrefix'   => 'm-',
                      'isModal'    => true,
                      'redirectTo' => current_url(),
                ]) ?>
                <div class="auth-footer">
                    ¿Aún no eres parte?
                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalRegistro">Crea tu cuenta aquí</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Registro -->
<div class="modal fade" id="modalRegistro" tabindex="-1" aria-labelledby="modalRegistroLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h2 class="modal-title fs-5" id="modalRegistroLabel">Crear Cuenta</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <?= view('partials/auth/_form_registro', [
                      'idPrefix'   => 'm-',
                      'isModal'    => true,
                      'isAdmin'    => false,
                      'redirectTo' => current_url(),
                ]) ?>
                <div class="auth-footer">
                    ¿Ya tienes una cuenta?
                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalLogin">Inicia sesión aquí</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('#modalLogin, #modalRegistro').forEach(function (modalEl) {
        modalEl.addEventListener('show.bs.modal', function () {
            setTimeout(function () {
                document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
                    backdrop.classList.add('auth-blur');
                });
            }, 0);
        });
    });

    <?php if ($reopen = session('reopen_modal')): ?>
    var elReabrir = document.getElementById(<?= json_encode($reopen === 'registro' ? 'modalRegistro' : 'modalLogin') ?>);
    if (elReabrir && window.bootstrap) {
        new bootstrap.Modal(elReabrir).show();
    }
    <?php endif; ?>
});
</script>
