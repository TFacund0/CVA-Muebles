<?php
/**
 * Vista de Completar Registro con Google
 */
?>
<?= $this->extend('layout/main') ?>

<?= $this->section('extra-css') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/pages/auth.css?v=1.1') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $validation = \Config\Services::validation(); ?>

<div class="auth-wrapper">
    <div class="auth-card">
        <!-- Branding Side -->
        <div class="auth-side-branding">
            <div class="auth-logo-circle" style="background-color: #ffffff;">
                <svg viewBox="0 0 48 48" width="40px" height="40px"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/><path fill="none" d="M0 0h48v48H0z"/></svg>
            </div>
            <h1 class="auth-quote">¡Casi listo, <?= esc($profile['nombre']) ?>!</h1>
            <p class="opacity-75">Tu correo ha sido verificado con éxito por Google. Solo falta un pequeño paso para unirte a CVA Muebles.</p>
        </div>

        <!-- Form Side -->
        <div class="auth-side-form">
            <div class="auth-header">
                <h2>Completar Registro</h2>
                <p class="text-muted">Por favor, elige tu nombre de usuario y acepta los términos.</p>
            </div>

            <form method="post" action="<?= base_url('/usuario/finalizar-registro-google') ?>">
                <?= csrf_field(); ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="artisan-input-group">
                            <label>Nombre</label>
                            <input type="text" class="artisan-control text-muted bg-light" value="<?= esc($profile['nombre']) ?>" readonly>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="artisan-input-group">
                            <label>Apellido</label>
                            <input type="text" class="artisan-control text-muted bg-light" value="<?= esc($profile['apellido']) ?>" readonly>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="artisan-input-group">
                            <label>Email verificado por Google</label>
                            <input type="email" class="artisan-control text-muted bg-light" value="<?= esc($profile['email']) ?>" readonly>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="artisan-input-group">
                            <label>Elige tu Nombre de Usuario <span class="text-danger">*</span></label>
                            <input type="text" class="artisan-control" name="user" value="<?= old('user') ?? explode('@', $profile['email'])[0] ?>" required autofocus>
                            <?php if ($validation->getError('user')): ?>
                                <div class="text-danger x-small mt-1 fw-bold"><?= $validation->getError('user') ?></div>
                            <?php endif; ?>
                            <small class="text-muted" style="font-size:0.75rem;">Este nombre se usará en tus pedidos y reseñas.</small>
                        </div>
                    </div>
                </div>

                <div class="artisan-check mt-4">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">Acepto los <a href="<?= base_url('/terminosYCondiciones') ?>" class="fw-bold text-cva-brown" target="_blank">Términos y Condiciones</a></label>
                </div>

                <button type="submit" class="btn-auth-primary mt-3">
                    <i class="bi bi-check-lg me-2"></i> Crear Cuenta
                </button>
            </form>

            <div class="auth-footer mt-4">
                <a href="<?= base_url('/login') ?>" class="text-muted"><i class="bi bi-arrow-left me-1"></i> Cancelar y volver</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
