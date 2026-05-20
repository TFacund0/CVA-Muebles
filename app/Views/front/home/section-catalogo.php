<?php
    $session = session();
    $isLogged = $session->get('logged_in');
?>

<style>
    /* --- ESPECIALIDADES PREMIUM (RESTAURADO) --- */
    .section-categorias { padding: 80px 0; background-color: white !important; background-image: none !important; }
    .catalogo-card-premium {
        position: relative;
        height: 450px;
        border-radius: 2.5rem;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(62, 39, 35, 0.08);
        transition: all 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        border: 1px solid rgba(0,0,0,0.03);
    }
    .catalogo-card-premium:hover {
        transform: translateY(-15px);
        box-shadow: 0 40px 80px rgba(62, 39, 35, 0.15);
    }
    .card-img-artisan {
        width: 100%; height: 100%; object-fit: cover;
        transition: transform 1.2s ease;
    }
    .catalogo-card-premium:hover .card-img-artisan {
        transform: scale(1.1);
    }
    .card-overlay-modern {
        position: absolute; inset: 0;
        background: linear-gradient(to top, rgba(38, 20, 18, 0.9) 0%, rgba(38, 20, 18, 0.2) 60%, transparent 100%);
        padding: 2.5rem; display: flex; flex-direction: column; justify-content: flex-end;
        color: white; transition: background 0.4s;
    }
    .catalogo-card-premium:hover .card-overlay-modern { background: linear-gradient(to top, var(--cva-brown) 0%, rgba(62, 39, 35, 0.6) 100%); }
    .catalogo-card-premium:hover .category-line { width: 100%; }

    /* Responsive Categorías */
    @media (max-width: 991.98px) {
        .section-categorias { padding: 60px 0; }
        .catalogo-card-premium { height: 280px; border-radius: 1.5rem; }
        .card-overlay-modern { padding: 1.2rem; }
        .display-5 { font-size: 2.2rem !important; }
        .card-overlay-modern h3 { font-size: 1.5rem !important; }
    }

    /* --- PRODUCTOS DESTACADOS --- */
    .section-destacados-dinamica { padding: 100px 0; background-color: var(--cva-sand); }
    .product-card-vivid {
        background: white; border-radius: 2rem; padding: 1.5rem;
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 1px solid rgba(0,0,0,0.05); position: relative;
    }
    .product-card-vivid:hover { transform: scale(1.05) translateY(-10px); box-shadow: 0 30px 60px rgba(62, 39, 35, 0.15); z-index: 10; }
    .product-img-container { border-radius: 1.5rem; overflow: hidden; margin-bottom: 1.5rem; height: 220px; position: relative; }
    .product-img-container img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.8s; }
    .product-card-vivid:hover .product-img-container img { transform: scale(1.1); }
    .price-badge-artisan {
        position: absolute; top: 15px; right: 15px; background: var(--cva-gold);
        color: white; padding: 0.5rem 1rem; border-radius: 1rem; font-weight: 800; font-size: 0.9rem;
    }
    .btn-detail-artisan {
        width: 45px; height: 45px; border-radius: 50%; background: var(--cva-brown);
        color: white; display: flex; align-items: center; justify-content: center;
        transition: all 0.3s; border: none; text-decoration: none;
    }
    .btn-detail-artisan:hover { background: var(--cva-gold); transform: rotate(90deg); color: white; }

    /* --- SWIPER CAROUSEL CUSTOM --- */
    .swiper-destacados { padding: 0 0 4rem !important; }
    .swiper-button-next, .swiper-button-prev {
        color: var(--cva-brown) !important;
        background: white;
        width: 50px !important;
        height: 50px !important;
        border-radius: 50%;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        border: 1px solid #f0e6d6;
        transition: all 0.3s ease;
    }
    .swiper-button-next:after, .swiper-button-prev:after { font-size: 1.2rem !important; font-weight: 800; }
    .swiper-button-next:hover, .swiper-button-prev:hover { background: var(--cva-gold); color: white !important; border-color: var(--cva-gold); }
    .swiper-pagination-bullet-active { background: var(--cva-gold) !important; }
