<?php
$idPrefix   = $idPrefix   ?? '';
$isModal    = $isModal    ?? false;
$redirectTo = $redirectTo ?? current_url();
?>
<form method="post" action="<?= base_url('enviar-login') ?>">
    <?= csrf_field(); ?>
    <input type="hidden" name="redirect_to" value="<?= esc($redirectTo, 'attr') ?>">

    <?php if (session('error')): ?>
        <div class="alert alert-danger py-2 small"><?= esc(session('error')) ?></div>
    <?php endif; ?>

    <div class="artisan-input-group">
        <label for="<?= $idPrefix ?>email">Usuario o Email</label>
        <input type="text" class="artisan-control" id="<?= $idPrefix ?>email" name="email" value="<?= old('email') ?>" placeholder="Ingresa tu usuario" required<?= $isModal ? '' : ' autofocus' ?>>
    </div>

    <div class="artisan-input-group">
        <label for="<?= $idPrefix ?>password">Contraseña</label>
        <input type="password" class="artisan-control" id="<?= $idPrefix ?>password" name="pass" placeholder="••••••••" required>
    </div>

    <button type="submit" class="btn-auth-primary">
        <i class="bi bi-door-open-fill me-2"></i> Ingresar al Portal
    </button>
</form>
