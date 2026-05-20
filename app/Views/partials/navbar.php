<?php
// Recibir datos de sesión
$isLogged = session()->get('logged_in');
$nombre = session()->get('nombre');
$imagen = session()->get('imagen');
$perfil = session()->get('perfil_id');

// Detectar página activa (ejemplo básico)
$active_inicio = (current_url() == base_url('/')) ? 'active' : '';
$active_productos = (strpos(current_url(), 'productos') !== false) ? 'active' : '';
$active_comercializacion = (strpos(current_url(), 'comercializacion') !== false) ? 'active' : '';
$active_info = (strpos(current_url(), 'quienesSomos') !== false) ? 'active' : '';
$active_contacto = (strpos(current_url(), 'informacionContacto') !== false) ? 'active' : '';
$active_galeria = (strpos(current_url(), 'galeria') !== false) ? 'active' : '';
$active_consultas = (strpos(current_url(), 'consultas') !== false) ? 'active' : '';

// Carrito
$cartCount = 0;
if ($env_cart_enabled) {
  $cartCount = \Config\Services::cart()->totalItems();
}
?>

<!-- NAVBAR PRINCIPAL (ESTRUCTURA HÍBRIDA ARTISAN) -->
<nav class="navbar navbar-expand-lg artisan-main-nav sticky-top">
  <div class="container-fluid px-3 px-lg-5 d-flex align-items-center justify-content-between">

    <div class="d-lg-none d-flex align-items-center gap-2" style="flex: 1;">
      <button class="boton-icon-circle" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
        <i class="bi bi-list fs-4"></i>
      </button>

      <?php if ($env_cart_enabled && $isLogged): ?>
        <a href="<?= base_url('muestro') ?>" class="boton-icon-circle position-relative text-decoration-none">
          <i class="bi bi-cart3 fs-5"></i>
          <?php if ($cartCount > 0): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
              <?= $cartCount ?>
            </span>
          <?php endif; ?>
        </a>
      <?php endif; ?>
    </div>

    <!-- [DESKTOP: IZQUIERDA / MÓVIL: CENTRO] LOGO -->
    <div class="d-flex align-items-center justify-content-center justify-content-lg-start" style="flex: 1;">
      <a class="navbar-brand d-flex align-items-center gap-2 m-0" href="<?= base_url('/') ?>">
        <img src="<?= base_url('assets/img/branding/cva2.png') ?>" alt="Logo" class="logo-img-nav">
        <h1 class="titulo-logo d-none d-lg-block">CVA Muebles</h1>
      </a>
    </div>

    <!-- [DESKTOP: CENTRO] NAVEGACIÓN (Oculto en móvil) -->
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav" style="flex: 2;">
      <ul class="navbar-nav gap-1">
        <li class="nav-item"><a class="nav-link-custom <?= $active_inicio ?>" href="<?= base_url('/') ?>">Inicio</a></li>
        <li class="nav-item"><a class="nav-link-custom <?= $active_productos ?>" href="<?= base_url('productos') ?>">Productos</a></li>
        <li class="nav-item"><a class="nav-link-custom <?= $active_comercializacion ?>" href="<?= base_url('comercializacion') ?>">Comercialización</a></li>
        <li class="nav-item"><a class="nav-link-custom <?= $active_info ?>" href="<?= base_url('quienesSomos') ?>">Información</a></li>
        <li class="nav-item"><a class="nav-link-custom <?= $active_galeria ?>" href="<?= base_url('galeria') ?>">Galería</a></li>
        <li class="nav-item"><a class="nav-link-custom <?= $active_contacto ?>" href="<?= base_url('informacionContacto') ?>">Contacto</a></li>
      </ul>
    </div>

    <!-- [DERECHA] ICONOS USUARIO / AUTH -->
    <div class="d-flex align-items-center justify-content-end gap-2" style="flex: 1;">
      <?php if ($env_cart_enabled && $isLogged): ?>
        <a href="<?= base_url('muestro') ?>" class="boton-icon-circle d-none d-lg-flex position-relative text-decoration-none">
          <i class="bi bi-cart3 fs-5"></i>
          <?php if ($cartCount > 0): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
              <?= $cartCount ?>
            </span>
          <?php endif; ?>
        </a>
      <?php endif; ?>

      <?php if (!$isLogged): ?>
        <div class="auth-pill-artisan d-none d-lg-flex">
          <a href="<?= base_url('login') ?>" class="auth-pill-link">Ingresar</a>
          <div class="auth-pill-divider"></div>
          <a href="<?= base_url('registro') ?>" class="auth-pill-link">Registrarse</a>
        </div>
      <?php else: ?>
        <button class="boton-icon-circle d-none d-lg-flex" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
          <img src="<?= base_url('assets/img/ui/icons/person.svg') ?>" alt="Perfil" class="icono-svg">
        </button>
      <?php endif; ?>
    </div>

  </div>
</nav>

