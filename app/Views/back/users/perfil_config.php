<?= $this->extend('layout/main') ?>

<?= $this->section('extra-css') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/profile.css?v=2.0')?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $name = session()->get('nombre');
    $username = session()->get('usuario');
    $surname = session()->get('apellido');
    $email = session()->get('email');
    $profile = session()->get('perfil_id');
    $image = session()->get('imagen');
?>

<div class="container profile-container-v3">
    
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 text-center py-3">
            <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <div class="profile-main-card">
        <!-- Parte Izquierda: Perfil y Navegación -->
        <aside class="profile-sidebar-summary">
            <div class="avatar-circle-v3">
                <?php if (!empty($image)): ?>
                    <img src="<?= imagen_url($image, 'perfil') ?>" alt="Perfil" id="previewImg">
                <?php else: ?>
                    <div class="avatar-placeholder-v3">
                        <i class="bi bi-person-fill"></i>
                    </div>
                <?php endif; ?>
            </div>
            
            <h3 class="fw-bold text-cva-brown mb-1"><?= esc($name) . ' ' . esc($surname) ?></h3>
            <p class="text-muted small mb-3">@<?= esc($username) ?></p>
            
            <span class="badge-role-v3">
                <i class="bi bi-shield-check me-1"></i> <?= ($profile == 1) ? 'ADMINISTRADOR' : 'CLIENTE CVA' ?>
            </span>

            <div class="profile-nav-links">
                <a href="#" class="profile-nav-link" data-bs-toggle="modal" data-bs-target="#modalPassword">
                    <i class="bi bi-key-fill text-gold"></i> Cambiar Contraseña
                </a>
                <?php if($profile == 1): ?>
                    <a href="<?= base_url('/admin-dashboard') ?>" class="profile-nav-link">
                        <i class="bi bi-speedometer2 text-gold"></i> Ir al Dashboard
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('/ventas_lista') ?>" class="profile-nav-link">
                        <i class="bi bi-bag-check text-gold"></i> Mis Pedidos
                    </a>
                <?php endif; ?>
                <hr class="my-4 opacity-50">
                <a href="<?= base_url('/logout') ?>" class="profile-nav-link text-danger">
                    <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                </a>
            </div>
        </aside>

        <!-- Parte Derecha: Formulario de Edición -->
        <main class="profile-form-content">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h2 class="h3 fw-bold text-cva-brown mb-0">Configuración</h2>
                <button type="button" class="btn btn-outline-brown rounded-pill px-4 fw-bold small" id="toggleEdit">
                    <i class="bi bi-pencil-square me-2"></i> EDITAR DATOS
                </button>
            </div>

            <form id="formPerfil" method="post" action="<?= base_url('/guardarCambios') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="artisan-label-v3">Nombre</label>
                        <input type="text" name="name" class="form-control artisan-control-v3" value="<?= esc($name) ?>" disabled required>
                    </div>
                    <div class="col-md-6">
                        <label class="artisan-label-v3">Apellido</label>
                        <input type="text" name="surname" class="form-control artisan-control-v3" value="<?= esc($surname) ?>" disabled required>
                    </div>
                    <div class="col-md-12">
                        <label class="artisan-label-v3">Dirección de Email</label>
                        <input type="email" name="email" class="form-control artisan-control-v3" value="<?= esc($email) ?>" disabled required>
                    </div>
                    <div class="col-md-12">
                        <label class="artisan-label-v3">Nombre de Usuario</label>
                        <input type="text" name="username" class="form-control artisan-control-v3" value="<?= esc($username) ?>" disabled required>
                    </div>
                    
                    <div class="col-md-12 d-none" id="fileGroup">
                        <label class="artisan-label-v3">Actualizar Foto de Perfil</label>
                        <input type="file" name="image" class="form-control artisan-control-v3" accept="image/*">
                    </div>
                </div>

                <div class="mt-5 pt-4 border-top d-none" id="btnGroup">
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-artisan-v3 flex-grow-1">
                            GUARDAR CAMBIOS
                        </button>
                        <button type="button" id="cancelEdit" class="btn btn-light rounded-pill px-5 fw-bold border">
                            CANCELAR
                        </button>
                    </div>
                </div>
            </form>
        </main>
    </div>
</div>

<!-- MODAL CONTRASEÑA -->
<div class="modal fade" id="modalPassword" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header bg-cva-brown text-white border-0 py-4">
                <h5 class="modal-title fw-bold mb-0">Cambiar Contraseña</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('/cambiarPassword') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-5">
                    <div class="mb-4">
                        <label class="artisan-label-v3">Contraseña Actual</label>
                        <input type="password" name="current_password" class="form-control artisan-control-v3" required>
                    </div>
                    <div class="mb-4">
                        <label class="artisan-label-v3">Nueva Contraseña</label>
                        <input type="password" name="new_password" class="form-control artisan-control-v3" required>
                    </div>
                    <div class="mb-0">
                        <label class="artisan-label-v3">Confirmar Nueva Contraseña</label>
                        <input type="password" name="confirm_password" class="form-control artisan-control-v3" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-artisan-v3 w-100 py-3">
                        ACTUALIZAR CLAVE
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('extra-js') ?>
<script src="<?= base_url('assets/js/admin/admin-perfil-config.js?v=1.0') ?>"></script>
<?= $this->endSection() ?>
