<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<section class="beneficios-page py-5">
    <div class="container py-5">
        <div class="text-center mb-5 animate-fade-in">
            <span class="text-gold fw-bold text-uppercase x-small" style="letter-spacing: 4px;">Experiencia CVA Muebles</span>
            <h1 class="display-3 fw-bold font-lora text-cva-brown mt-2">Nuestros Compromisos</h1>
            <p class="lead text-muted mx-auto mt-3" style="max-width: 700px;">
                En nuestra carpintería, cada pieza es una promesa de calidad y dedicación artesanal.
            </p>
            <div class="mx-auto mt-4" style="width: 80px; height: 4px; background: var(--cva-gold); border-radius: 2px;"></div>
        </div>

        <div class="row g-4 mt-5">
            <!-- Beneficio 1: Calidad Real -->
            <div class="col-lg-4">
                <div class="level-card p-5 h-100 text-center border-0 rounded-5 shadow-sm" style="background: white;">
                    <div class="level-icon mb-4 bg-cva-brown text-white mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; border-radius: 50%;">
                        <i class="bi bi-shield-check fs-2"></i>
                    </div>
                    <h3 class="font-lora fw-bold text-cva-brown">Maderas de Primera</h3>
                    <p class="text-muted small mb-4">Seleccionamos cada tabla para asegurar durabilidad y belleza natural.</p>
                </div>
            </div>

            <!-- Beneficio 2: Hecho a Mano -->
            <div class="col-lg-4">
                <div class="level-card p-5 h-100 text-center border-0 rounded-5 shadow-sm" style="background: white;">
                    <div class="level-icon mb-4 bg-cva-gold text-cva-brown mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; border-radius: 50%;">
                        <i class="bi bi-hammer fs-2"></i>
                    </div>
                    <h3 class="font-lora fw-bold text-cva-brown">Pasión Artesanal</h3>
                    <p class="text-muted small mb-4">No usamos procesos industriales masivos. Cada mueble tiene alma propia.</p>
                </div>
            </div>

            <!-- Beneficio 3: Atención Directa -->
            <div class="col-lg-4">
                <div class="level-card p-5 h-100 text-center border-0 rounded-5 shadow-sm" style="background: white;">
                    <div class="level-icon mb-4 bg-cva-vivid text-white mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; border-radius: 50%;">
                        <i class="bi bi-chat-heart fs-2"></i>
                    </div>
                    <h3 class="font-lora fw-bold text-cva-brown">Trato Cercano</h3>
                    <p class="text-muted small mb-4">Atención personalizada directa con el artesano para tus proyectos especiales.</p>
                </div>
            </div>
        </div>

        <div class="mt-5 pt-5 text-center">
            <h4 class="font-lora fw-bold text-cva-brown">¿Buscás algo a medida?</h4>
            <p class="text-muted">Estamos listos para hacer realidad tu idea en madera.</p>
            <a href="<?= base_url('informacionContacto') ?>" class="btn btn-brown-solid px-5 py-3 rounded-pill mt-3 shadow">CONTACTAR AHORA</a>
        </div>
    </div>
</section>

<?= $this->endSection() ?>