<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('extra-css') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/admin-sales.css?v=30.0')?>">
<?= $this->endSection() ?>

<?= $this->section('breadcrumbs') ?>
<li class="breadcrumb-item active small fw-bold text-gold" aria-current="page">CONTROL DE PEDIDOS</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Encabezado Estilo Artisan -->
<div class="row mb-5 align-items-center g-4">
    <div class="col-lg-7">
        <div class="d-flex align-items-center gap-3 gap-md-4">
            <div class="dashboard-icon-main bg-brown text-gold shadow">
                <i class="bi bi-hammer"></i>
            </div>
            <div>
                <h1 class="display-6 display-md-5 fw-bold text-cva-brown mb-1">Gestión de Pedidos</h1>
                <p class="text-muted mb-0 small"><i class="bi bi-calendar-check text-gold me-1"></i> Control de producción y entregas.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-5 text-lg-end">
        <a href="<?= base_url('ventas/nuevo-personalizado') ?>" class="btn btn-admin-primary rounded-pill px-4 py-2 shadow-gold w-sm-100 justify-content-center">
            <i class="bi bi-plus-circle-fill me-2"></i> NUEVO PEDIDO
        </a>
    </div>
</div>

<!-- Mensajes modularizados -->
<?= view('components/alert_message') ?>

<!-- KPIs de Ventas y Producción -->
<div class="row g-3 g-md-4 mb-5">
    <div class="col-6 col-md-3">
        <div class="admin-card-v2 p-3 p-md-4 border-start border-4 border-info h-100 shadow-sm cursor-pointer" onclick="filterByStatus('ALL')">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="d-block x-small text-uppercase fw-bold text-muted mb-1">Pedidos <?= $nombreMes ?></span>
                    <h3 class="fw-bold text-cva-brown mb-0"><?= $counts['mensuales'] ?></h3>
                </div>
                <div class="bg-light text-info p-2 p-md-3 rounded-circle d-none d-sm-block">
                    <i class="bi bi-receipt fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="admin-card-v2 p-3 p-md-4 border-start border-4 h-100 shadow-sm cursor-pointer" style="border-color: #0284c7 !important;" onclick="filterByStatus('EN_PROCESO')">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="d-block x-small text-uppercase fw-bold text-muted mb-1">En Taller</span>
                    <h3 class="fw-bold text-proceso mb-0"><?= $counts['en_proceso'] ?></h3>
                </div>
                <div class="bg-proceso-soft text-proceso p-2 p-md-3 rounded-circle d-none d-sm-block">
                    <i class="bi bi-tools fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="admin-card-v2 p-3 p-md-4 border-start border-4 border-warning h-100 shadow-sm cursor-pointer" onclick="filterByStatus('PENDIENTE')">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="d-block x-small text-uppercase fw-bold text-muted mb-1">Pendientes</span>
                    <h3 class="fw-bold text-warning mb-0"><?= $counts['pendientes'] ?></h3>
                </div>
                <div class="bg-light text-warning p-2 p-md-3 rounded-circle d-none d-sm-block">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="admin-card-v2 p-3 p-md-4 border-start border-4 border-success h-100 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="d-block x-small text-uppercase fw-bold text-muted mb-1">Recaudación Histórica</span>
                    <h4 class="fw-bold text-success mb-0">$ <?= number_format($counts['ingresos'], 0, ',', '.') ?></h4>
                    <span class="d-block text-muted" style="font-size: 0.65rem;">Total de pagos cobrados</span>
                </div>
                <div class="bg-light text-success p-2 p-md-3 rounded-circle d-none d-sm-block">
                    <i class="bi bi-cash-stack fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$has_solicitados = !empty($solicitados);
$active_tab = $has_solicitados ? 'solicitudes' : 'activos';
?>

