<?php
    $session = session();
    $isLogged = $session->get('logged_in');
?>


<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<!-- SECCIÓN ESPECIALIDADES -->
<section class="section-categorias">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-vivid fw-bold text-uppercase small" style="letter-spacing: 4px;">Explorá nuestro arte</span>
            <h2 class="display-5 fw-bold font-lora mt-2 text-cva-brown">Nuestras Especialidades</h2>
            <div class="mx-auto mt-3" style="width: 80px; height: 4px; background: var(--cva-gold); border-radius: 2px;"></div>
        </div>
        
        <div class="row g-4">
            <!-- Especialidad: Muebles de Sala -->
            <div class="col-lg-4 col-md-6">
                <div class="catalogo-card-premium">
                    <img src="<?= base_url('assets/img/content/hero/Muebles 22.jpeg') ?>" class="card-img-artisan" alt="Sala">
                    <div class="card-overlay-modern">
                        <div class="category-line"></div>
                        <h3 class="fw-bold h2 mb-2 font-lora">Muebles de Sala</h3>
                        <p class="opacity-80 mb-4 small">Confort y distinción para tu espacio principal.</p>
                        <a href="<?= base_url('productos') ?>" class="btn btn-vivid rounded-pill px-4 py-2 fw-bold w-100 shadow">VER COLECCIÓN</a>
                    </div>
                </div>
            </div>
            <!-- Especialidad: Dormitorios -->
            <div class="col-lg-4 col-md-6">
                <div class="catalogo-card-premium">
                    <img src="<?= base_url('assets/img/content/hero/Muebles 69.jpeg') ?>" class="card-img-artisan" alt="Dormitorios">
                    <div class="card-overlay-modern">
                        <div class="category-line"></div>
                        <h3 class="fw-bold h2 mb-2 font-lora">Dormitorios</h3>
                        <p class="opacity-80 mb-4 small">El refugio perfecto con maderas nobles.</p>
                        <a href="<?= base_url('productos') ?>" class="btn btn-vivid rounded-pill px-4 py-2 fw-bold w-100 shadow">VER COLECCIÓN</a>
                    </div>
                </div>
            </div>                
            <!-- Especialidad: Cocina -->
            <div class="col-lg-4 col-md-6">
                <div class="catalogo-card-premium">
                    <img src="<?= base_url('assets/img/content/products/Muebles 10.jpeg') ?>" class="card-img-artisan" alt="Cocina">
                    <div class="card-overlay-modern">
                        <div class="category-line"></div>
                        <h3 class="fw-bold h2 mb-2 font-lora">Cocina</h3>
                        <p class="opacity-80 mb-4 small">Funcionalidad rústica para el corazón del hogar.</p>
                        <a href="<?= base_url('productos') ?>" class="btn btn-vivid rounded-pill px-4 py-2 fw-bold w-100 shadow">VER COLECCIÓN</a>
                    </div>
                </div>
            </div>  
        </div>
    </div>
</section>


<!-- SECCIÓN MISIÓN (Verde Bosque Premium) -->
<section class="mission-statement-premium">
    <div class="mission-bg-accent">CVA</div>
    <div class="container text-center">
        <div class="mission-content">
            <span class="text-gold fw-bold text-uppercase small mb-3 d-block" style="letter-spacing: 5px;">Nuestra Esencia</span>
            <h2 class="display-3 fw-bold font-lora mb-0">Inspiración en la <span class="text-gold">Morfología</span></h2>
            <div class="mission-divider"></div>
            <div class="mission-quote">
                En CVA Muebles nos destacamos por la creatividad al diseñar. Inspirados en la simpleza morfológica encontramos la sutileza que caracteriza a nuestros diseños, dando lugar al nacimiento de lo bello.
            </div>
            <div class="mt-5 pt-4">
                <i class="bi bi-patch-check text-gold fs-4 me-2"></i>
                <span class="small text-uppercase fw-bold opacity-75" style="letter-spacing: 2px;">Calidad Artesanal Garantizada</span>
            </div>
        </div>
    </div>
</section>

