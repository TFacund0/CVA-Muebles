<?= $this->extend('layout/main') ?>

<?= $this->section('extra-css') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/quienesSomos.css?v=6.1')?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div id="quienes-somos-wrapper">
    <!-- Cabecera Premium (FONDO FIJO) -->
    <header class="info-header">
        <div class="container animate-fade-in">
            <span class="text-gold fw-bold text-uppercase x-small" style="letter-spacing: 3px;">Tradición Familiar</span>
            <h1 class="display-3 fw-bold font-lora">El Alma de CVA</h1>
            <div class="divider-artisan mx-auto mb-4"></div>
            <p class="lead">Más que muebles, creamos legados tallados en la nobleza de la madera correntina.</p>
        </div>
    </header>

    <!-- BLOQUE 1: HISTORIA (BLANCO / SPLIT) -->
    <section class="section-historia-full bg-white overflow-hidden">
        <div class="row g-0 align-items-center">
            <div class="col-lg-6 order-2 order-lg-1">
                <div class="img-historia-full" style="background-image: url('<?= base_url('assets/img/ui/textures/carpienteria.jpg') ?>'); min-height: 700px; background-size: cover; background-position: center;"></div>
            </div>
            <div class="col-lg-6 p-5 p-xl-5 order-1 order-lg-2">
                <div class="max-width-600 mx-auto">
                    <span class="text-gold fw-bold text-uppercase small" style="letter-spacing: 2px;">Nuestros Inicios</span>
                    <h2 class="display-4 fw-bold font-lora text-cva-brown mt-2 mb-4">Legado y Tradición</h2>
                    <p class="lead text-muted mb-4">
                        CVA Muebles nace en el corazón de Mantilla, Corrientes, como un tributo al oficio artesano y la nobleza de la madera. Bajo la visión de <strong>César Víctor Acevedo</strong>, nuestro taller se ha convertido en un referente de la <em>Carpintería de Autor</em>.
                    </p>
                    <p class="text-muted mb-5">
                        Lo que comenzó como una pasión familiar por transformar la materia prima, hoy es una realidad que combina técnicas tradicionales de ebanistería con un diseño contemporáneo. En CVA no fabricamos muebles en serie; creamos compañeros de vida.
                    </p>
                    <div class="artisan-quote-premium">
                        <p class="fst-italic mb-0">
                            "Nuestra misión es que cada mueble cuente una historia de calidad, calidez y herencia correntina."
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- BLOQUE 2: FILOSOFÍA (ARENA) -->
    <section class="section-filosofia-full py-5" style="background-color: var(--cva-sand);">
        <div class="container py-5 text-center">
            <div class="row">
                <div class="col-lg-9 mx-auto">
                    <span class="text-vivid fw-bold text-uppercase small" style="letter-spacing: 2px;">Nuestra Esencia</span>
                    <h2 class="display-4 fw-bold font-lora text-cva-brown mt-2 mb-5">Compromiso Artesano</h2>
                    <p class="display-6 font-lora italic text-muted mb-5" style="line-height: 1.4;">
                        "En cada pieza que creamos, ponemos el mismo cuidado que pondríamos en un mueble para nuestra propia casa"
                    </p>
                    <div class="row g-4 mt-4">
                        <div class="col-md-4">
                            <div class="value-card">
                                <i class="bi bi-tree fs-2 text-gold"></i>
                                <h6 class="fw-bold mt-3">Sustentabilidad</h6>
                                <p class="small text-muted">Maderas de reforestación y proceso residuo cero.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="value-card">
                                <i class="bi bi-hammer fs-2 text-gold"></i>
                                <h6 class="fw-bold mt-3">Hecho a Mano</h6>
                                <p class="small text-muted">Técnicas de ebanistería tradicional correntina.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="value-card">
                                <i class="bi bi-award fs-2 text-gold"></i>
                                <h6 class="fw-bold mt-3">Calidad Eterna</h6>
                                <p class="small text-muted">Muebles diseñados para pasar de generación en generación.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<!-- BLOQUE 3: EQUIPO (VERDE BOSQUE DINÁMICO) -->
    <section class="section-equipo-full py-5" style="background-color: var(--cva-forest); color: white;">
        <div class="container py-5">
            <div class="row mb-5 text-center">
                <div class="col-lg-8 mx-auto">
                    <span class="text-gold fw-bold text-uppercase small" style="letter-spacing: 2px;">El Factor Humano</span>
                    <h2 class="display-4 fw-bold font-lora text-white mt-2">Manos Maestras</h2>
                    <div class="divider-artisan mx-auto mb-4"></div>
                </div>
            </div>
            
            <div class="row g-4">
                <!-- Miembro 1 -->
                <div class="col-md-4">
                    <div class="team-card-premium">
                        <div class="team-img-wrapper">
                            <img src="<?= base_url('assets/img/team/viejo.jpg') ?>" alt="Acevedo Cesar">
                            <div class="team-overlay">
                                <div class="social-links-team">
                                    <a href="#"><i class="bi bi-instagram"></i></a>
                                    <a href="#"><i class="bi bi-linkedin"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="team-body text-center">
                            <h5 class="fw-bold mb-1">Acevedo Cesar</h5>
                            <p class="text-gold small fw-bold text-uppercase mb-3">Maestro Ebanista</p>
                            <div class="team-bio-short">
                                <p class="small opacity-75">Más de 25 años transformando la madera en arte. Especialista en tallado a mano.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Miembro 2 -->
                <div class="col-md-4">
                    <div class="team-card-premium">
                        <div class="team-img-wrapper">
                            <img src="<?= base_url('assets/img/team/diseñadora.jpg') ?>" alt="Valeria Acevedo">
                            <div class="team-overlay">
                                <div class="social-links-team">
                                    <a href="#"><i class="bi bi-instagram"></i></a>
                                    <a href="#"><i class="bi bi-behance"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="team-body text-center">
                            <h5 class="fw-bold mb-1">Valeria Acevedo</h5>
                            <p class="text-gold small fw-bold text-uppercase mb-3">Diseñadora Industrial</p>
                            <div class="team-bio-short">
                                <p class="small opacity-75">Fusiona lo contemporáneo con lo tradicional para crear piezas que son tendencia.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Miembro 3 -->
                <div class="col-md-4">
                    <div class="team-card-premium">
                        <div class="team-img-wrapper">
                            <img src="<?= base_url('assets/img/team/diseñador.jpg') ?>" alt="Andrés Rojas">
                            <div class="team-overlay">
                                <div class="social-links-team">
                                    <a href="#"><i class="bi bi-instagram"></i></a>
                                    <a href="#"><i class="bi bi-facebook"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="team-body text-center">
                            <h5 class="fw-bold mb-1">Andrés Rojas</h5>
                            <p class="text-gold small fw-bold text-uppercase mb-3">Maestro de Acabados</p>
                            <div class="team-bio-short">
                                <p class="small opacity-75">Dota a cada pieza de un carácter único con técnicas de envejecido natural.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- Fin Row Equipo -->
        </div> <!-- Fin Container Equipo -->
    </section>
</div>
<?= $this->endSection() ?>