<!-- Selector de Pestañas Premium (Segmented Tabs) -->
<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-center justify-content-md-start">
            <ul class="nav nav-pills custom-segmented-tabs p-1 bg-light rounded-4 shadow-sm border" id="pedidosTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $active_tab === 'activos' ? 'active' : '' ?> rounded-4 px-4 py-2-5 fw-bold text-uppercase x-small d-flex align-items-center gap-2"
                        id="activos-tab"
                        data-bs-toggle="pill"
                        data-bs-target="#panel-activos"
                        type="button"
                        role="tab"
                        aria-controls="panel-activos"
                        aria-selected="<?= $active_tab === 'activos' ? 'true' : 'false' ?>">
                        <i class="bi bi-folder2-open text-gold"></i>
                        <span>Pedidos Activos</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $active_tab === 'solicitudes' ? 'active' : '' ?> rounded-4 px-4 py-2-5 fw-bold text-uppercase x-small d-flex align-items-center gap-2 position-relative"
                        id="solicitudes-tab"
                        data-bs-toggle="pill"
                        data-bs-target="#panel-solicitudes"
                        type="button"
                        role="tab"
                        aria-controls="panel-solicitudes"
                        aria-selected="<?= $active_tab === 'solicitudes' ? 'true' : 'false' ?>">
                        <i class="bi bi-inbox text-gold"></i>
                        <span>Solicitudes</span>
                        <?php if ($has_solicitados): ?>
                            <span class="badge-pulse-gold ms-1"></span>
                            <span class="badge bg-gold text-brown rounded-pill x-small fw-bold px-2 py-1 shadow-sm"><?= count($solicitados) ?></span>
                        <?php else: ?>
                            <span class="badge bg-secondary-soft text-muted rounded-pill x-small fw-bold px-2 py-1">0</span>
                        <?php endif; ?>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="tab-content" id="pedidosTabContent">
    <!-- 1. PANEL PEDIDOS ACTIVOS -->
    <div class="tab-pane fade <?= $active_tab === 'activos' ? 'show active' : '' ?>" id="panel-activos" role="tabpanel" aria-labelledby="activos-tab">

        <!-- FILTROS Y LISTADO PRINCIPAL -->
        <div class="admin-card-v2 mb-4 border-0 shadow-sm overflow-hidden">
            <div class="bg-light p-3 border-bottom d-flex align-items-center justify-content-between" style="min-height: 52px;">
                <h6 class="mb-0 fw-bold text-cva-brown"><i class="bi bi-filter-right me-2 text-gold"></i> Filtros de Búsqueda (Pedidos Aprobados)</h6>
                <div id="filter-status" class="x-small fw-bold text-success" style="opacity: 0; transition: opacity 0.2s ease;">
                    <span class="spinner-grow spinner-grow-sm me-1"></span> ACTUALIZANDO...
                </div>
            </div>
            <div class="p-4">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-6 col-md-12">
                        <label class="x-small fw-bold text-muted text-uppercase mb-2">Buscador en tiempo real</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 border-2" style="border-radius: 1rem 0 0 1rem;">
                                <i class="bi bi-search text-gold"></i>
                            </span>
                            <input type="text" id="input-search" class="form-control border-start-0 border-2 py-2"
                                style="border-radius: 0 1rem 1rem 0;"
                                placeholder="ID, nombre o usuario...">
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <label class="x-small fw-bold text-muted text-uppercase mb-2">Estado</label>
                        <select id="select-status" class="form-select border-2 py-2 rounded-3 x-small fw-bold text-uppercase">
                            <option value="ALL">TODOS</option>
                            <option value="PENDIENTE">🟠 PENDIENTE</option>
                            <option value="EN_PROCESO">🔵 EN PROCESO</option>
                            <option value="TERMINADO">🟢 TERMINADO</option>
                            <option value="ENTREGADO">🔘 ENTREGADO</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-6">
                        <button type="button" id="btn-reset" class="btn btn-light border py-2 w-100 rounded-3 shadow-sm x-small fw-bold text-uppercase">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Pedidos -->
        <div class="admin-card-v2 border-0 shadow-sm overflow-hidden mb-5">
            <div class="table-responsive-stack">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase x-small fw-bold text-muted">Cliente / Origen</th>
                            <th class="py-3 text-uppercase x-small fw-bold text-muted d-none d-lg-table-cell">ID</th>
                            <th class="py-3 text-uppercase x-small fw-bold text-muted d-none d-lg-table-cell">Fecha y Hora</th>
                            <th class="py-3 text-uppercase x-small fw-bold text-muted text-end">Total / Pagado</th>
                            <th class="py-3 text-uppercase x-small fw-bold text-muted text-center">Estado</th>
                            <th class="py-3 text-uppercase x-small fw-bold text-muted text-center">Prioridad</th>
                            <th class="pe-4 py-3 text-uppercase x-small fw-bold text-muted text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="order-table-body">
                        <?php foreach ($ventas as $v): ?>
                            <tr class="order-row"
                                data-search="<?= $v['search_data'] ?>"
                                data-estado="<?= $v['estado'] ?>">
                                <td class="ps-4" data-label="CLIENTE">
                                    <div class="d-flex align-items-center gap-3 py-1 order-info-wrapper">
                                        <div class="position-relative">
                                            <div class="avatar-premium bg-brown text-gold rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm">
                                                <?= strtoupper(substr($v['nombre'] ?? 'M', 0, 1)) ?><?= strtoupper(substr($v['apellido'] ?? 'P', 0, 1)) ?>
                                            </div>
                                            <span class="position-absolute top-0 start-0 badge rounded-pill bg-dark shadow-sm d-lg-none" style="transform: translate(-30%, -30%); font-size: 0.6rem; border: 1px solid var(--cva-gold);">#<?= $v['id'] ?></span>
                                        </div>
                                        <div class="order-text-details">
                                            <div class="fw-bold text-cva-brown"><?= esc(($v['nombre'] ?? 'VENTA') . ' ' . ($v['apellido'] ?? 'MANUAL')) ?></div>
                                            <div class="d-flex gap-2 align-items-center">
                                                <span class="badge bg-light text-muted border d-none d-md-inline-block" style="font-size: 0.65rem;">PEDIDO: #<?= $v['id'] ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-lg-table-cell" data-label="ID">
                                    <span class="badge bg-light text-muted border">#<?= $v['id'] ?></span>
                                </td>
                                <td class="d-none d-lg-table-cell" data-label="FECHA">
                                    <div class="fw-bold text-cva-brown"><?= date('d/m/Y', strtotime($v['fecha'])) ?></div>
                                    <div class="x-small text-muted"><?= date('H:i', strtotime($v['fecha'])) ?> hs</div>
                                </td>
                                <td class="text-end" data-label="TOTAL / PAGADO">
                                    <div class="order-total-info">
                                        <div class="fw-bold text-dark mb-1">
                                            $ <?= number_format($v['total_venta'], 0, ',', '.') ?>
                                        </div>
                                        <div class="x-small">
                                            <?php if ($v['total_pagado'] >= $v['total_venta']): ?>
                                                <span class="badge bg-success-soft text-success border border-success-subtle px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.65rem;">
                                                    <i class="bi bi-check-circle-fill me-1"></i> PAGADO
                                                </span>
                                            <?php elseif ($v['total_pagado'] > 0): ?>
                                                <span class="badge bg-warning-soft text-warning border border-warning-subtle px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.65rem;">
                                                    COBRADO: $<?= number_format($v['total_pagado'], 0, ',', '.') ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-muted border px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.65rem;">
                                                    SIN PAGO
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center" data-label="ESTADO">
                                    <?php
                                    $badge_class = "bg-light text-muted border";
                                    $icon = "bi-clock";
                                    if ($v['estado'] == 'PENDIENTE') {
                                        $badge_class = "bg-warning-soft text-warning border-warning";
                                        $icon = "bi-hourglass-split";
                                    }
                                    if ($v['estado'] == 'EN_PROCESO') {
                                        $badge_class = "bg-proceso-soft text-proceso border-proceso";
                                        $icon = "bi-tools";
                                    }
                                    if ($v['estado'] == 'TERMINADO') {
                                        $badge_class = "bg-success-soft text-success border-success";
                                        $icon = "bi-check-all";
                                    }
                                    if ($v['estado'] == 'ENTREGADO') {
                                        $badge_class = "bg-dark text-white";
                                        $icon = "bi-truck";
                                    }
                                    ?>
                                    <span class="badge px-3 py-2 rounded-pill x-small fw-bold <?= $badge_class ?>" style="min-width: 100px;">
                                        <i class="bi <?= $icon ?> me-1"></i>
                                        <?= strtoupper($v['estado']) ?>
                                    </span>
                                </td>
                                <td class="text-center" data-label="PRIORIDAD">
                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                        <a href="<?= base_url('ventas/subir/' . $v['id']) ?>"
                                           class="btn btn-action-premium text-gold border-gold border-opacity-25 shadow-sm p-0 d-flex align-items-center justify-content-center btn-prioridad-subir"
                                           style="width: 32px; height: 32px;"
                                           title="Subir prioridad">
                                            <i class="bi bi-arrow-up fs-6"></i>
                                        </a>
                                        <a href="<?= base_url('ventas/bajar/' . $v['id']) ?>"
                                           class="btn btn-action-premium text-gold border-gold border-opacity-25 shadow-sm p-0 d-flex align-items-center justify-content-center btn-prioridad-bajar"
                                           style="width: 32px; height: 32px;"
                                           title="Bajar prioridad">
                                            <i class="bi bi-arrow-down fs-6"></i>
                                        </a>
                                    </div>
                                </td>
                                <td class="pe-4 text-center" data-label="ACCIONES">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="<?= base_url('ventas/gestion/' . $v['id']) ?>"
                                            class="btn btn-action-premium text-gold border-gold border-opacity-25 shadow-sm"
                                            title="Gestionar Pedido">
                                            <i class="bi bi-sliders2"></i>
                                        </a>
                                        <a href="<?= base_url('factura/' . $v['id']) ?>"
                                            class="btn btn-action-premium text-danger border-danger border-opacity-25 shadow-sm"
                                            title="Ver Comprobante">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <!-- Filas de Estados Vacíos -->
                        <tr id="no-results-row" style="display: none;">
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-search display-4 text-muted opacity-25"></i>
                                <p class="text-muted mt-3">No hay pedidos que coincidan con la búsqueda.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- 2. PANEL SOLICITUDES PENDIENTES -->
    <div class="tab-pane fade <?= $active_tab === 'solicitudes' ? 'show active' : '' ?>" id="panel-solicitudes" role="tabpanel" aria-labelledby="solicitudes-tab">

        <div class="admin-card-v2 mb-5 border-0 shadow-lg overflow-hidden animate__animated animate__fadeIn">
            <div class="bg-brown p-3 px-4 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-gold">
                    <i class="bi bi-bell-fill me-2 <?= $has_solicitados ? 'animate__animated animate__swing animate__infinite' : '' ?>"></i>
                    SOLICITUDES POR APROBAR (<?= count($solicitados) ?>)
                </h6>
                <?php if ($has_solicitados): ?>
                    <span class="badge bg-gold text-brown x-small fw-bold px-3">REVISIÓN REQUERIDA</span>
                <?php else: ?>
                    <span class="badge bg-light text-muted x-small fw-bold px-3">AL DÍA</span>
                <?php endif; ?>
            </div>

            <?php if ($has_solicitados): ?>
                <div class="table-responsive-stack">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="x-small text-uppercase text-muted fw-bold">
                                <th class="ps-4 py-3 col-solicitud-cliente">Cliente</th>
                                <th class="py-3 col-solicitud-id d-none d-lg-table-cell">ID</th>
                                <th class="py-3 col-solicitud-fecha d-none d-lg-table-cell">Fecha</th>
                                <th class="py-3 text-end col-solicitud-monto">Monto</th>
                                <th class="py-3 text-center col-solicitud-acciones">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($solicitados as $s): ?>
                                <tr class="solicitud-row">
                                    <td class="ps-4 col-solicitud-cliente" data-label="CLIENTE">
                                        <div class="d-flex align-items-center gap-3 py-1 order-info-wrapper">
                                            <div class="position-relative">
                                                <div class="avatar-premium bg-brown text-gold rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm">
                                                    <?= strtoupper(substr($s['nombre'] ?? 'M', 0, 1)) ?><?= strtoupper(substr($s['apellido'] ?? 'P', 0, 1)) ?>
                                                </div>
                                                <span class="position-absolute top-0 start-0 badge rounded-pill bg-dark shadow-sm d-lg-none" style="transform: translate(-30%, -30%); font-size: 0.6rem; border: 1px solid var(--cva-gold);">#<?= $s['id'] ?></span>
                                            </div>
                                            <div class="order-text-details">
                                                <div class="fw-bold text-cva-brown"><?= esc($s['nombre'] . ' ' . $s['apellido']) ?></div>
                                                <div class="d-flex gap-2 align-items-center mt-1">
                                                    <span class="badge bg-light text-muted border d-none d-md-inline-block" style="font-size: 0.65rem;">ID: #<?= $s['id'] ?></span>
                                                    <span class="badge bg-gold-soft text-gold border border-gold border-opacity-25 x-small" style="font-size: 0.6rem;"><?= esc($s['email']) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="col-solicitud-id d-none d-lg-table-cell" data-label="ID"><span class="badge bg-light text-muted">#<?= $s['id'] ?></span></td>
                                    <td class="small fw-bold col-solicitud-fecha d-none d-lg-table-cell" data-label="FECHA"><?= date('d/m/Y H:i', strtotime($s['fecha'])) ?></td>
                                    <td class="text-end fw-bold text-dark col-solicitud-monto" data-label="MONTO">$ <?= number_format($s['total_venta'], 2, ',', '.') ?></td>
                                    <td class="text-center pe-4 col-solicitud-acciones" data-label="GESTIÓN">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="<?= base_url('ventas/gestion/' . $s['id']) ?>" class="btn btn-sm btn-outline-brown btn-solicitud-action rounded-pill px-3 fw-bold">
                                                <i class="bi bi-eye me-1"></i> REVISAR
                                            </a>
                                            <form action="<?= base_url('ventas/actualizar_estado/' . $s['id']) ?>" method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="estado" value="ACEPTADO">
                                                <button type="submit" class="btn btn-sm btn-success btn-solicitud-action rounded-pill px-3 fw-bold">
                                                    <i class="bi bi-check-lg me-1"></i> APROBAR
                                                </button>
                                            </form>
                                            <form action="<?= base_url('ventas/actualizar_estado/' . $s['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('¿Estás seguro de rechazar este pedido? No se mostrará en la lista.')">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="estado" value="RECHAZADO">
                                                <button type="submit" class="btn btn-sm btn-outline-danger btn-solicitud-action rounded-pill px-3 fw-bold">
                                                    <i class="bi bi-x-lg me-1"></i> RECHAZAR
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="p-5 text-center">
                    <i class="bi bi-check2-circle display-4 text-success opacity-25 mb-3 d-block"></i>
                    <h6 class="fw-bold text-brown">No tienes solicitudes nuevas</h6>
                    <p class="text-muted small mb-0">Cuando un cliente realice un pedido desde el carrito, aparecerá aquí para tu aprobación.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>



