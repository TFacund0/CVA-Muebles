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
                    <div class="d-flex gap-3 justify-content-center align-items-center">
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

