<?php
/**
 * Vista de Registro de Usuarios (Registration Page)
 *
 * Ofrece un flujo visual premium de creación de cuentas para nuevos clientes de CVA Muebles.
 * Se adapta dinámicamente si el usuario actual es un administrador autenticado, permitiendo
 * registrar nuevos usuarios de forma directa sin requerir aceptación manual de términos.
 *
 * Características Estéticas y Funcionales:
 * - Sección Lateral Informativa: Lista de beneficios exclusivos y enlace a la historia corporativa.
 * - Formulario Inteligente: Entradas sanitizadas y formateadas con validaciones de servidor en tiempo real.
 * - Protección CSRF: Integración directa mediante csrf_field() de CodeIgniter 4.
 *
 * Parámetros Inyectados y Servicios:
 * @var \CodeIgniter\Validation\ValidationInterface $validation Instancia del validador para visualización de errores específicos por campo.
 *
 * Datos de Sesión Evaluados:
 * - logged_in (bool|null): Indica si hay una sesión activa.
 * - perfil_id (int|null): Identifica el rol del usuario actual (1 para Administrador).
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

            <form method="post" action="<?= base_url('/enviar-form') ?>">
                <?= csrf_field(); ?>

                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="artisan-input-group">
                            <label>Nombre de Usuario</label>
                            <input type="text" class="artisan-control" name="user" value="<?= old('user') ?>" placeholder="Ej: artesano_maestro" required>
                            <?php if ($validation->getError('user')): ?>
                                <div class="text-danger x-small mt-1 fw-bold"><?= $validation->getError('user') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="artisan-input-group">
                            <label>Nombre</label>
                            <input type="text" class="artisan-control" name="name" value="<?= old('name') ?>" placeholder="Tu nombre" required>
                            <?php if ($validation->getError('name')): ?>
                                <div class="text-danger x-small mt-1 fw-bold"><?= $validation->getError('name') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="artisan-input-group">
                            <label>Apellido</label>
                            <input type="text" class="artisan-control" name="surname" value="<?= old('surname') ?>" placeholder="Tu apellido" required>
                            <?php if ($validation->getError('surname')): ?>
                                <div class="text-danger x-small mt-1 fw-bold"><?= $validation->getError('surname') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="artisan-input-group">
                            <label>Email</label>
                            <input type="email" class="artisan-control" name="email" value="<?= old('email') ?>" placeholder="correo@ejemplo.com" required>
                            <?php if ($validation->getError('email')): ?>
                                <div class="text-danger x-small mt-1 fw-bold"><?= $validation->getError('email') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="artisan-input-group">
                            <label>Contraseña</label>
                            <input type="password" class="artisan-control" name="pass" placeholder="Mínimo 8 caracteres" required>
                            <?php if ($validation->getError('pass')): ?>
                                <div class="text-danger x-small mt-1 fw-bold"><?= $validation->getError('pass') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if (session()->get('logged_in') && session()->get('perfil_id') == 1): ?>
                    <input type="hidden" name="terms" value="checked">
                <?php else: ?>
                    <div class="artisan-check">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">Acepto los <a href="<?= base_url('/terminosYCondiciones') ?>" class="fw-bold text-cva-brown" target="_blank">Términos y Condiciones</a></label>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn-auth-primary">
                    <i class="bi bi-check-lg me-2"></i> Finalizar Registro
                </button>
            </form>

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