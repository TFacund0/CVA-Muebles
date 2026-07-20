<?= $this->extend('layout/main') ?>

<?= $this->section('extra-css') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/condiciones.css?v=3.0')?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div id="container-condiciones-wrapper">
    <!-- Cabecera Premium (Sello del Sitio) -->
    <header class="condiciones-header">
        <div class="container animate-fade-in">
            <span class="text-gold fw-bold text-uppercase x-small kicker-spacing-3">Transparencia CVA</span>
            <h1 class="display-3 fw-bold font-lora">Marco Legal</h1>
            <div class="divider-artisan mx-auto mb-4"></div>
            <p class="lead">Nuestros compromisos y tus derechos en un lenguaje claro y artesanal.</p>
        </div>
    </header>

    <!-- CUERPO DEL DOCUMENTO (NUEVO PATRÓN: Sidebar + Documento) -->
    <section class="section-legal-document py-5 bg-white">
        <div class="container py-5">
            <div class="row g-5">
                <!-- Navegación Sticky (Izquierda) -->
                <div class="col-lg-3 d-none d-lg-block">
                    <div class="sticky-top">
                        <h5 class="fw-bold text-cva-brown mb-4 text-uppercase small kicker-spacing-2">Contenido</h5>
                        <nav id="legal-nav" class="nav flex-column border-start border-2 border-light ps-3">
                            <a class="nav-link text-muted py-2 active" href="#privacidad">01. Privacidad</a>
                            <a class="nav-link text-muted py-2" href="#venta">02. Términos de Venta</a>
                            <a class="nav-link text-muted py-2" href="#garantia">03. Garantías</a>
                            <a class="nav-link text-muted py-2" href="#sitio">04. Uso del Sitio</a>
                        </nav>
                        <div class="mt-5 p-4 rounded-4 bg-sand-light border-gold-subtle shadow-sm">
                            <p class="small text-muted mb-0 italic">¿Tenés dudas legales? <br> <a href="mailto:info@cvamuebles.com" class="text-vivid fw-bold">Contactanos</a></p>
                        </div>
                    </div>
                </div>

                <!-- Documento (Derecha) -->
                <div class="col-lg-9">
                    <div class="artisan-paper-document p-5 rounded-4 shadow-sm">
                        <!-- 01. Privacidad -->
                        <div id="privacidad" class="legal-section mb-5 pb-5 border-bottom">
                            <span class="badge-legal-num">01</span>
                            <h2 class="font-lora fw-bold text-cva-brown mt-3">Política de Privacidad</h2>
                            <p class="lead text-muted mt-4">En CVA Muebles tratamos tu información con el mismo respeto que le damos a la madera nativa.</p>
                            <div class="mt-4 text-muted">
                                <p>Recopilamos datos mínimos necesarios para la entrega de tus productos y la mejora de nuestra atención. No compartimos tus datos con terceros sin tu consentimiento explícito.</p>
                                <div class="bg-light p-4 rounded-4 border-start border-4 border-gold mt-4">
                                    <p class="mb-0 small fw-bold">Dato Clave: Tu información está protegida bajo estándares de encriptación modernos.</p>
                                </div>
                            </div>
                        </div>

                        <!-- 02. Términos de Venta -->
                        <div id="venta" class="legal-section mb-5 pb-5 border-bottom">
                            <span class="badge-legal-num">02</span>
                            <h2 class="font-lora fw-bold text-cva-brown mt-3">Términos de Venta</h2>
                            <div class="row g-4 mt-4">
                                <div class="col-md-6">
                                    <div class="legal-mini-card">
                                        <h6>Fabricación</h6>
                                        <p class="small text-muted">Iniciamos la producción artesanal una vez acreditado el 50% del presupuesto.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="legal-mini-card">
                                        <h6>Envíos</h6>
                                        <p class="small text-muted">La logística es coordinada según disponibilidad del taller y zona de entrega.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 03. Garantías -->
                        <div id="garantia" class="legal-section mb-5 pb-5 border-bottom">
                            <span class="badge-legal-num">03</span>
                            <h2 class="font-lora fw-bold text-cva-brown mt-3">Garantías y Soporte</h2>
                            <p class="mt-4 text-muted">Ofrecemos un compromiso de <strong>12 meses de garantía estructural</strong> en todos nuestros muebles.</p>
                            <div class="alert alert-cva-info mt-4">
                                <i class="bi bi-info-circle-fill me-2"></i> No cubrimos daños por humedad extrema, exposición directa al sol prolongada o uso indebido de productos químicos.
                            </div>
                        </div>

                        <!-- 04. Uso del Sitio -->
                        <div id="sitio" class="legal-section mb-5">
                            <span class="badge-legal-num">04</span>
                            <h2 class="font-lora fw-bold text-cva-brown mt-3">Uso del Sitio Web</h2>
                            <p class="mt-4 text-muted">Todos los diseños y fotografías son propiedad intelectual de <strong>CVA Muebles</strong>. Queda prohibida su reproducción sin permiso.</p>
                            
                            <div class="accordion accordion-flush mt-4" id="legalAccordion">
                                <div class="accordion-item border-bottom-0">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed px-0 bg-white shadow-none fw-bold text-cva-brown" type="button" data-bs-toggle="collapse" data-bs-target="#accPropiedad">
                                            Propiedad Intelectual
                                        </button>
                                    </h2>
                                    <div id="accPropiedad" class="accordion-collapse collapse" data-bs-parent="#legalAccordion">
                                        <div class="accordion-body px-0 text-muted small">
                                            Los planos de diseño y fotografías de catálogo son activos propios.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-end mt-5 pt-5 border-top opacity-50">
                            <img src="<?= base_url('assets/img/branding/cva2.png') ?>" alt="Sello CVA" class="seal-img">
                            <p class="x-small mb-0 mt-2">Documento validado para 2026</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
