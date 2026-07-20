<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('extra-css') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/admin-sales.css?v=33.0')?>">
<?= $this->endSection() ?>

<?= $this->section('breadcrumbs') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/ventas-list') ?>" class="text-decoration-none text-muted">CONTROL DE PEDIDOS</a></li>
    <li class="breadcrumb-item active small fw-bold text-gold" aria-current="page">GESTIÓN DE PRODUCCIÓN</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Encabezado Estilo Artisan -->
<div class="row mb-5 align-items-center g-4">
    <div class="col-lg-7">
        <div class="d-flex align-items-center gap-3 gap-md-4">
            <div class="dashboard-icon-main bg-brown text-gold shadow">
                <i class="bi bi-tools"></i>
            </div>
            <div>
                <h1 class="display-6 display-md-5 fw-bold text-cva-brown mb-1">Producción</h1>
                <p class="text-muted mb-0 small">
                    <i class="bi bi-person-badge text-gold me-1"></i> <?= esc($venta['nombre'] . ' ' . $venta['apellido']) ?>
                    <a href="<?= wa_link($venta['usuario'] ?? '', "Hola " . $venta['nombre'] . ", te contacto por tu pedido #" . $venta['id'] . " en CVA Muebles", null) ?>"
                       target="_blank" class="ms-2 badge bg-success text-white text-decoration-none">
                        <i class="bi bi-whatsapp me-1"></i> Contactar
                    </a>
                </p>
            </div>
        </div>
    </div>
    <div class="col-lg-5 text-lg-end">
        <div class="badge bg-gold-soft text-gold px-4 py-2 rounded-pill fs-6 fw-bold border border-gold shadow-sm w-sm-100 justify-content-center">
            #<?= $venta['id'] ?> | <?= fecha_local($venta['fecha'], 'd M, Y') ?>
        </div>
    </div>
</div>

<!-- Mensajes modularizados -->
<?= view('components/alert_message') ?>