<!-- MENÚ LATERAL (OFFCANVAS) -->
<div class="offcanvas offcanvas-end menu-lateral shadow-lg border-0" tabindex="-1" id="offcanvasNavbar">
  <div class="offcanvas-header shadow-sm">
    <div class="d-flex align-items-center">
      <img src="<?= base_url('assets/img/branding/cva2.png') ?>" alt="Logo" width="35px" class="me-2">
      <h5 class="offcanvas-title fw-bold text-cva-brown">CVA Muebles</h5>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body p-0">
    <?php if ($isLogged): ?>
      <div class="user-profile-card p-4 text-center">
        <div class="avatar-wrapper mx-auto mb-3" style="width: 80px; height: 80px;">
          <div class="avatar-bg shadow-sm">
            <?php if (!empty($imagen)): ?><img src="<?= base_url('assets/uploads/perfil/' . $imagen) ?>" alt="Perfil"><?php else: ?><i class="bi bi-person-fill text-secondary" style="font-size: 3rem;"></i><?php endif; ?>
          </div>
        </div>
        <h5 class="mb-1 fw-bold"><?= esc($nombre) ?></h5>
        <span class="badge bg-cva-brown rounded-pill px-3 py-1 mb-2" style="font-size: 0.6rem;"><?= ($perfil == 1) ? 'ADMINISTRADOR' : 'CLIENTE CVA' ?></span>
        <br><a href="<?= base_url('/perfil') ?>" class="text-decoration-none small text-gold fw-bold">MI PERFIL <i class="bi bi-chevron-right"></i></a>
      </div>
    <?php else: ?>
      <div class="auth-section-mobile p-4 text-center">
        <div class="avatar-wrapper mx-auto mb-3" style="width: 60px; height: 60px;">
          <div class="avatar-bg shadow-sm" style="background: white; border: 1px solid var(--cva-brown); display: flex; align-items: center; justify-content: center; border-radius: 50%;">
            <i class="bi bi-person-plus text-cva-brown" style="font-size: 1.8rem;"></i>
          </div>
        </div>
        <p class="small text-muted mb-3">Bienvenido a nuestra carpintería artesanal.</p>
        <div class="d-grid gap-2">
          <a href="<?= base_url('login') ?>" class="btn btn-brown-solid rounded-pill">Iniciar Sesión</a>
          <a href="<?= base_url('registro') ?>" class="btn btn-outline-brown rounded-pill">Registrarse</a>
        </div>
      </div>
    <?php endif; ?>
    <div class="menu-navigation-mobile d-lg-none p-3 border-bottom">
      <div class="list-group list-group-flush rounded-4 overflow-hidden border shadow-sm">
        <a href="<?= base_url('/') ?>" class="list-group-item list-group-item-action <?= $active_inicio == 'active' ? 'active-sidebar' : '' ?>"><i class="bi bi-house-door me-3"></i> Inicio</a>
        <a href="<?= base_url('productos') ?>" class="list-group-item list-group-item-action <?= $active_productos == 'active' ? 'active-sidebar' : '' ?>"><i class="bi bi-box-seam me-3"></i> Productos</a>
        <a href="<?= base_url('comercializacion') ?>" class="list-group-item list-group-item-action <?= $active_comercializacion == 'active' ? 'active-sidebar' : '' ?>"><i class="bi bi-truck me-3"></i> Comercialización</a>
        <a href="<?= base_url('quienesSomos') ?>" class="list-group-item list-group-item-action <?= $active_info == 'active' ? 'active-sidebar' : '' ?>"><i class="bi bi-info-circle me-3"></i> Información</a>
        <a href="<?= base_url('galeria') ?>" class="list-group-item list-group-item-action <?= $active_galeria == 'active' ? 'active-sidebar' : '' ?>"><i class="bi bi-images me-3"></i> Galería</a>
        <a href="<?= base_url('informacionContacto') ?>" class="list-group-item list-group-item-action <?= $active_contacto == 'active' ? 'active-sidebar' : '' ?>"><i class="bi bi-chat-left-text me-3"></i> Contacto</a>
      </div>
    </div>
    <div class="menu-options-container p-3">
      <?php if ($isLogged): ?>
        <?php if ($perfil == 1): ?>
          <p class="sidebar-section-label">ADMINISTRACIÓN</p>
          <div class="list-group list-group-flush rounded-4 overflow-hidden border shadow-sm mb-4">
            <a href="<?= base_url('/admin-dashboard') ?>" class="list-group-item list-group-item-action"><i class="bi bi-speedometer2 me-3 text-cva-gold"></i> Dashboard</a>
            <a href="<?= base_url('/ventas-list') ?>" class="list-group-item list-group-item-action"><i class="bi bi-receipt me-3 text-cva-gold"></i> Ventas/Pedidos</a>
            <a href="<?= base_url('/consultas') ?>" class="list-group-item list-group-item-action"><i class="bi bi-chat-dots me-3 text-cva-gold"></i> Consultas</a>
            <a href="<?= base_url('/crud-productos') ?>" class="list-group-item list-group-item-action"><i class="bi bi-box-seam me-3 text-cva-gold"></i> Productos</a>
            <a href="<?= base_url('/admin/galeria') ?>" class="list-group-item list-group-item-action"><i class="bi bi-images me-3 text-cva-gold"></i> Moderar Galería</a>
          </div>
        <?php endif; ?>
        <p class="sidebar-section-label">MI CUENTA</p>
        <div class="list-group list-group-flush rounded-4 overflow-hidden border shadow-sm">
          <a href="<?= base_url('/mis-favoritos') ?>" class="list-group-item list-group-item-action"><i class="bi bi-heart me-3 text-danger"></i> Favoritos</a>
          <a href="<?= base_url('/ventas_lista') ?>" class="list-group-item list-group-item-action"><i class="bi bi-bag-check me-3 text-cva-gold"></i> Mis Compras</a>
          <a href="<?= base_url('/logout') ?>" class="list-group-item list-group-item-action text-danger fw-bold"><i class="bi bi-box-arrow-right me-3"></i> Cerrar Sesión</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  function adjustOffcanvasPosition() {
    const offcanvas = document.getElementById('offcanvasNavbar');
    if (window.innerWidth < 992) {
      offcanvas.classList.remove('offcanvas-end');
      offcanvas.classList.add('offcanvas-start');
    } else {
      offcanvas.classList.remove('offcanvas-start');
      offcanvas.classList.add('offcanvas-end');
    }
  }
  window.addEventListener('resize', adjustOffcanvasPosition);
  window.addEventListener('DOMContentLoaded', adjustOffcanvasPosition);
</script>