<?php
$idPrefix   = $idPrefix   ?? '';
$isModal    = $isModal    ?? false;
$isAdmin    = $isAdmin    ?? false;
$redirectTo = $redirectTo ?? current_url();
$validation = \Config\Services::validation();
?>
<form method="post" action="<?= base_url('/enviar-form') ?>">
    <?= csrf_field(); ?>
    <input type="hidden" name="redirect_to" value="<?= esc($redirectTo, 'attr') ?>">

    <?php if (session('fail')): ?>
        <div class="alert alert-danger py-2 small"><?= esc(session('fail')) ?></div>
    <?php endif; ?>

    <div class="row g-3">
        <div class="col-md-12">
            <div class="artisan-input-group">
                <label for="<?= $idPrefix ?>user">Nombre de Usuario</label>
                <input type="text" class="artisan-control" id="<?= $idPrefix ?>user" name="user" value="<?= old('user') ?>" placeholder="Ej: artesano_maestro" required>
                <?php if ($validation->getError('user')): ?>
                    <div class="text-danger x-small mt-1 fw-bold"><?= $validation->getError('user') ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="artisan-input-group">
                <label for="<?= $idPrefix ?>name">Nombre</label>
                <input type="text" class="artisan-control" id="<?= $idPrefix ?>name" name="name" value="<?= old('name') ?>" placeholder="Tu nombre" required>
                <?php if ($validation->getError('name')): ?>
                    <div class="text-danger x-small mt-1 fw-bold"><?= $validation->getError('name') ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="artisan-input-group">
                <label for="<?= $idPrefix ?>surname">Apellido</label>
                <input type="text" class="artisan-control" id="<?= $idPrefix ?>surname" name="surname" value="<?= old('surname') ?>" placeholder="Tu apellido" required>
                <?php if ($validation->getError('surname')): ?>
                    <div class="text-danger x-small mt-1 fw-bold"><?= $validation->getError('surname') ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-12">
            <div class="artisan-input-group">
                <label for="<?= $idPrefix ?>email">Email</label>
                <input type="email" class="artisan-control" id="<?= $idPrefix ?>email" name="email" value="<?= old('email') ?>" placeholder="correo@ejemplo.com" required>
                <?php if ($validation->getError('email')): ?>
                    <div class="text-danger x-small mt-1 fw-bold"><?= $validation->getError('email') ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-12">
            <div class="artisan-input-group">
                <label for="<?= $idPrefix ?>pass">Contraseña</label>
                <input type="password" class="artisan-control" id="<?= $idPrefix ?>pass" name="pass" placeholder="Mínimo 8 caracteres" required>
                <?php if ($validation->getError('pass')): ?>
                    <div class="text-danger x-small mt-1 fw-bold"><?= $validation->getError('pass') ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($isAdmin): ?>
        <input type="hidden" name="terms" value="checked">
    <?php else: ?>
        <div class="artisan-check">
            <input type="checkbox" id="<?= $idPrefix ?>terms" name="terms" required>
            <label for="<?= $idPrefix ?>terms">Acepto los <a href="<?= base_url('/terminosYCondiciones') ?>" class="fw-bold text-cva-brown" target="_blank">Términos y Condiciones</a></label>
        </div>
    <?php endif; ?>

    <button type="submit" class="btn-auth-primary">
        <i class="bi bi-check-lg me-2"></i> Finalizar Registro
    </button>
</form>
