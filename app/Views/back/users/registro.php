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
<style>
    .btn-google {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 0.8rem 1.5rem;
        margin-top: 1rem;
        background-color: #ffffff;
        color: #3c4043;
        border: 1px solid #dadce0;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        text-decoration: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .btn-google:hover {
        background-color: #f8f9fa;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        color: #3c4043;
        transform: translateY(-1px);
    }
    .btn-google img {
        width: 20px;
        height: 20px;
        margin-right: 10px;
    }
    .auth-divider {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 1.5rem 0;
        color: #6c757d;
        font-size: 0.85rem;
    }
    .auth-divider::before,
    .auth-divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #dee2e6;
    }
    .auth-divider:not(:empty)::before { margin-right: .5em; }
    .auth-divider:not(:empty)::after { margin-left: .5em; }
</style>
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

            <?php if (!session()->get('logged_in')): ?>
                <div class="auth-divider">o regístrate más rápido con</div>

                <a href="<?= base_url('usuario/login-google') ?>" class="btn-google">
                    <svg viewBox="0 0 48 48" width="20px" height="20px" style="margin-right:10px;"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/><path fill="none" d="M0 0h48v48H0z"/></svg>
                    Continuar con Google
                </a>
            <?php endif; ?>

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