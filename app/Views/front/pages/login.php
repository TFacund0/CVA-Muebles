<?= $this->extend('layout/main') ?>

<?= $this->section('extra-css') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/auth.css?v=1.0')?>">
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



            <form method="post" action="<?= base_url('enviar-login') ?>">
                <?= csrf_field(); ?>
                
                <div class="artisan-input-group">
                    <label for="email">Usuario o Email</label>
                    <input type="text" class="artisan-control" id="email" name="email" value="<?= old('email') ?>" placeholder="Ingresa tu usuario" required autofocus>
                </div>

                <div class="artisan-input-group">
                    <label for="password">Contraseña</label>
                    <input type="password" class="artisan-control" id="password" name="pass" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn-auth-primary">
                    <i class="bi bi-door-open-fill me-2"></i> Ingresar al Portal
                </button>
            </form>

            <div class="auth-footer">
                ¿Aún no eres parte? <a href="<?= base_url('/registro') ?>">Crea tu cuenta aquí</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