<div class="row g-4 mb-5">
    <!-- COLUMNA PRINCIPAL: DETALLES Y PRODUCCIÓN -->
    <div class="col-lg-8">
        
        <!-- PROGRESO VISUAL O APROBACIÓN -->
        <div class="admin-card-v2 p-4 p-md-5 border-0 shadow-sm mb-4 overflow-hidden">
            <?php if ($venta['estado_aprobacion'] == 'SOLICITUD'): ?>
                <div class="text-center py-4">
                    <div class="bg-gold-soft p-4 rounded-4 mb-4 border border-gold border-opacity-25">
                        <i class="bi bi-shield-lock display-4 text-gold mb-3 d-block"></i>
                        <h4 class="fw-bold text-brown">Pedido Pendiente de Aprobación</h4>
                        <p class="text-muted">Revisa los detalles antes de aceptar este pedido para producción.</p>
                        
                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <form action="<?= base_url('ventas/actualizar_estado/' . $venta['id']) ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="estado" value="ACEPTADO">
                                <button type="submit" class="btn btn-success px-5 py-3 rounded-pill fw-bold shadow-sm">
                                    <i class="bi bi-check-lg me-2"></i> ACEPTAR Y EMPEZAR OBRA
                                </button>
                            </form>
                            <form action="<?= base_url('ventas/actualizar_estado/' . $venta['id']) ?>" method="post" data-confirm="¿Seguro que deseas rechazar este pedido?">
                                <?= csrf_field() ?>
                                <input type="hidden" name="estado" value="RECHAZADO">
                                <button type="submit" class="btn btn-outline-danger px-5 py-3 rounded-pill fw-bold">
                                    <i class="bi bi-x-lg me-2"></i> RECHAZAR PEDIDO
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <h5 class="fw-bold text-brown mb-4"><i class="bi bi-hammer me-2 text-gold"></i> Estado de la Obra</h5>
                
                <div class="production-stepper position-relative py-3">
                    <?php 
                        $steps = ['ACEPTADO', 'EN_PROCESO', 'TERMINADO', 'ENTREGADO'];
                        // Si por algún motivo está en PENDIENTE (manual), lo tratamos como ACEPTADO
                        $estado_visual = ($venta['estado'] == 'PENDIENTE') ? 'ACEPTADO' : $venta['estado'];
                        $current_idx = array_search($estado_visual, $steps);
                        $progress = ($current_idx / (count($steps)-1)) * 100;
                    ?>
                    <div class="progress mb-4 progress-h10-light production-stepper">
                        <div class="progress-bar progress-bar-striped progress-bar-animated js-progress-width" role="progressbar"
                             data-progress="<?= $progress ?>"></div>
                    </div>
                    <div class="d-flex justify-content-between text-center position-relative">
                        <?php foreach($steps as $idx => $step): ?>
                            <div class="step-item step-item-flex <?= $idx <= $current_idx ? 'active' : '' ?>">
                                <div class="step-dot mb-2 mx-auto shadow-sm"></div>
                                <span class="x-small fw-bold d-block text-uppercase step-item-label <?= $idx <= $current_idx ? 'text-brown' : 'text-muted' ?>"><?= $step ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Formulario de cambio de estado -->
                <form action="<?= base_url('ventas/actualizar_estado/' . $venta['id']) ?>" method="post" class="mt-5 pt-4 border-top">
                    <?= csrf_field() ?>
                    <div class="row align-items-center g-3">
                        <div class="col-lg-6">
                            <p class="small text-muted mb-0">Cambia la etapa de producción:</p>
                        </div>
                        <div class="col-lg-4 col-8">
                            <select name="estado" class="form-select admin-control py-2 fw-bold text-uppercase x-small">
                                <?php foreach($steps as $step): ?>
                                    <option value="<?= $step ?>" <?= $venta['estado'] == $step ? 'selected' : '' ?>><?= $step ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-2 col-4">
                            <button class="btn btn-admin-gold w-100 py-2" type="submit">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <!-- DETALLES DE LA OBRA -->
        <div class="admin-card-v2 border-0 shadow-sm mb-4 notebook-card overflow-hidden">
            <div class="bg-brown p-3 px-4 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-gold"><i class="bi bi-pencil-square me-2"></i> ESPECIFICACIONES Y OBRA</h6>
                <span class="badge bg-gold-soft text-gold x-small px-3 border border-gold border-opacity-25">Ficha Técnica</span>
            </div>
            
            <?php 
                $obs_clean = $venta['observaciones'] ?? '';
                $img_ref = "";
                if (preg_match('/\[IMG_REF:(.*?)\]/', $obs_clean, $matches)) {
                    $img_ref = $matches[1];
                    $obs_clean = trim(str_replace($matches[0], '', $obs_clean));
                }
            ?>

            <?php if (!empty($obs_clean) || !empty($img_ref)): ?>
            <div class="p-4 border-bottom bg-gold-soft bg-opacity-10">
                <div class="d-flex gap-3">
                    <i class="bi bi-chat-left-quote fs-4 text-gold opacity-50"></i>
                    <div>
                        <span class="admin-label mb-1">Nota del Cliente / Pedido</span>
                        <p class="mb-0 text-brown fw-semibold italic nota-cliente-text"><?= nl2br(esc($obs_clean)) ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="p-4">
                <?php if (!empty($img_ref)): ?>
                    <div class="mb-4 text-center p-3 border rounded-4 bg-light">
                        <span class="admin-label mb-2 d-block">Imagen de Referencia / Boceto</span>
                        <div class="product-img-zoom-container rounded-3 overflow-hidden shadow-sm d-inline-block zoom-container-max-w-100">
                            <a href="<?= base_url('assets/uploads/referencias/' . $img_ref) ?>" target="_blank">
                                <img src="<?= base_url('assets/uploads/referencias/' . $img_ref) ?>"
                                     class="img-fluid transition-all zoom-img-max-h-400" alt="Referencia">
                            </a>
                        </div>
                        <div class="mt-2 x-small text-muted italic"><i class="bi bi-zoom-in me-1"></i> Pasa el mouse para ampliar o haz clic para ver original</div>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('ventas/guardar_observaciones') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="venta_id" value="<?= $venta['id'] ?>">
                    <div class="mb-3 position-relative">
                        <textarea name="observaciones" class="form-control artisan-notebook-textarea" rows="8" 
                                  placeholder="Madera: Roble, Medidas: 1.80m x 0.90m, Acabado: Barniz mate..."><?= esc($obs_clean) ?></textarea>
                        
                        <!-- Mantenemos el tag de imagen oculto si existe para no perderlo al guardar -->
                        <?php if ($img_ref): ?>
                            <input type="hidden" name="img_ref_tag" value="[IMG_REF:<?= $img_ref ?>]">
                        <?php endif; ?>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-admin-gold px-5 py-2 shadow-sm fw-bold">
                            <i class="bi bi-save me-2"></i> GUARDAR ESPECIFICACIONES
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- LISTADO DE ÍTEMS -->
        <div class="admin-card-v2 border-0 shadow-sm overflow-hidden mb-4">
            <div class="bg-light py-3 px-4 border-bottom">
                <h6 class="mb-0 fw-bold text-brown">Artículos en la Obra</h6>
            </div>
            <div class="table-responsive-stack">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-white d-none d-md-table-header-group">
                        <tr class="x-small text-uppercase text-muted fw-bold">
                            <th class="py-3 px-4 border-0 col-imagen-100">Imagen</th>
                            <th class="py-3 border-0">Producto</th>
                            <th class="py-3 text-center border-0">Cant.</th>
                            <th class="py-3 text-end border-0">Unit.</th>
                            <th class="py-3 text-end px-4 border-0">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalles as $det): ?>
                        <tr>
                            <td class="ps-4 py-3" data-label="IMAGEN">
                                <?php if (!empty($det['imagen'])): ?>
                                    <div class="product-img-zoom-container rounded-3 border overflow-hidden shadow-sm thumb-70">
                                        <img src="<?= imagen_url($det['imagen']) ?>"
                                             class="img-fluid h-100 w-100 object-fit-cover transition-all"
                                             alt="<?= esc($det['nombre_prod'] ?? 'Mueble a Medida / Personalizado') ?>">
                                    </div>
                                <?php else: ?>
                                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted thumb-70">
                                        <i class="bi bi-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="py-3" data-label="PRODUCTO">
                                <div class="fw-bold text-brown"><?= esc($det['nombre_prod'] ?? 'Mueble a Medida / Personalizado') ?></div>
                                <div class="x-small text-muted">ID: <?= $det['producto_id'] ?? 'CUSTOM' ?></div>
                            </td>
                            <td class="text-center fw-bold" data-label="CANTIDAD"><?= $det['cantidad'] ?></td>
                            <td class="text-end text-muted small" data-label="UNITARIO">$ <?= money($det['precio'], 2) ?></td>
                            <td class="text-end px-4 fw-bold text-brown" data-label="SUBTOTAL">$ <?= money($det['cantidad'] * $det['precio'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-light border-top border-2">
                        <tr>
                            <td colspan="4" class="text-end py-3 px-4 text-muted x-small fw-bold text-uppercase d-none d-md-table-cell">Total Venta</td>
                            <td class="text-end py-3 px-4 text-brown fw-bold font-lora fs-5" data-label="TOTAL FINAL">$ <?= money($venta['total_venta'], 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- COLUMNA LATERAL: PAGOS Y FINANZAS -->
    <div class="col-lg-4">
        
        <!-- ESTADO FINANCIERO -->
        <div class="admin-card-v2 p-4 border-0 shadow-lg mb-4 text-white finance-card position-relative overflow-hidden finance-card-gradient">
            <div class="position-relative z-1">
                <h6 class="text-gold x-small fw-bold text-uppercase mb-4 tracking-wider">Estado Financiero</h6>
                
                <div class="mb-4">
                    <small class="d-block opacity-50 x-small mb-1">TOTAL COBRADO</small>
                    <h3 class="fw-bold text-success mb-0">$ <?= money($total_pagado, 2) ?></h3>
                </div>

                <div class="p-3 rounded-4 bg-white bg-opacity-10 border border-white border-opacity-10 shadow-inner">
                    <small class="d-block opacity-50 x-small mb-1 fw-bold">SALDO PENDIENTE</small>
                    <h2 class="fw-bold font-lora mb-0 <?= $saldo_pendiente > 0 ? 'text-gold' : 'text-success' ?>">
                        $ <?= money($saldo_pendiente, 2) ?>
                    </h2>
                </div>
            </div>
            <i class="bi bi-wallet2 position-absolute end-0 bottom-0 text-white opacity-05 wallet-icon-bg"></i>
        </div>

        <!-- REGISTRAR PAGO -->
        <div class="admin-card-v2 border-0 shadow-sm mb-4">
            <div class="p-4">
                <h5 class="fw-bold text-brown mb-4"><i class="bi bi-cash-stack me-2 text-success"></i> Nueva Entrega</h5>
                <form action="<?= base_url('ventas/registrar_pago') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="venta_id" value="<?= $venta['id'] ?>">
                    <div class="mb-3">
                        <label class="x-small fw-bold text-muted text-uppercase mb-2">Monto Recibido</label>
                        <div class="input-group shadow-sm rounded-3 overflow-hidden border-2">
                            <span class="input-group-text bg-white border-end-0 text-gold fw-bold">$</span>
                            <input type="number" step="0.01" name="monto" class="form-control border-start-0 ps-0 fw-bold py-2" required placeholder="0.00">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="x-small fw-bold text-muted text-uppercase mb-2">Concepto / Nota</label>
                        <input type="text" name="nota" class="form-control admin-control py-2" placeholder="Ej: Seña, Entrega materiales...">
                    </div>
                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold rounded-pill shadow-sm">
                        <i class="bi bi-plus-circle me-2"></i> REGISTRAR COBRO
                    </button>
                </form>
            </div>
        </div>

        <!-- HISTORIAL DE PAGOS -->
        <div class="admin-card-v2 border-0 shadow-sm overflow-hidden mb-5">
            <div class="bg-light py-3 px-4 border-bottom">
                <h6 class="mb-0 fw-bold text-brown small text-uppercase">Cronología de Pagos</h6>
            </div>
            <div class="p-0">
                <?php if (empty($pagos)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-clock-history text-muted opacity-25 fs-1"></i>
                        <p class="text-muted small mt-2">Sin movimientos.</p>
                    </div>
                <?php else: ?>
                    <div class="payment-list">
                        <?php foreach ($pagos as $pago): ?>
                            <div class="payment-item d-flex justify-content-between align-items-center p-3 border-bottom bg-white hover-bg-light transition-all">
                                <div>
                                    <div class="fw-bold text-brown">$ <?= money($pago['monto'], 2) ?></div>
                                    <small class="text-muted d-block text-truncate x-small payment-note-truncate"><?= esc($pago['nota']) ?></small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-light text-muted fw-normal x-small border"><?= fecha_local($pago['fecha'], 'd/m/y') ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>



<?= $this->endSection() ?>

<?= $this->section('extra-js') ?>
<script src="<?= base_url('assets/js/admin/admin-progress-bars.js?v=1.0') ?>"></script>
<?= $this->endSection() ?>
