<?= $this->extend('layout/main') ?>

<?= $this->section('extra-css') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/contacto.css?v=9.0')?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div id="container-contacto-wrapper">
    <!-- Cabecera Premium (FONDO FIJO) -->
    <header class="contact-header">
        <div class="container animate-fade-in">
            <span class="text-gold fw-bold text-uppercase x-small kicker-spacing-3">Atención Personalizada</span>
            <h1 class="display-3 fw-bold font-lora">Contacto</h1>
            <div class="divider-artisan mx-auto mb-4"></div>
            <p class="lead">Estamos aquí para ayudarte a dar vida a tus proyectos en madera noble.</p>
        </div>
    </header>

    <!-- BLOQUE 1: DATOS DE CONTACTO (ARENA) -->
    <section class="section-contact-data py-5">
        <div class="container py-5">
            <!-- Título de Sección Agregado -->
            <div class="row mb-5 text-center">
                <div class="col-lg-8 mx-auto">
                    <span class="text-vivid fw-bold text-uppercase small kicker-spacing-2">Vías Directas</span>
                    <h2 class="display-4 fw-bold font-lora text-cva-brown mt-2">Nuestros Canales</h2>
                    <div class="divider-artisan mx-auto"></div>
                </div>
            </div>

            <div class="row g-4 justify-content-center">
                <!-- Dirección -->
                <div class="col-md-4">
                    <div class="artisan-contact-card">
                        <div class="icon-contact-wrapper"><i class="bi bi-geo-alt"></i></div>
                        <h4 class="font-lora fw-bold">Dirección</h4>
                        <p class="text-muted mb-4">9 de Julio 1449<br>Mantilla, Corrientes</p>
                        <a href="https://maps.google.com/?q=9+de+Julio+1449,Mantilla,Corrientes" target="_blank" class="btn btn-premium-action">
                            <span>COMO LLEGAR</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <!-- Teléfonos -->
                <div class="col-md-4">
                    <div class="artisan-contact-card">
                        <div class="icon-contact-wrapper"><i class="bi bi-whatsapp"></i></div>
                        <h4 class="font-lora fw-bold">WhatsApp</h4>
                        <p class="text-muted mb-4"><?= $env_whatsapp ?><br>Atención Inmediata</p>
                        <a href="<?= wa_link($env_whatsapp) ?>" target="_blank" class="btn btn-premium-action w-whatsapp">
                            <span>HABLAR AHORA</span>
                            <i class="bi bi-chat-dots"></i>
                        </a>
                    </div>
                </div>
                <!-- Email -->
                <div class="col-md-4">
                    <div class="artisan-contact-card">
                        <div class="icon-contact-wrapper"><i class="bi bi-envelope-at"></i></div>
                        <h4 class="font-lora fw-bold">Email</h4>
                        <p class="text-muted mb-4">info@cvamuebles.com<br>ventas@cvamuebles.com</p>
                        <a href="mailto:info@cvamuebles.com" class="btn btn-premium-action w-email">
                            <span>ENVIAR CORREO</span>
                            <i class="bi bi-send"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- BLOQUE 2: FORMULARIO Y LEGAL (BLANCO) -->
    <section class="section-form-contacto py-5 bg-white">
        <div class="container py-5">
            <!-- Título de Sección Solicitado -->
            <div class="row mb-5 text-center">
                <div class="col-lg-8 mx-auto">
                    <span class="text-vivid fw-bold text-uppercase small kicker-spacing-2">Consultas</span>
                    <h2 class="display-4 fw-bold font-lora text-cva-brown mt-2">Canales de Atención</h2>
                    <div class="divider-artisan mx-auto"></div>
                </div>
            </div>

            <div class="row g-5 align-items-stretch">
                <!-- Info Institucional (REDiseño) -->
                <div class="col-lg-5">
                    <div class="institutional-box-premium p-4 p-xl-5 rounded-5 h-100 d-flex flex-column">
                        <span class="text-gold fw-bold text-uppercase x-small kicker-spacing-3">CVA Muebles</span>
                        <h2 class="font-lora h1 fw-bold text-white mt-2 mb-4">Nuestra Identidad</h2>
                        
                        <div class="legal-item-premium mb-3">
                            <label>Razón Social</label>
                            <p>CVA Muebles S.R.L.</p>
                        </div>
                        <div class="legal-item-premium mb-3">
                            <label>Titular Responsable</label>
                            <p>Acevedo Cesar Victor</p>
                        </div>
                        <div class="legal-item-premium mb-4">
                            <label>Identificación Tributaria</label>
                            <p>CUIT 30-12345678-9</p>
                        </div>

                        <div class="horarios-premium-card p-4 rounded-4 bg-white shadow-sm mb-4">
                            <h6 class="fw-bold text-cva-brown mb-3 text-uppercase small">Horarios de Taller</h6>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">Lunes a Viernes</span>
                                <span class="fw-bold text-cva-brown">08:00 - 19:00</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center opacity-50">
                                <span class="text-muted small">Sábados y Domingos</span>
                                <span class="fw-bold">Cerrado</span>
                            </div>
                        </div>

                        <div class="artisan-seal-quote mt-auto">
                            <i class="bi bi-quote h1 text-gold opacity-25"></i>
                            <p class="fst-italic text-white opacity-75 small">"La madera es un material vivo; responder a cada consulta es para nosotros el primer paso para darle una nueva forma."</p>
                        </div>
                    </div>
                </div>

                <!-- Formulario (RECUADRO REDONDEADO) -->
                <div class="col-lg-7">
                    <div class="artisan-form-container-box h-100 p-4 p-xl-5 d-flex flex-column">
                        <div class="text-center mb-4">
                            <h3 class="font-lora h2 fw-bold text-cva-brown mb-2">Escribinos tu Consulta</h3>
                            <p class="text-muted small">Contanos qué tenés en mente y te responderemos a la brevedad con una propuesta personalizada.</p>
                        </div>

                        <!-- Alertas de estado -->
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4">
                                <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
                            </div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('/enviar-consulta') ?>" method="post" class="row g-4 mt-auto">
                            <?= csrf_field() ?>
                            
                            <!-- Campo Honeypot (Trampa para bots) -->
                            <div class="honeypot-field">
                                <label>Si eres humano, deja esto vacío</label>
                                <input type="text" name="honeypot" value="">
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating-custom">
                                    <input type="text" class="form-control" name="nombre" placeholder="Nombre" value="<?= old('nombre') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating-custom">
                                    <input type="text" class="form-control" name="apellido" placeholder="Apellido" value="<?= old('apellido') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating-custom">
                                    <input type="email" class="form-control" name="email" placeholder="Email" value="<?= old('email') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating-custom">
                                    <input type="tel" class="form-control" name="telefono" placeholder="Teléfono" value="<?= old('telefono') ?>" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <select class="form-select form-control" name="asunto" required>
                                    <option value="" disabled selected>Seleccioná un motivo</option>
                                    <option value="Presupuesto" <?= old('asunto') == 'Presupuesto' ? 'selected' : '' ?>>Presupuesto para Mueble</option>
                                    <option value="Pedido" <?= old('asunto') == 'Pedido' ? 'selected' : '' ?>>Consulta sobre mi Pedido</option>
                                    <option value="Garantia" <?= old('asunto') == 'Garantia' ? 'selected' : '' ?>>Garantía y Soporte</option>
                                    <option value="Otro" <?= old('asunto') == 'Otro' ? 'selected' : '' ?>>Otros motivos</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <textarea class="form-control" name="descripcion" rows="6" placeholder="¿Cómo podemos ayudarte hoy?" required><?= old('descripcion') ?></textarea>
                            </div>

                            <div class="col-12 text-center">
                                <p class="x-small text-muted mb-0">
                                    <i class="bi bi-shield-check text-gold"></i> Formulario protegido contra SPAM. Límite de 3 consultas diarias.
                                </p>
                            </div>

                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-vivid-premium w-100 py-3 rounded-pill">
                                    <span>ENVIAR CONSULTA</span>
                                    <i class="bi bi-send-fill ms-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- BLOQUE 3: PREGUNTAS FRECUENTES (ARENA SUAVE) -->
    <section class="section-faq py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="text-vivid fw-bold text-uppercase small kicker-spacing-2">Resolviendo Dudas</span>
                <h2 class="display-4 fw-bold font-lora text-cva-brown mt-2">Preguntas Frecuentes</h2>
                <div class="divider-artisan mx-auto"></div>
            </div>

            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="accordion artisan-accordion" id="faqAccordion">
                        
                        <!-- Pregunta 1 -->
                        <div class="accordion-item mb-3 border-0 rounded-4 shadow-sm overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button fw-bold text-cva-brown py-4 shadow-none accordion-button-white" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    ¿Cuánto tiempo tarda la fabricación de un mueble?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted pb-4 bg-white">
                                    Como realizamos trabajos artesanales, el tiempo de entrega varía entre **15 y 30 días hábiles** dependiendo de la complejidad de la pieza y la demanda del taller. Cada etapa (corte, ensamble, lijado y acabado) requiere su tiempo para garantizar la calidad que nos caracteriza.
                                </div>
                            </div>
                        </div>

                        <!-- Pregunta 2 -->
                        <div class="accordion-item mb-3 border-0 rounded-4 shadow-sm overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold text-cva-brown py-4 shadow-none accordion-button-white" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    ¿Realizan envíos fuera de Mantilla?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted pb-4 bg-white">
                                    Sí, realizamos envíos a toda la provincia de **Corrientes** y provincias vecinas. El costo se coordina según la zona y el volumen del mueble. Trabajamos con transportes de confianza para asegurar que tu obra llegue en perfectas condiciones.
                                </div>
                            </div>
                        </div>

                        <!-- Pregunta 3 -->
                        <div class="accordion-item mb-3 border-0 rounded-4 shadow-sm overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold text-cva-brown py-4 shadow-none accordion-button-white" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    ¿Puedo pedir un mueble con medidas personalizadas?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted pb-4 bg-white">
                                    ¡Por supuesto! En CVA Muebles nos especializamos en **muebles a medida**. Si viste un modelo en nuestro catálogo pero necesitas otras dimensiones o un color de madera específico, contáctanos por WhatsApp para que podamos armar un presupuesto personalizado.
                                </div>
                            </div>
                        </div>

                        <!-- Pregunta 4 -->
                        <div class="accordion-item mb-3 border-0 rounded-4 shadow-sm overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold text-cva-brown py-4 shadow-none accordion-button-white" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    ¿Qué tipos de madera utilizan?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted pb-4 bg-white">
                                    Trabajamos principalmente con maderas nobles de la región, como **Algarrobo, Pino seleccionado y Eucalipto**. Todas nuestras maderas pasan por un proceso de secado natural para evitar futuras deformaciones, asegurando muebles para toda la vida.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>