<!-- SECCIÓN PRODUCTOS (REDISEÑADA PARA SER MÁS COMPACTA) -->
<section class="section-destacados-dinamica"> 
    <div class="container">
        <div class="row mb-2 align-items-center">
            <div class="col-lg-6">
                <span class="text-vivid fw-bold text-uppercase small" style="letter-spacing: 3px;">Colección de Autor</span>
                <h2 class="display-5 fw-bold font-lora mt-2 text-cva-brown">Piezas Destacadas</h2>
                <div class="mt-2" style="width: 80px; height: 3px; background: var(--cva-gold);"></div>
            </div>
            <div class="col-lg-6 text-lg-end mt-4 mt-lg-0">
                <p class="text-muted small mb-3">Selección exclusiva de nuestro taller, donde cada veta cuenta una historia.</p>
                <a href="<?= base_url('productos') ?>" class="btn btn-vivid px-4 py-2 rounded-pill fw-bold shadow">VER CATÁLOGO <i class="bi bi-arrow-right ms-2"></i></a>
            </div>
        </div>

        <div class="swiper swiper-destacados">
            <div class="swiper-wrapper">                
                <?php 
                    $destacados = [
                        ['id' => 10, 'img' => 'Muebles 56.jpeg', 'tit' => 'Mesa Comedor Roble', 'desc' => 'Roble macizo tallado a mano.', 'price' => '$1.2M'],
                        ['id' => 14, 'img' => 'Muebles 10.jpeg', 'tit' => 'Alacena Rústica', 'desc' => 'Pino seleccionado con forja.', 'price' => '$650k'],
                        ['id' => 21, 'img' => 'Muebles 46.jpeg', 'tit' => 'Set de 4 Sillas', 'desc' => 'Diseño ergonómico recuperado.', 'price' => '$1.2M'],
                        ['id' => 11, 'img' => 'Muebles 68.jpeg', 'tit' => 'Mesa Ratona', 'desc' => 'Acabado rústico premium.', 'price' => '$450k'],
                        ['id' => 22, 'img' => 'Muebles 22.jpeg', 'tit' => 'Mueble de TV', 'desc' => 'Líneas modernas y madera noble.', 'price' => '$850k'],
                        ['id' => 16, 'img' => 'Muebles 69.jpeg', 'tit' => 'Respaldo de Cama', 'desc' => 'Textura natural y robustez.', 'price' => '$550k'],
                    ];
                    foreach($destacados as $p):
                ?>
                <div class="swiper-slide p-3">
                    <div class="product-card-vivid h-100">
                        <div class="product-img-container">
                            <img src="<?= base_url('assets/img/content/products/'.$p['img']) ?>" alt="<?= $p['tit'] ?>">
                            <div class="price-badge-artisan"><?= $p['price'] ?></div>
                        </div>
                        <div class="px-2">
                            <h6 class="fw-bold text-cva-brown mb-1 font-lora"><?= $p['tit'] ?></h6>
                            <p class="x-small text-muted mb-3"><?= $p['desc'] ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="x-small fw-bold text-vivid">Pieza de Autor</span>
                                <a href="<?= base_url('producto/detalle/' . $p['id']) ?>" class="btn-detail-artisan" style="width: 35px; height: 35px;"><i class="bi bi-eye"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Controles -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination mt-4"></div>
        </div>
    </div>
</section>

<!-- UBICACIÓN (RESTAURADO COLOR BLANCO Y ALINEACIÓN) -->
<section class="section-ubicacion py-0">
    <div class="container-fluid p-0">
        <div class="row g-0 align-items-stretch" style="min-height: 700px;">
            <div class="col-lg-5 d-flex align-items-center bg-white"> 
                <div class="p-5 p-xl-5 w-100 mx-auto" style="max-width: 600px;">
                    <span class="text-gold fw-bold text-uppercase x-small" style="letter-spacing: 3px;">Vení al Taller</span>
                    <h2 class="display-3 fw-bold font-lora mt-2 mb-4 text-cva-brown">Mantilla, <span class="text-gold">Corrientes</span></h2>
                    <p class="lead mb-5 text-muted">Donde la pasión por el oficio se encuentra. Un rincón correntino dedicado a crear muebles para toda la vida.</p>
                    
                    <div class="d-flex align-items-center gap-4 mb-4">
                        <div class="bg-sand p-3 rounded-4 text-brown shadow-sm" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-geo-alt fs-4"></i></div>
                        <div>
                            <h6 class="fw-bold mb-1">Dirección</h6>
                            <p class="text-muted mb-0 small">Ruta Nacional 12, Km 885, Mantilla.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-4 mb-5">
                        <div class="bg-sand p-3 rounded-4 text-brown shadow-sm" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-clock fs-4"></i></div>
                        <div>
                            <h6 class="fw-bold mb-1">Horarios</h6>
                            <p class="text-muted mb-0 small">Lunes a Viernes: 08:00 - 18:00 hs.</p>
                        </div>
                    </div>

                    <a href="https://maps.google.com" target="_blank" class="btn btn-vivid px-5 py-4 rounded-pill fw-bold w-100 shadow-lg">
                        CÓMO LLEGAR <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="h-100 w-100">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3479.876234045048!2d-58.7687226!3d-29.2839154!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x944eba9a3d8e3c1f%3A0xf8a3e4f8a3e4f8a!2sMantilla%2C%20Corrientes!5e0!3m2!1ses-419!2sar!4v1713456789012!5m2!1ses-419!2sar" 
                        width="100%" height="100%" style="border:0; min-height: 700px;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>
    
</section>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- Inicialización de Swiper -->
<script>
    (function() {
        function initSwiper() {
            if (typeof Swiper !== 'undefined') {
                const swiper = new Swiper('.swiper-destacados', {
                    slidesPerView: 1,
                    spaceBetween: 20,
                    loop: true,
                    autoplay: {
                        delay: 3500,
                        disableOnInteraction: false,
                    },
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    breakpoints: {
                        640: {
                            slidesPerView: 1.2,
                            spaceBetween: 20,
                        },
                        768: {
                            slidesPerView: 2,
                            spaceBetween: 30,
                        },
                        1024: {
                            slidesPerView: 3,
                            spaceBetween: 30,
                        },
                    }
                });
            } else {
                console.error('Swiper is not defined');
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSwiper);
        } else {
            initSwiper();
        }
    })();
</script>
