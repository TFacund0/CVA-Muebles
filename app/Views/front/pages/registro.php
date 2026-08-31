<?= $this->extend('layout/main') ?>

<?= $this->section('extra-css') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/pages/auth.css?v=3.2') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $validation = \Config\Services::validation(); ?>

<div class="auth-wrapper">
    <div class="auth-card">
        <!-- Branding Side -->
        <div class="auth-side-branding">
            <div class="auth-logo-circle">
                <i class="bi bi-person-plus-fill"></i>
            </div>
            <h1 class="auth-quote">Únete a la comunidad de CVA Muebles.</h1>

            <div class="mt-4">
                <div class="benefit-item">
                    <i class="bi bi-patch-check-fill"></i>
                    <span>Acceso a lanzamientos exclusivos y piezas limitadas.</span>
                </div>
                <div class="benefit-item">
                    <i class="bi bi-patch-check-fill"></i>
                    <span>Seguimiento detallado de tus pedidos artesanales.</span>
                </div>
                <div class="benefit-item">
                    <i class="bi bi-patch-check-fill"></i>
                    <span>Gestión personalizada de tus obras a medida.</span>
                </div>
            </div>

            <div class="mt-auto pt-5">
                <a href="<?= base_url('/quienesSomos') ?>" class="btn btn-outline-light rounded-pill px-4 btn-sm fw-bold">
                    CONOCE NUESTRA HISTORIA
                </a>
            </div>
        </div>

        <!-- Form Side -->
        <div class="auth-side-form">
            <div class="auth-header">
                <?php if (session()->get('logged_in') && session()->get('perfil_id') == 1): ?>
                    <h2>Registrar Nuevo Usuario</h2>
                    <p class="text-muted">Como administrador, puedes registrar nuevas cuentas directamente.</p>
                <?php else: ?>
                    <h2>Crear Cuenta</h2>
                    <p class="text-muted">Completa tus datos para formar parte de la familia.</p>
                <?php endif; ?>
            </div>

            <?php $isAdmin = session()->get('logged_in') && session()->get('perfil_id') == 1; ?>
            <?= view('partials/auth/_form_registro', [
                  'idPrefix'   => '',
                  'isModal'    => false,
                  'isAdmin'    => $isAdmin,
                  'redirectTo' => session('redirect_to') ?? '/',
            ]) ?>

            <div class="auth-footer">
                <?php if (session()->get('logged_in') && session()->get('perfil_id') == 1): ?>
                    <a href="<?= base_url('/crud-usuarios') ?>" class="btn btn-outline-brown rounded-pill px-4 btn-sm fw-bold">
                        <i class="bi bi-arrow-left me-1"></i> VOLVER AL PANEL
                    </a>
                <?php else: ?>
                    ¿Ya tienes una cuenta? <a href="<?= base_url('/login') ?>">Inicia sesión aquí</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>