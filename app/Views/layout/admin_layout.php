<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?? 'Panel Control - CVA Muebles' ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Lora:wght@700&display=swap" rel="stylesheet">

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css')?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Admin Design System -->
    <link rel="stylesheet" href="<?= base_url('assets/css/base/global.css?v=3.0')?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/admin-panel.css?v=28.0')?>">


    <?= $this->renderSection('extra-css') ?>
</head>
<body class="admin-body">

    <div class="admin-main-container">
        <!-- Sidebar Overlay (Mobile) -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        <?= view('partials/admin_sidebar') ?>

        <!-- Main Content -->
        <main class="admin-content">
            <!-- Top Bar / Header Contextual -->
            <header class="admin-topbar d-flex justify-content-between align-items-center mb-5">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-admin-toggle d-lg-none" id="sidebarToggle" onclick="toggleSidebar(event)">
                        <i class="bi bi-list fs-3"></i>
                    </button>
                    <nav aria-label="breadcrumb" class="d-none d-sm-block">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url('/admin-dashboard') ?>" class="text-muted text-decoration-none small fw-bold">DASHBOARD</a></li>
                            <?= $this->renderSection('breadcrumbs') ?>
                        </ol>
                    </nav>
                </div>
                <div class="admin-user-zone">
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center gap-3 text-decoration-none dropdown-toggle hide-caret" data-bs-toggle="dropdown">
                            <div class="text-end d-none d-md-block me-1">
                                <div class="small fw-bold text-cva-brown mb-0 lh-1" style="font-size: 0.95rem;"><?= esc(session()->get('nombre')) ?> <?= esc(session()->get('apellido')) ?></div>
                                <div class="x-small text-gold fw-bold mt-1" style="font-size: 0.7rem; letter-spacing: 1px;">MODO ADMINISTRADOR</div>
                            </div>
                            <div class="avatar-container position-relative">
                                <?php 
                                    $nombre_user = session()->get('nombre') ?? 'A';
                                    $imagen_user = session()->get('imagen');
                                    $inicial = strtoupper(substr($nombre_user, 0, 1));
                                ?>
                                <div class="avatar-circle-wrapper" style="width: 50px; height: 50px;">
                                    <?php if ($imagen_user): ?>
                                        <img src="<?= base_url('assets/uploads/perfil/' . $imagen_user) ?>" 
                                             class="avatar-circle shadow-sm border border-gold" 
                                             style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;" 
                                             alt="Perfil"
                                             onerror="this.style.display='none'; document.getElementById('avatar-fallback').style.display='flex';">
                                    <?php endif; ?>
                                    
                                    <div id="avatar-fallback" 
                                         class="avatar-circle bg-brown text-white fw-bold d-flex align-items-center justify-content-center shadow-sm border border-gold" 
                                         style="width: 100%; height: 100%; border-radius: 50%; font-size: 1.2rem; <?= $imagen_user ? 'display:none;' : '' ?>">
                                        <?= $inicial ?>
                                    </div>
                                </div>
                                <span class="position-absolute bottom-0 end-0 badge border border-2 border-light rounded-circle bg-success p-2" style="transform: translate(20%, 20%); z-index: 10;"><span class="visually-hidden">online</span></span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-3 mt-3" style="min-width: 220px;">
                            <li><h6 class="dropdown-header x-small text-uppercase opacity-50 fw-bold mb-2">Mi Perfil</h6></li>
                            <li><a class="dropdown-item rounded-3 py-2 px-3 mb-1" href="<?= base_url('/perfil') ?>"><i class="bi bi-person-badge me-2 text-gold"></i> Mis Datos</a></li>
                            <li><a class="dropdown-item rounded-3 py-2 px-3" href="<?= base_url('/perfil') ?>"><i class="bi bi-shield-lock me-2 text-gold"></i> Seguridad</a></li>
                            <li><hr class="dropdown-divider my-3"></li>
                            <li><a class="dropdown-item rounded-3 py-2 px-3 text-danger" href="<?= base_url('/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión Segura</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <script src="<?= base_url('assets/vendor/bootstrap/bootstrap.bundle.min.js')?>" ></script>
    <script>
        /**
         * Función infalible para alternar el sidebar
         */
        function toggleSidebar(e) {
            if(e) e.preventDefault();
            console.log("Toggle Sidebar ejecutado");
            const body = document.body;
            body.classList.toggle('sidebar-visible');
            
            // Bloquear scroll cuando está abierto
            if (body.classList.contains('sidebar-visible')) {
                body.style.overflow = 'hidden';
            } else {
                body.style.overflow = '';
            }
        }

        // También permitimos cerrar al hacer clic en el overlay o la X
        document.addEventListener('DOMContentLoaded', function() {
            const overlay = document.getElementById('sidebarOverlay');
            const closeBtn = document.getElementById('sidebarClose');
            
            if(overlay) overlay.addEventListener('click', function() {
                document.body.classList.remove('sidebar-visible');
                document.body.style.overflow = '';
            });
            
            if(closeBtn) closeBtn.addEventListener('click', function() {
                document.body.classList.remove('sidebar-visible');
                document.body.style.overflow = '';
            });
        });
        function submitAction(url, message) {
            if (confirm(message)) {
                const form = document.getElementById('global-action-form');
                form.action = url;
                form.submit();
            }
        }
    </script>
    
    <!-- Formulario Global para acciones POST Seguras -->
    <form id="global-action-form" method="POST" style="display: none;">
        <?= csrf_field() ?>
    </form>
    <?= $this->renderSection('extra-js') ?>
</body>
</html>
