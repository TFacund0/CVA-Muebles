<?= $this->extend('layout/main') ?>

<?= $this->section('extra-css') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/comercializacion.css?v=8.0')?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div id="container-comercializacion-wrapper">
    <!-- Sección de encabezado Premium (FONDO FIJO) -->
    <header class="comercializacion-header">
        <div class="container animate-fade-in">
            <span class="text-gold fw-bold text-uppercase x-small" style="letter-spacing: 3px;">Transparencia y Confianza</span>
            <h1 class="display-3 fw-bold font-lora">Comercialización</h1>
            <div class="divider-artisan mx-auto mb-4"></div>
            <p class="lead">Descubrí el camino que recorre cada una de nuestras piezas desde el taller hasta tu hogar.</p>
        </div>
    </header>

    <!-- BLOQUE 1: INTRODUCCIÓN (BLANCO) -->
    <section class="section-intro-comercializacion py-5 bg-white text-center">
        <div class="container py-4">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h2 class="font-lora fw-bold text-cva-brown mb-4">El Compromiso CVA</h2>
                    <p class="lead text-muted">
                        En <strong>CVA Muebles</strong> nos esforzamos por hacer que la adquisición de nuestros productos
                        sea tan placentera como su uso. Cada pieza es tratada con el respeto que merece la madera noble.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- BLOQUE 2: MÉTODOS DE PAGO (ARENA) -->
    <section class="section-pagos-full py-5" style="background-color: var(--cva-sand);">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-5 mb-5 mb-lg-0">
                    <span class="text-vivid fw-bold text-uppercase small" style="letter-spacing: 2px;">Inversión Segura</span>
                    <h2 class="display-5 fw-bold font-lora text-cva-brown mt-2">Formas de Pago</h2>
                    <p class="text-muted mt-3">Facilitamos tu inversión en calidad artesanal con diversas modalidades adaptadas a tu comodidad.</p>
                </div>
                <div class="col-lg-7">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="payment-card-premium">
                                <div class="icon-wrap"><i class="bi bi-cash-stack"></i></div>
                                <h5>Efectivo</h5>
                                <p class="small text-muted mb-0">Bonificaciones especiales en taller o contra-entrega.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="payment-card-premium">
                                <div class="icon-wrap"><i class="bi bi-credit-card"></i></div>
                                <h5>Tarjetas de Crédito</h5>
                                <p class="small text-muted mb-0">Hasta 12 cuotas fijas con todos los bancos.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="payment-card-premium">
                                <div class="icon-wrap"><i class="bi bi-bank"></i></div>
                                <h5>Transferencia</h5>
                                <p class="small text-muted mb-0">Bancaria o vía Mercado Pago de forma inmediata.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="payment-card-premium">
                                <div class="icon-wrap"><i class="bi bi-wallet2"></i></div>
                                <h5>Financiación</h5>
                                <p class="small text-muted mb-0">Planes a medida para proyectos especiales.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- BLOQUE 3: LOGÍSTICA (VERDE BOSQUE) -->
    <section class="section-logistica-full py-5" style="background-color: var(--cva-forest); color: white;">
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-6 mb-5 mb-lg-0 pe-lg-5">
                    <span class="text-gold fw-bold text-uppercase small" style="letter-spacing: 2px;">Del Taller a tu Casa</span>
                    <h2 class="display-5 fw-bold font-lora text-white mt-2">Logística Artesanal</h2>
                    <p class="opacity-75 mt-3">Entendemos que un mueble de autor requiere un traslado a su altura. No somos solo un flete, somos custodios de tu pieza.</p>
                    
                    <div class="mt-5">
                        <div class="d-flex align-items-center gap-4 mb-4">
                            <div class="badge-logistica">01</div>
                            <div>
                                <h6 class="fw-bold mb-1">Fabricación Cuidadosa</h6>
                                <p class="small opacity-75 mb-0">Respetamos los 15-30 días de creación artesanal.</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-4 mb-4">
                            <div class="badge-logistica">02</div>
                            <div>
                                <h6 class="fw-bold mb-1">Embalaje de Alta Resistencia</h6>
                                <p class="small opacity-75 mb-0">Protección extrema para cada veta y arista.</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-4">
                            <div class="badge-logistica">03</div>
                            <div>
                                <h6 class="fw-bold mb-1">Entrega Especializada</h6>
                                <p class="small opacity-75 mb-0">Personal capacitado para el manejo de muebles pesados.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 bg-white rounded-5 p-5 shadow-lg text-cva-brown">
                    <h3 class="font-lora fw-bold mb-4">Área de Cobertura</h3>
                    <p class="text-muted mb-5">Llegamos a toda la provincia de **Corrientes** con logística propia. Consultanos por envíos nacionales.</p>
                    
                    <div class="p-4 rounded-4 mb-4" style="background: var(--cva-sand);">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-geo-alt-fill text-vivid fs-3"></i>
                            <div>
                                <h6 class="fw-bold mb-0">Retiro en Taller</h6>
                                <p class="small text-muted mb-0">Mantilla, Corrientes (Sin costo adicional)</p>
                            </div>
                        </div>
                    </div>

                    <a href="<?= base_url('informacionContacto')?>" class="btn btn-vivid w-100 py-3 rounded-pill fw-bold">CONSULTAR COSTO DE ENVÍO</a>
                </div>
            </div>
        </div>
    </section>

    <!-- BLOQUE 4: GARANTÍA (PREMIUM LIGHT ARTISAN) -->
    <section class="section-garantia-full py-5" style="background-color: #fdfbf7; border-top: 1px solid rgba(184, 134, 11, 0.1); color: var(--cva-brown); text-align: center;">
        <div class="container py-5 animate-fade-in">
            <span class="text-gold fw-bold text-uppercase x-small" style="letter-spacing: 3px;">Seguridad y Garantía</span>
            <h2 class="display-4 fw-bold font-lora text-cva-brown mt-2 mb-4">Compromiso para Generaciones</h2>
            <div class="mx-auto" style="width: 100px; height: 3px; background: var(--cva-gold); margin-bottom: 2.5rem;"></div>
            <p class="lead mb-5 mx-auto" style="max-width: 800px; color: #5c4a44; font-weight: 500;">Cada mueble CVA cuenta con <strong>1 año de garantía estructural</strong>. Nuestra meta es que tu única preocupación sea disfrutar de la calidez de la madera en tu hogar.</p>
            <a href="<?= base_url('terminosYCondiciones') ?>" class="btn btn-outline-brown px-5 py-3 rounded-pill fw-bold" style="letter-spacing: 2px;">VER TÉRMINOS Y CONDICIONES</a>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
