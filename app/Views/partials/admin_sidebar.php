<aside class="admin-sidebar">
    <div class="sidebar-header">
        <div class="d-flex align-items-center justify-content-between w-100">
            <a href="<?= base_url('/admin-dashboard') ?>" class="sidebar-logo">
                <i class="bi bi-hammer text-gold"></i>
                <span>CVA ADMIN</span>
            </a>
            <button class="btn text-white d-lg-none p-0" id="sidebarClose">
                <i class="bi bi-x-lg fs-4"></i>
            </button>
        </div>
    </div>

    <nav class="sidebar-nav">
        <!-- 1. DASHBOARD & MÉTRICAS -->
        <div class="sidebar-group-label">Métricas y Control</div>
        <a href="<?= base_url('/admin-dashboard') ?>" class="nav-item-admin <?= (current_url() == base_url('/admin-dashboard')) ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <!-- 2. OPERACIONES TALLER -->
        <div class="sidebar-group-label">Operaciones Taller</div>
        <a href="<?= base_url('/ventas-list') ?>" class="nav-item-admin <?= (current_url() == base_url('/ventas-list')) ? 'active' : '' ?>">
            <i class="bi bi-tools"></i>
            <span>Ventas</span>
            <?= view_cell('\App\Cells\AdminSidebarCell::renderSolicitadosBadge') ?>
        </a>
        <a href="<?= base_url('/consultas') ?>" class="nav-item-admin <?= (current_url() == base_url('/consultas')) ? 'active' : '' ?>">
            <i class="bi bi-chat-dots"></i>
            <span>Consultas</span>
        </a>
        <a href="<?= base_url('/admin/galeria') ?>" class="nav-item-admin <?= (current_url() == base_url('/admin/galeria')) ? 'active' : '' ?>">
            <i class="bi bi-images"></i>
            <span>Galería</span>
        </a>

        <!-- 3. INVENTARIO Y CATÁLOGO -->
        <div class="sidebar-group-label">Catálogo de Obras</div>
        <a href="<?= base_url('/crud-productos') ?>" class="nav-item-admin <?= (current_url() == base_url('/crud-productos')) ? 'active' : '' ?>">
            <i class="bi bi-box-seam"></i>
            <span>Productos</span>
        </a>
        <a href="<?= base_url('/crud-categorias') ?>" class="nav-item-admin <?= (current_url() == base_url('/crud-categorias')) ? 'active' : '' ?>">
            <i class="bi bi-tags"></i>
            <span>Categorías</span>
        </a>

        <!-- 4. CONFIGURACIÓN -->
        <div class="sidebar-group-label">Seguridad y Perfil</div>
        <a href="<?= base_url('/crud-usuarios') ?>" class="nav-item-admin <?= (current_url() == base_url('/crud-usuarios')) ? 'active' : '' ?>">
            <i class="bi bi-people"></i>
            <span>Usuarios</span>
        </a>
        <a href="<?= base_url('/perfil') ?>" class="nav-item-admin <?= (current_url() == base_url('/perfil')) ? 'active' : '' ?>">
            <i class="bi bi-person-gear"></i>
            <span>Mi Perfil</span>
        </a>

        <!-- Opción Salir a la Web -->
        <div class="mt-5 px-4 mb-4">
            <a href="<?= base_url('/') ?>" class="nav-item-admin-special">
                <i class="bi bi-house-door"></i>
                <span>Volver al Sitio</span>
            </a>
        </div>
    </nav>
</aside>