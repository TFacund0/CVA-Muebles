<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('extra-css') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/admin-sales.css?v=33.0')?>">
<?= $this->endSection() ?>

<?= $this->section('breadcrumbs') ?>
    <li class="breadcrumb-item active text-gold fw-bold" aria-current="page">ESTADÍSTICAS DEL TALLER</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Encabezado Dinámico Refinado -->
<div class="row mb-5 align-items-center g-4">
    <div class="col-lg-8">
        <div class="d-flex align-items-center gap-3 gap-md-4 mb-1 mb-md-3">
            <div class="dashboard-icon-main bg-brown text-gold shadow">
                <i class="bi bi-activity"></i>
            </div>
            <div>
                <h1 class="display-6 display-md-5 fw-bold text-cva-brown mb-1">Producción</h1>
                <p class="text-muted mb-0 small"><i class="bi bi-check2-circle text-success me-1"></i> Monitoreo en tiempo real.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4 text-lg-end">
        <div class="stats-timer shadow-sm border w-sm-100 justify-content-center">
            <span class="pulse-dot"></span>
            ACTUALIZACIÓN VIVA: <?= date('H:i') ?>
        </div>
    </div>
</div>

<!-- Grid de KPIs Interactivos -->
<div class="row g-4 mb-5">
    <?php 
    $kpis = [
        ['label' => 'Pendientes', 'val' => $stats['PENDIENTE'], 'icon' => 'bi-hourglass-split', 'color' => 'warning', 'link' => 'ventas-list?estado=PENDIENTE', 'desc' => 'POR INICIAR'],
        ['label' => 'En Taller', 'val' => $stats['EN_PROCESO'], 'icon' => 'bi-hammer', 'color' => 'info', 'link' => 'ventas-list?estado=EN_PROCESO', 'desc' => 'FABRICANDO'],
        ['label' => 'Terminados', 'val' => $stats['TERMINADO'], 'icon' => 'bi-check-all', 'color' => 'success', 'link' => 'ventas-list?estado=TERMINADO', 'desc' => 'PARA ENTREGA'],
        ['label' => 'Consultas', 'val' => $total_consultas, 'icon' => 'bi-chat-left-text', 'color' => 'gold', 'link' => '/consultas', 'desc' => 'INBOX ACTIVO']
    ];
    foreach($kpis as $kpi): 
    ?>
    <div class="col-6 col-md-3">
        <a href="<?= base_url($kpi['link']) ?>" class="text-decoration-none h-100 d-block">
            <div class="kpi-card-premium border-<?= $kpi['color'] ?> h-100">
                <div class="kpi-body p-4 text-center">
                    <div class="kpi-icon-container mx-auto mb-3 bg-<?= $kpi['color'] ?>-soft text-<?= $kpi['color'] ?>">
                        <i class="bi <?= $kpi['icon'] ?>"></i>
                    </div>
                    <h6 class="kpi-label"><?= $kpi['label'] ?></h6>
                    <div class="kpi-value text-cva-brown"><?= $kpi['val'] ?></div>
                    <div class="kpi-footer text-<?= $kpi['color'] ?> fw-bold x-small mt-2">
                        <?= $kpi['desc'] ?> <i class="bi bi-chevron-right ms-1"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <!-- Acciones Rápidas (Volvemos a 8 unidades) -->
    <div class="col-lg-8">
        <div class="admin-card-v2 p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-cva-brown mb-0">
                    <i class="bi bi-lightning-charge-fill text-gold me-2"></i> 
                    Acciones de Gestión Directa
                </h5>
                <span class="badge bg-light text-muted border rounded-pill px-3">Accesos Rápidos</span>
            </div>
            <div class="row g-3 h-100 pb-4">
                <div class="col-12 col-md-6 d-flex">
                    <a href="<?= base_url('ventas/nuevo-personalizado') ?>" class="action-btn-v2 flex-grow-1">
                        <div class="action-icon"><i class="bi bi-hammer"></i></div>
                        <div class="action-text">
                            <span class="d-block fw-bold">Pedido Manual</span>
                            <small class="text-muted">WhatsApp / Local</small>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-md-6 d-flex">
                    <a href="<?= base_url('/alta-producto') ?>" class="action-btn-v2 flex-grow-1">
                        <div class="action-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                        <div class="action-text">
                            <span class="d-block fw-bold">Subir Producto</span>
                            <small class="text-muted">Catálogo</small>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-md-6 d-flex">
                    <a href="<?= base_url('/ventas-list') ?>" class="action-btn-v2 flex-grow-1">
                        <div class="action-icon"><i class="bi bi-card-checklist"></i></div>
                        <div class="action-text">
                            <span class="d-block fw-bold">Ver Ventas</span>
                            <small class="text-muted">Facturación</small>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-md-6 d-flex">
                    <a href="<?= base_url('/consultas') ?>" class="action-btn-v2 flex-grow-1">
                        <div class="action-icon"><i class="bi bi-envelope-paper"></i></div>
                        <div class="action-text">
                            <span class="d-block fw-bold">Inbox Consultas</span>
                            <small class="text-muted">Clientes</small>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-md-6 d-flex">
                    <a href="<?= base_url('/admin/galeria') ?>" class="action-btn-v2 flex-grow-1 position-relative">
                        <?php if (isset($total_galeria_pendientes) && $total_galeria_pendientes > 0): ?>
                            <span class="position-absolute top-0 end-0 m-3 badge rounded-pill bg-danger badge-pill-notification">
                                <?= $total_galeria_pendientes ?>
                            </span>
                        <?php endif; ?>
                        <div class="action-icon"><i class="bi bi-images"></i></div>
                        <div class="action-text">
                            <span class="d-block fw-bold">Moderar Galería</span>
                            <small class="text-muted">Fotos de Clientes</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen de Rendimiento (4 unidades) -->
    <div class="col-lg-4">
        <?php 
            $total_pedidos = $stats['PENDIENTE'] + $stats['EN_PROCESO'] + $stats['TERMINADO'] + $stats['ENTREGADO'];
            $porcentaje = ($total_pedidos > 0) ? round(($stats['ENTREGADO'] / $total_pedidos) * 100) : 0;
        ?>
        <div class="admin-card-v2 h-100 bg-cva-brown text-white border-0 shadow-gold d-flex flex-column p-4">
            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2 text-gold">
                <i class="bi bi-award"></i> RENDIMIENTO HISTÓRICO
            </h6>
            <p class="x-small opacity-75 mb-auto">Métricas de éxito acumuladas en la fabricación y entrega de piezas artesanales.</p>
            
            <div class="production-display-compact p-4 rounded-4 border border-white border-opacity-10 my-4 text-center bg-white bg-opacity-5">
                <div class="text-gold display-4 fw-bold mb-0 lh-1"><?= $stats['ENTREGADO'] ?></div>
                <div class="x-small text-uppercase fw-bold opacity-50 tracking-widest mt-2">Muebles Entregados</div>
            </div>

            <div class="mt-auto">
                <div class="d-flex justify-content-between x-small fw-bold mb-2">
                    <span class="opacity-75">EFICIENCIA GLOBAL</span>
                    <span class="text-gold"><?= $porcentaje ?>%</span>
                </div>
                <div class="progress progress-h10">
                    <div class="progress-bar bg-gold progress-bar-striped progress-bar-animated js-progress-width" data-progress="<?= $porcentaje ?>"></div>
                </div>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection() ?>

<?= $this->section('extra-js') ?>
<script src="<?= base_url('assets/js/admin/admin-progress-bars.js?v=1.0') ?>"></script>
<?= $this->endSection() ?>
