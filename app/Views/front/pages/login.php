<?= $this->extend('layout/main') ?>

<?= $this->section('extra-css') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/auth.css?v=3.2')?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="auth-wrapper">
    <div class="auth-card">
        <!-- Branding Side -->
        <div class="auth-side-branding">
            <div class="auth-logo-circle">
                <i class="bi bi-hammer"></i>
            </div>
            <h1 class="auth-quote">La excelencia artesanal en cada detalle.</h1>
            <p class="opacity-75">Bienvenido al portal exclusivo de CVA Muebles. Gestiona tus pedidos y descubre piezas únicas diseñadas para durar toda la vida.</p>
        </div>

        <!-- Form Side -->
        <div class="auth-side-form">
            <div class="auth-header text-center text-lg-start">
                <h2>Iniciar Sesión</h2>
                <p>Ingresa tus credenciales para continuar.</p>
            </div>



            <?= view('partials/auth/_form_login', [
                  'idPrefix'   => '',
                  'isModal'    => false,
                  'redirectTo' => session('redirect_to') ?? '/',
            ]) ?>

            <div class="auth-footer">
                ¿Aún no eres parte? <a href="<?= base_url('/registro') ?>">Crea tu cuenta aquí</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
