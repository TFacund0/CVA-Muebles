<!-- 
  =============================================
  ARTISAN HERO - THE SOUL OF THE WORKSHOP
  =============================================
-->

<div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
    
    <!-- Indicadores Minimalistas -->
    <div class="carousel-indicators mb-5">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
    </div>

    <div class="carousel-inner" style="height: 85vh; min-height: 600px;">

        <!-- Slide 1: El Taller -->
        <div class="carousel-item active h-100">
            <div class="hero-overlay-artisan"></div>
            <img src="<?= base_url('assets/img/content/hero/taller.jpg') ?>" class="d-block w-100 h-100 object-fit-cover zoom-animation" alt="Taller">
            <div class="carousel-caption d-flex align-items-center justify-content-center h-100 text-center">
                <div class="glass-caption animate-up">
                    <span class="badge bg-gold mb-3 px-3 py-2 rounded-pill text-uppercase" style="letter-spacing: 2px;">Mueblería de Autor</span>
                    <h1 class="display-2 fw-bold font-lora mb-3">La Esencia de la <br><span class="text-gold">Madera Noble</span></h1>
                    <p class="fs-5 mb-4 opacity-90 mx-auto" style="max-width: 600px;">Transformamos maderas seleccionadas en piezas únicas que respiran historia y elegancia.</p>
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="<?= base_url('productos') ?>" class="btn btn-vivid rounded-pill px-5 py-3 fw-bold shadow-lg">VER CATÁLOGO</a>
                        <a href="<?= base_url('quienesSomos') ?>" class="btn btn-outline-light rounded-pill px-5 py-3 fw-bold">NUESTRO TALLER</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 2: El Detalle -->
        <div class="carousel-item h-100">
            <div class="hero-overlay-artisan"></div>
            <img src="<?= base_url('assets/img/content/hero/Muebles 22.jpeg') ?>" class="d-block w-100 h-100 object-fit-cover zoom-animation" alt="Calidad">
            <div class="carousel-caption d-flex align-items-center justify-content-start h-100 text-start ps-lg-5">
                <div class="glass-caption left-align animate-left">
                    <h2 class="display-3 fw-bold font-lora mb-3">Oficio que <br>Perdura</h2>
                    <p class="fs-5 mb-4 opacity-90" style="max-width: 500px;">Cada veta cuenta una historia de paciencia, técnica y amor por lo artesanal.</p>
                    <a href="<?= base_url('productos') ?>" class="btn btn-gold rounded-pill px-5 py-3 fw-bold">DESCUBRIR MÁS</a>
                </div>
            </div>
        </div>

        <!-- Slide 3: El Hogar -->
        <div class="carousel-item h-100">
            <div class="hero-overlay-artisan"></div>
            <img src="<?= base_url('assets/img/content/hero/Muebles 69.jpeg') ?>" class="d-block w-100 h-100 object-fit-cover zoom-animation" alt="Pasión">
            <div class="carousel-caption d-flex align-items-center justify-content-end h-100 text-end pe-lg-5">
                <div class="glass-caption right-align animate-right">
                    <h2 class="display-3 fw-bold font-lora mb-3">Diseño para <br>Toda la Vida</h2>
                    <p class="fs-5 mb-4 opacity-90" style="max-width: 500px;">Creamos el corazón de tu hogar con muebles que trascienden generaciones.</p>
                    <a href="<?= base_url('informacionContacto') ?>" class="btn btn-vivid rounded-pill px-5 py-3 fw-bold">PEDIR PRESUPUESTO</a>
                </div>
            </div>
        </div>

    </div>

    <!-- Controles Stylized -->
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <div class="control-circle"><i class="bi bi-arrow-left"></i></div>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <div class="control-circle"><i class="bi bi-arrow-right"></i></div>
    </button>

</div>

<style>
    /* Override Bootstrap constraints to center the flexbox layout completely */
    #heroCarousel .carousel-caption {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 0;
        margin: 0;
        height: 100%;
        width: 100%;
        display: flex !important;
        align-items: center;
        z-index: 10;
    }

    /* Hero Visuals */
    .hero-overlay-artisan {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: linear-gradient(to bottom, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.7) 100%);
        z-index: 1;
    }

    .glass-caption {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        padding: 3rem;
        border-radius: 2rem;
        color: white;
        z-index: 2;
        box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        max-width: 700px;
        width: 100%;
    }

    .glass-caption.left-align { border-left: 5px solid var(--cva-gold); }
    .glass-caption.right-align { border-right: 5px solid var(--cva-vivid); }

    /* Animations */
    .zoom-animation {
        animation: slowZoom 20s infinite alternate linear;
    }

    @keyframes slowZoom {
        from { transform: scale(1); }
        to { transform: scale(1.1); }
    }

    .animate-up { animation: fadeInUp 1s ease-out; }
    .animate-left { animation: fadeInLeft 1s ease-out; }
    .animate-right { animation: fadeInRight 1s ease-out; }

    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeInLeft { from { opacity: 0; transform: translateX(-30px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes fadeInRight { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }

    /* Controls */
    .control-circle {
        width: 60px; height: 60px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        color: white;
        font-size: 1.5rem;
        transition: all 0.3s;
    }
    .control-circle:hover {
        background: var(--cva-gold);
        color: white;
        transform: scale(1.1);
    }

    /* Responsive Hero */
    @media (max-width: 991.98px) {
        .carousel-inner { height: 70vh !important; min-height: 500px !important; }
        .glass-caption { padding: 2rem !important; margin: 0 1.5rem; max-width: calc(100% - 3rem); }
        .display-2 { font-size: 2.5rem !important; }
        .display-3 { font-size: 2.2rem !important; }
        .fs-5 { font-size: 1rem !important; }
        .btn-vivid, .btn-outline-light, .btn-gold { padding: 0.8rem 1.5rem !important; font-size: 0.9rem !important; }
        #heroCarousel .carousel-caption { padding: 0 !important; }
    }

    @media (max-width: 575.98px) {
        .carousel-inner { height: 60vh !important; min-height: 400px !important; }
        .display-2 { font-size: 2rem !important; }
        .display-3 { font-size: 1.8rem !important; }
        .glass-caption { padding: 1.5rem !important; border-radius: 1.5rem !important; }
        .carousel-control-prev, .carousel-control-next { display: none; } /* Ocultar flechas en móvil muy pequeño */
    }
</style>
