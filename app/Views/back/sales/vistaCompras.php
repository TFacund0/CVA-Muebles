<?= $this->extend('layout/main') ?>

<?= $this->section('extra-css') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/mis-compras.css?v=1.0')?>">
<?= $this->endSection() ?>


<?= $this->section('content') ?>
<div class="purchases-wrapper">
    <div class="container">
        <!-- Header con Identidad -->
        <div class="mb-5 text-center text-lg-start">
            <span class="cart-header-badge">HISTORIAL ARTESANAL</span>
            <h1 class="cart-title-main">Mis Compras</h1>
            <p class="text-muted fs-5">Seguimiento de tus piezas y proyectos en curso.</p>
            <div class="title-divider-gold"></div>
        </div>

        <?php if (!empty($ventas)): ?>
            <!-- Buscador y Filtros Premium de Pedidos -->
            <div class="row mb-5 g-3 align-items-center animate-fade-in">
                <!-- Filtros (Izquierda) -->
                <div class="col-lg-8 col-md-12">
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-filter-artisan active" data-filter="todos">
                            <i class="bi bi-collection-fill me-1"></i> Todos
                        </button>
                        <button class="btn btn-filter-artisan" data-filter="solicitado">
                            <i class="bi bi-clock-history me-1"></i> Por Aprobar
                        </button>
                        <button class="btn btn-filter-artisan" data-filter="pendiente">
                            <i class="bi bi-hourglass-split me-1"></i> Pendientes
                        </button>
                        <button class="btn btn-filter-artisan" data-filter="en_proceso">
                            <i class="bi bi-hammer me-1"></i> En Taller
                        </button>
                        <button class="btn btn-filter-artisan" data-filter="entregado">
                            <i class="bi bi-truck me-1"></i> Entregados
                        </button>
                        <button class="btn btn-filter-artisan" data-filter="rechazado">
                            <i class="bi bi-x-circle-fill me-1"></i> Rechazados
                        </button>
                    </div>
                </div>

                <!-- Ordenar por (Derecha) -->
                <div class="col-lg-4 col-md-12 text-lg-end">
                    <div class="d-inline-flex align-items-center gap-2 bg-white rounded-pill px-3 py-2 border shadow-sm sort-purchases-wrap">
                        <span class="small text-muted fw-bold text-nowrap"><i class="bi bi-sort-down text-gold me-1"></i> Ordenar:</span>
                        <select id="sort-purchases" class="form-select border-0 bg-transparent py-0 px-2 fw-semibold text-cva-brown sort-purchases-select">
                            <option value="recent">Más recientes</option>
                            <option value="oldest">Más antiguos</option>
                            <option value="high-price">Mayor inversión</option>
                            <option value="low-price">Menor inversión</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Estado Vacío Auxiliar para Filtros de Pedidos -->
            <div id="no-results-purchases" class="text-center py-5 d-none animate-fade-in mb-5 no-results-purchases-box">
                <div class="mb-4">
                    <i class="bi bi-funnel text-gold opacity-25 no-results-icon"></i>
                </div>
                <h4 class="fw-bold text-cva-brown font-lora">No hay pedidos con este estado</h4>
                <p class="text-muted mb-0">Probá seleccionando otra opción de filtrado o de ordenamiento.</p>
            </div>

            <div class="row">
                <div class="col-lg-10 mx-auto" id="purchases-list">
                    <?php foreach ($ventas as $venta): ?>
                        <?php 
                            $status_class = 'status-' . strtolower($venta['estado'] ?? 'solicitado');
                            $status_label = $venta['estado'] ?? 'PENDIENTE';

                            // Si no ha sido aprobado por el admin, forzamos el label de solicitud
                            if (($venta['estado_aprobacion'] ?? '') == 'SOLICITUD') {
                                $status_class = 'status-solicitado';
                                $status_label = 'POR APROBAR';
                            } elseif (($venta['estado_aprobacion'] ?? '') == 'RECHAZADO') {
                                $status_class = 'status-rechazado';
                                $status_label = 'RECHAZADO';
                            }
                        ?>
                        <div class="purchase-card" data-status="<?= str_replace('status-', '', $status_class) ?>" data-fecha="<?= strtotime($venta['fecha']) ?>" data-id="<?= $venta['id'] ?>" data-total="<?= $venta['total_venta'] ?>">
                            <div class="purchase-header">
                                <div>
                                    <span class="info-label">IDENTIFICADOR DE OBRA</span>
                                    <span class="fw-bold text-cva-brown">#PED-<?= str_pad($venta['id'], 5, '0', STR_PAD_LEFT) ?></span>
                                </div>
                                <span class="status-badge <?= $status_class ?>">
                                    <?= $status_label ?>
                                </span>
                            </div>

                            <div class="purchase-body">
                                <div class="purchase-main-info">
                                    <span class="info-label">FECHA DE SOLICITUD</span>
                                    <h4><?= fecha_local($venta['fecha'], 'd M, Y') ?></h4>
                                    <p class="text-muted mb-3 small">Registrado a las <?= fecha_local($venta['fecha'], 'H:i') ?>hs</p>

                                    <!-- Resumen de Productos -->
                                    <div class="mt-2">
                                        <span class="info-label">MUEBLES EN ESTE PEDIDO</span>
                                        <div class="d-flex flex-wrap gap-2 mt-1">
                                            <?php if (!empty($venta['items'])): ?>
                                                <?php foreach($venta['items'] as $item): ?>
                                                    <span class="badge bg-white border text-dark px-3 py-2 rounded-pill x-small fw-bold shadow-sm">
                                                        <i class="bi bi-box me-1 text-gold"></i>
                                                        <?= $item['cantidad'] ?>x <?= esc($item['nombre_prod'] ?? 'Mueble a Medida') ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-muted x-small italic">Cargando detalles...</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <span class="info-label">INVERSIÓN TOTAL</span>
                                    <span class="display-6 fw-bold text-cva-brown">$<?= money($venta['total_venta'], 0) ?></span>
                                </div>
                            </div>

                            <div class="purchase-footer">
                                <div class="d-flex gap-2">
                                    <a href="<?= base_url('factura/' . $venta['id']) ?>" class="btn-artisan btn-artisan-primary py-2 px-4 btn-artisan-sm-text">
                                        VER DETALLE Y PAGOS <i class="bi bi-chevron-right ms-1"></i>
                                    </a>
                                    
                                    <?php
                                        $wa_url = wa_link($env_whatsapp, "Hola! Soy " . session()->get('nombre') . ", quería consultar sobre el estado de mi pedido #" . $venta['id']);
                                    ?>
                                    <a href="<?= $wa_url ?>" target="_blank" class="btn-wa-support">
                                        <i class="bi bi-whatsapp"></i> CONSULTAR POR WHATSAPP
                                    </a>
                                </div>
                                
                                <div class="text-muted small">
                                    <i class="bi bi-shield-check me-1"></i> Compra Protegida
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-history">
                <div class="mb-4">
                    <i class="bi bi-bag-x display-1 text-gold opacity-25"></i>
                </div>
                <h3 class="fw-bold text-cva-brown">Aún no tienes registros</h3>
                <p class="text-muted mb-4 fs-5">Tu historial aparecerá aquí en cuanto realices tu primer encargo.</p>
                <a href="<?= base_url('productos') ?>" class="btn-artisan btn-artisan-primary px-5 py-3">
                    EXPLORAR CATÁLOGO
                </a>
            </div>
        <?php endif; ?>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('extra-js') ?>
<script src="<?= base_url('assets/js/admin/admin-vista-compras.js?v=1.0') ?>"></script>
<?= $this->endSection() ?>
