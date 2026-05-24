<?php
/**
 * Vista de Comprobante / Factura del Administrador (Admin Invoice View)
 *
 * Muestra el detalle completo de un pedido activo en el sistema, formateado con
 * la estética oscura/dorada Artisan del panel de administración. Permite al
 * administrador ver el estado de pagos, la lista de productos y gestionar o imprimir
 * el comprobante en formato físico/PDF.
 *
 * @var array $venta Datos cabecera del pedido (id, fecha, estado, total, observaciones).
 * @var array $detalles Lista de productos adquiridos con información de cantidades y precios.
 * @var array $pagos Historial de transacciones o pagos realizados para este pedido.
 * @var float $total_pagado Sumatoria total de los pagos validados.
 * @var float $saldo_pendiente Diferencia entre el costo total y el total pagado.
 */
?>
<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('extra-css') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/admin-sales.css?v=31.0')?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/ver-factura.css?v=1.0')?>">
    <style>
        /* Estilos de impresión */
        @media print {
            .btn, .sidebar, .navbar, .admin-sidebar, footer { display: none !important; }
            body { background: #fff !important; }
            .admin-card-v2 { border: none !important; box-shadow: none !important; }
        }
    </style>
<?= $this->endSection() ?>

<?= $this->section('breadcrumbs') ?>
<li class="breadcrumb-item"><a href="<?= base_url('ventas-list') ?>" class="text-decoration-none text-muted text-hover-gold">Control de Pedidos</a></li>
<li class="breadcrumb-item active small fw-bold text-gold" aria-current="page">PEDIDO #<?= $venta['id'] ?></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Encabezado Estilo Artisan -->
<div class="row mb-5 align-items-center g-4">
    <div class="col-lg-7">
        <div class="d-flex align-items-center gap-3 gap-md-4">
            <div class="dashboard-icon-main bg-brown text-gold shadow">
                <i class="bi bi-receipt"></i>
            </div>
            <div>
                <h1 class="display-6 display-md-5 fw-bold text-cva-brown mb-1">Comprobante #<?= $venta['id'] ?></h1>
                <p class="text-muted mb-0 small"><i class="bi bi-calendar-check text-gold me-1"></i> Fecha: <?= date('d/m/Y', strtotime($venta['fecha'])) ?></p>
            </div>
        </div>
    </div>
    <div class="col-lg-5 text-lg-end d-flex gap-2 justify-content-lg-end">
        <a href="<?= base_url('ventas/gestion/' . $venta['id']) ?>" class="btn btn-admin-primary rounded-pill px-4 py-2 shadow-gold justify-content-center" style="transition: all 0.3s ease;">
            <i class="bi bi-sliders2 me-2"></i> GESTIONAR PEDIDO
        </a>
        <button onclick="descargarPDF()" id="btn-download-pdf" class="btn text-cva-brown bg-light border-gold border-opacity-50 border-2 rounded-pill px-4 py-2 justify-content-center shadow-sm" style="transition: all 0.3s ease;" onmouseover="this.classList.replace('bg-light', 'bg-gold-light'); this.classList.replace('text-cva-brown', 'text-gold-dark'); this.style.transform='translateY(-2px)'" onmouseout="this.classList.replace('bg-gold-light', 'bg-light'); this.classList.replace('text-gold-dark', 'text-cva-brown'); this.style.transform='translateY(0)'">
            <i class="bi bi-file-earmark-pdf-fill me-2 text-danger"></i> GENERAR PDF
        </button>
    </div>
</div>

<div class="row g-4" id="factura-content">
    <!-- Detalle del Pedido -->
    <div class="col-lg-8">
        <div class="admin-card-v2 p-4 p-md-5 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-cva-brown mb-0">Desglose de la Obra</h5>
                <?php 
                    $status_class = 'status-' . strtolower($venta['estado']);
                    $status_label = $venta['estado'];

                    if (($venta['estado_aprobacion'] ?? '') == 'SOLICITUD') {
                        $status_class = 'status-solicitado';
                        $status_label = 'POR APROBAR';
                    } elseif (($venta['estado_aprobacion'] ?? '') == 'RECHAZADO') {
                        $status_class = 'status-rechazado';
                        $status_label = 'RECHAZADO';
                    }
                ?>
                <span class="order-status-badge <?= $status_class ?> px-3 py-1 rounded-pill fw-bold">
                    <?= $status_label ?>
                </span>
            </div>

            <div class="table-responsive" style="overflow: visible !important;">
                <table class="table table-borderless table-hover align-middle">
                    <thead class="border-bottom">
                        <tr>
                            <th class="py-3 text-muted small text-uppercase">Obra / Producto</th>
                            <th class="py-3 text-center text-muted small text-uppercase">Cant.</th>
                            <th class="py-3 text-end text-muted small text-uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalles as $item): ?>
                            <tr class="border-bottom">
                                <td class="py-4">
                                    <div class="position-relative d-inline-block">
                                        <div class="badge-preview-trigger cursor-pointer" onclick="togglePreview('preview-<?= $item['id'] ?>', this)">
                                            <div class="fw-bold text-cva-brown"><?= esc($item['nombre_prod'] ?? 'Mueble a Medida / Personalizado') ?></div>
                                            <small class="text-gold">Ver detalle artesanal <i class="bi bi-chevron-right x-small"></i></small>
                                        </div>

                                        <div id="preview-<?= $item['id'] ?>" class="product-preview-card position-absolute z-3 shadow-lg border-gold border-opacity-25" style="width: 320px; left: calc(100% + 20px); top: -40px;">
                                            <div class="text-center mb-3">
                                                <?php if(!empty($item['imagen'])): ?>
                                                    <img src="<?= (strpos($item['imagen'], 'http') === 0) ? $item['imagen'] : base_url('assets/uploads/' . $item['imagen']) ?>" class="preview-img mb-3 rounded shadow-sm" style="width: 100%; height: auto; object-fit: cover; border-radius: 8px;" alt="<?= esc($item['nombre_prod'] ?? 'Mueble a Medida') ?>">
                                                <?php else: ?>
                                                    <div class="preview-img bg-light d-flex align-items-center justify-content-center mx-auto mb-3 rounded shadow-sm" style="width: 100%; height: 180px; border-radius: 8px;">
                                                        <i class="bi bi-hammer text-muted display-6"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <h5 class="fw-bold text-cva-brown mb-1"><?= esc($item['nombre_prod'] ?? 'Mueble a Medida / Personalizado') ?></h5>
                                                <span class="text-gold fw-bold">$<?= number_format($item['precio'], 0, ',', '.') ?></span>
                                            </div>
                                            <p class="text-muted small italic mb-0 text-center">
                                                <?= !empty($item['descripcion']) ? esc($item['descripcion']) : 'Pieza única fabricada con técnicas artesanales tradicionales.' ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 text-center fw-bold text-muted"><?= $item['cantidad'] ?></td>
                                <td class="py-4 text-end fw-bold text-cva-brown">$<?= number_format($item['cantidad'] * $item['precio'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (!empty($venta['observaciones'])): ?>
                <div class="mt-4 p-4 rounded-4 border-start border-4 border-gold bg-light">
                    <h6 class="fw-bold text-cva-brown mb-2"><i class="bi bi-journal-text me-2 text-gold"></i>Especificaciones / Detalles Constructivos:</h6>
                    <p class="mb-0 text-muted italic">"<?= esc($venta['observaciones']) ?>"</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Resumen de Pagos -->
    <div class="col-lg-4">
        <div class="admin-card-v2 p-4 h-100 sticky-top" style="top: 100px;">
            <h5 class="fw-bold text-cva-brown mb-4 border-bottom pb-2"><i class="bi bi-wallet2 text-gold me-2"></i>Estado de Cuenta</h5>
            
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Total de la Obra</span>
                <span class="fw-bold fs-5">$<?= number_format($venta['total_venta'], 0, ',', '.') ?></span>
            </div>
            
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Cobros Realizados</span>
                <span class="fw-bold text-success fs-5">-$<?= number_format($total_pagado, 0, ',', '.') ?></span>
            </div>

            <div class="d-flex justify-content-between p-3 rounded-3 mt-3 <?= $saldo_pendiente > 0 ? 'bg-danger bg-opacity-10' : 'bg-success bg-opacity-10' ?>">
                <span class="fw-bold <?= $saldo_pendiente > 0 ? 'text-danger' : 'text-success' ?>">Saldo Pendiente</span>
                <span class="fw-bold fs-4 <?= $saldo_pendiente > 0 ? 'text-danger' : 'text-success' ?>">$<?= number_format($saldo_pendiente, 0, ',', '.') ?></span>
            </div>

            <hr class="my-4">

            <h6 class="fw-bold text-cva-brown mb-3"><i class="bi bi-clock-history text-gold me-2"></i>Historial de Cobros</h6>
            <?php if (!empty($pagos)): ?>
                <ul class="list-unstyled small mb-0">
                    <?php foreach ($pagos as $pago): ?>
                        <li class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                            <span>
                                <i class="bi bi-check-circle-fill text-success me-1"></i>
                                <?= date('d/m/y', strtotime($pago['fecha'])) ?> - <?= esc($pago['nota'] ?: 'Pago') ?>
                            </span>
                            <span class="fw-bold text-success">$<?= number_format($pago['monto'], 0, ',', '.') ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="text-center p-3 text-muted bg-light rounded">
                    <i class="bi bi-info-circle mb-2 d-block fs-4 text-gold"></i>
                    No se han registrado pagos aún.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('extra-js') ?>
<!-- Librería para generar PDFs desde el Frontend -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    function descargarPDF() {
        const btn = document.getElementById('btn-download-pdf');
        const originalText = btn.innerHTML;
        
        // Estado de carga
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>GENERANDO...';
        btn.disabled = true;

        // Cargar el HTML puro desde la nueva ruta
        fetch('<?= base_url("ventas/comprobante_a4/" . $venta["id"]) ?>')
            .then(response => response.text())
            .then(html => {
                // Crear contenedor temporal oculto
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                tempDiv.style.position = 'absolute';
                tempDiv.style.top = '-9999px';
                document.body.appendChild(tempDiv);

                // Opciones de configuración del PDF
                const opt = {
                    margin:       0,
                    filename:     'Comprobante_Admin_Pedido_<?= $venta['id'] ?>.pdf',
                    image:        { type: 'jpeg', quality: 1 },
                    html2canvas:  { scale: 2, useCORS: true, windowWidth: 800 },
                    jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
                };

                // Generar y descargar
                html2pdf().set(opt).from(tempDiv).save().then(() => {
                    // Limpieza
                    document.body.removeChild(tempDiv);
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
            })
            .catch(err => {
                console.error('Error al generar PDF:', err);
                btn.innerHTML = originalText;
                btn.disabled = false;
                alert('Ocurrió un error al cargar la plantilla del comprobante. Por favor intenta de nuevo.');
            });
    }
</script>
<script src="<?= base_url('assets/js/admin/sales.js?v=1.0') ?>"></script>
<?= $this->endSection() ?>
