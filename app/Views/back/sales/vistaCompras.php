<?= $this->extend('layout/main') ?>

<?= $this->section('extra-css') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/frontend-pages.css?v=1.0')?>">
<?= $this->endSection() ?>


<?= $this->section('content') ?>
<div class="purchases-wrapper">
    <div class="container">
        <!-- Header con Identidad -->
        <div class="mb-5 text-center text-lg-start">
            <span class="cart-header-badge">HISTORIAL ARTESANAL</span>
            <h1 class="cart-title-main">Mis Compras</h1>
            <p class="text-muted fs-5">Seguimiento de tus piezas y proyectos en curso.</p>
            <div style="width: 100px; height: 4px; background: var(--cva-gold); border-radius: 2px;"></div>
        </div>

        <!-- Alerta de Éxito Notable -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-artisan-success mb-5 p-4 border-0 shadow-lg animate__animated animate__backInDown" style="background: white; border-left: 10px solid #28a745 !important; border-radius: 1.5rem;">
                <div class="d-flex align-items-center">
                    <div class="bg-success text-white rounded-circle p-3 me-4 shadow">
                        <i class="bi bi-check-lg fs-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-success mb-1"><?= session()->getFlashdata('success') ?></h4>
                        <p class="text-muted mb-0 fw-semibold">Nuestro equipo revisará tu solicitud a la brevedad para confirmar los detalles.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($ventas)): ?>
            <div class="row">
                <div class="col-lg-10 mx-auto">
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
                        <div class="purchase-card">
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
                                    <h4><?= date('d M, Y', strtotime($venta['fecha'])) ?></h4>
                                    <p class="text-muted mb-3 small">Registrado a las <?= date('H:i', strtotime($venta['fecha'])) ?>hs</p>

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
                                    <span class="display-6 fw-bold text-cva-brown">$<?= number_format($venta['total_venta'], 0, ',', '.') ?></span>
                                </div>
                            </div>

                            <div class="purchase-footer">
                                <div class="d-flex gap-2">
                                    <a href="<?= base_url('factura/' . $venta['id']) ?>" class="btn-artisan btn-artisan-primary py-2 px-4" style="font-size: 0.7rem;">
                                        VER DETALLE Y PAGOS <i class="bi bi-chevron-right ms-1"></i>
                                    </a>
                                    
                                    <?php 
                                        $wa_num = $env_whatsapp;
                                        $msg = urlencode("Hola! Soy " . session()->get('nombre') . ", quería consultar sobre el estado de mi pedido #" . $venta['id']);
                                        $wa_url = "https://wa.me/{$wa_num}?text={$msg}";
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
    <script>
        function togglePreview(id, element) {
            // Cerrar otros abiertos
            document.querySelectorAll('.product-preview-card').forEach(card => {
                if(card.id !== id) card.classList.remove('active');
            });
            
            const card = document.getElementById(id);
            card.classList.toggle('active');
            
            // Cerrar al hacer clic fuera
            document.addEventListener('click', function close(e) {
                if (!card.contains(e.target) && !element.contains(e.target)) {
                    card.classList.remove('active');
                    document.removeEventListener('click', close);
                }
            });
        }
    </script>
</div>
<?= $this->endSection() ?>