<?= $this->endSection() ?>

<?= $this->section('extra-js') ?>
<script>
    function filterByStatus(status) {
        const tabEl = document.getElementById('activos-tab');
        if (tabEl) {
            tabEl.click();
        }
        const selectStatus = document.getElementById('select-status');
        selectStatus.value = status;
        selectStatus.dispatchEvent(new Event('change'));
    }

    document.addEventListener('DOMContentLoaded', function() {
        const inputSearch = document.getElementById('input-search');
        const selectStatus = document.getElementById('select-status');
        const rows = document.querySelectorAll('.order-row');
        const noResults = document.getElementById('no-results-row');
        const filterStatus = document.getElementById('filter-status');
        const btnReset = document.getElementById('btn-reset');

        function filterOrders() {
            const searchTerm = inputSearch.value.toLowerCase();
            const statusFilter = selectStatus.value;
            let visibleCount = 0;

            filterStatus.style.opacity = '1';

            rows.forEach(row => {
                const searchData = row.getAttribute('data-search');
                const estado = row.getAttribute('data-estado');

                const matchesSearch = searchData.includes(searchTerm);
                const matchesStatus = (statusFilter === 'ALL' || estado === statusFilter);

                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            noResults.style.display = (visibleCount === 0) ? '' : 'none';

            setTimeout(() => {
                filterStatus.style.opacity = '0';
            }, 300);
        }

        inputSearch.addEventListener('input', filterOrders);
        selectStatus.addEventListener('change', filterOrders);

        btnReset.addEventListener('click', function() {
            inputSearch.value = '';
            selectStatus.value = 'ALL';
            filterOrders();
        });

        // Check for initial filter in URL
        const urlParams = new URLSearchParams(window.location.search);
        const initialStatus = urlParams.get('estado');
        if (initialStatus) {
            selectStatus.value = initialStatus;
        }

        // Lógica de reordenamiento asíncrono y UI optimista para las flechitas de prioridad
        document.querySelectorAll('.btn-prioridad-subir, .btn-prioridad-bajar').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const url = this.getAttribute('href');
                const currentRow = this.closest('.order-row');
                if (!currentRow) return;

                const isSubir = this.classList.contains('btn-prioridad-subir');
                let targetSibling = null;

                // Buscar el vecino visible (arriba o abajo)
                let sibling = isSubir ? currentRow.previousElementSibling : currentRow.nextElementSibling;
                while (sibling) {
                    if (sibling.classList.contains('order-row') && sibling.style.display !== 'none') {
                        targetSibling = sibling;
                        break;
                    }
                    sibling = isSubir ? sibling.previousElementSibling : sibling.nextElementSibling;
                }

                if (targetSibling) {
                    // Animación suave de fade-out temporal para el swap visual
                    currentRow.style.transition = 'background-color 0.3s ease';
                    targetSibling.style.transition = 'background-color 0.3s ease';
                    currentRow.style.backgroundColor = '#fdf8f0';
                    targetSibling.style.backgroundColor = '#fdf8f0';

                    // Realizar el intercambio en el DOM de forma optimista
                    if (isSubir) {
                        currentRow.parentNode.insertBefore(currentRow, targetSibling);
                    } else {
                        currentRow.parentNode.insertBefore(targetSibling, currentRow);
                    }

                    // Limpiar colores de fondo después de la animación
                    setTimeout(() => {
                        currentRow.style.backgroundColor = '';
                        targetSibling.style.backgroundColor = '';
                    }, 800);

                    // Ejecutar la petición asíncrona en segundo plano sin recargar
                    fetch(url, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error al actualizar prioridad en el servidor');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Revertir el intercambio si falla
                        if (isSubir) {
                            currentRow.parentNode.insertBefore(targetSibling, currentRow);
                        } else {
                            currentRow.parentNode.insertBefore(currentRow, targetSibling);
                        }
                        alert('No se pudo guardar el cambio de prioridad. Por favor, intenta de nuevo.');
                    });
                }
            });
        });

        // Inicializar
        filterOrders();
    });
</script>
<?= $this->endSection() ?>