<?php
/**
 * Vista de Inicio de Sesión (Login Page)
 *
 * Renderiza la interfaz premium de autenticación de la plataforma para clientes y administradores.
 * Cuenta con una disposición dividida:
 * - Sección Lateral de Branding: Mensaje identitario de la marca y estética de taller de CVA Muebles.
 * - Formulario de Acceso: Entradas seguras protegidas por CSRF y redirección dinámica según el rol.
 *
 * Mensajes de Sesión (Flashdata):
 * - error (string|null): Alertas en caso de credenciales inválidas o sesiones expiradas.
 * - success (string|null): Confirmaciones al registrar cuentas o cerrar sesión con éxito.
 */
?>
<?= $this->extend('layout/main') ?>

<?= $this->section('extra-css') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/auth.css?v=1.0')?>">
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

            <div class="auth-divider">o</div>

            <a href="<?= base_url('usuario/login-google') ?>" class="btn-google">
                <svg viewBox="0 0 48 48" width="20px" height="20px" style="margin-right:10px;"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/><path fill="none" d="M0 0h48v48H0z"/></svg>
                Continuar con Google
            </a>

            <div class="auth-footer">
                ¿Aún no eres parte? <a href="<?= base_url('/registro') ?>">Crea tu cuenta aquí</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