</style>

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

<style>
    .mission-statement-premium {
        background-color: var(--cva-forest) !important;
        color: white;
        padding: 160px 0 !important;
        position: relative;
        overflow: hidden;
    }
    .mission-bg-accent {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        font-size: 25rem;
        font-family: 'Lora', serif;
        opacity: 0.03;
        z-index: 0;
        pointer-events: none;
        user-select: none;
    }
    .mission-content {
        position: relative;
        z-index: 1;
        max-width: 900px;
        margin: 0 auto;
    }
    .mission-divider {
        width: 60px;
        height: 3px;
        background: var(--cva-gold);
        margin: 2rem auto;
        border-radius: 2px;
    }
    .mission-quote {
        font-family: 'Lora', serif;
        font-style: italic;
        font-size: 1.5rem;
        line-height: 1.8;
        opacity: 0.9;
        position: relative;
        padding: 0 40px;
    }
    .mission-quote::before, .mission-quote::after {
        content: '"';
        position: absolute;
        font-size: 4rem;
        color: var(--cva-gold);
        opacity: 0.5;
        font-family: 'Lora', serif;
    }
    .mission-quote::before { top: -20px; left: 0; }
    .mission-quote::after { bottom: -60px; right: 0; }

    /* Responsive Misión */
    @media (max-width: 991.98px) {
        .mission-statement-premium { padding: 100px 0 !important; }
        .display-3 { font-size: 2.5rem !important; }
        .mission-quote { font-size: 1.2rem; padding: 0 20px; }
        .mission-bg-accent { font-size: 15rem; }
    }
    @media (max-width: 575.98px) {
        .mission-statement-premium { padding: 80px 0 !important; }
        .display-3 { font-size: 2rem !important; }
        .mission-quote { font-size: 1.1rem; }
    }
</style>

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
                        ['id' => 1, 'img' => 'Muebles 56.jpeg', 'tit' => 'Mesa Comedor Roble', 'desc' => 'Roble macizo tallado a mano.', 'price' => '$1.2M'],
                        ['id' => 2, 'img' => 'Muebles 10.jpeg', 'tit' => 'Alacena Rústica', 'desc' => 'Pino seleccionado con forja.', 'price' => '$650k'],
                        ['id' => 3, 'img' => 'Muebles 46.jpeg', 'tit' => 'Set de 4 Sillas', 'desc' => 'Diseño ergonómico recuperado.', 'price' => '$1.2M'],
                        ['id' => 4, 'img' => 'Muebles 68.jpeg', 'tit' => 'Mesa Ratona', 'desc' => 'Acabado rústico premium.', 'price' => '$450k'],
                        ['id' => 5, 'img' => 'Muebles 22.jpeg', 'tit' => 'Mueble de TV', 'desc' => 'Líneas modernas y madera noble.', 'price' => '$850k'],
                        ['id' => 6, 'img' => 'Muebles 69.jpeg', 'tit' => 'Respaldo de Cama', 'desc' => 'Textura natural y robustez.', 'price' => '$550k'],
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
                                <a href="<?= base_url('productos') ?>" class="btn-detail-artisan" style="width: 35px; height: 35px;"><i class="bi bi-eye"></i></a>
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
    
    <style>
        /* Responsive Ubicación */
        @media (max-width: 991.98px) {
            .section-ubicacion .row.g-0 { flex-direction: column; } /* Texto primero, luego mapa */
            .section-ubicacion .col-lg-5 { padding: 40px 20px !important; }
            .section-ubicacion .display-3 { font-size: 2.5rem !important; }
            .section-ubicacion iframe { min-height: 400px !important; }
            .section-ubicacion .row.g-0 { min-height: auto !important; }
        }
    </style>
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
