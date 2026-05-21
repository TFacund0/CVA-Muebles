<?= $this->extend('layout/main') ?>

<?= $this->section('extra-css') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/galeria.css?v=2.0')?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<header class="gallery-header text-center">
    <div class="container">
        <h1 class="display-2 fw-bold font-lora">CVA en tu Hogar</h1>
        <p class="lead">Nuestra mayor satisfacción es ver cómo nuestras piezas cobran vida en tu espacio.</p>
        <div class="gold-divider mx-auto mt-4"></div>
    </div>
</header>

<section class="py-5">
    <div class="container py-5">
        
        <?php if (session()->get('logged_in') && session()->get('perfil_id') == 1): ?>
            <div class="moderation-active-panel p-4 mb-5 rounded-4 shadow-sm animate-fade-in mx-auto border-artisan-gold">
                <div class="d-md-flex align-items-center justify-content-between text-center text-md-start gap-4">
                    <div class="d-flex flex-column flex-md-row align-items-center gap-3 mb-3 mb-md-0">
                        <div class="icon-badge-gold text-white rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-shield-check fs-4"></i>
                        </div>
                        <div>
                            <h4 class="h5 fw-bold text-cva-brown mb-1">Panel de Moderación Activo</h4>
                            <p class="text-muted mb-0 small">
                                <?php if ($pendientesCount > 0): ?>
                                    Hay <span class="badge bg-danger px-2.5 py-1 rounded-pill fw-bold badge-sm-text"><?= $pendientesCount ?></span> solicitud<?= $pendientesCount > 1 ? 'es' : '' ?> de foto<?= $pendientesCount > 1 ? 's' : '' ?> pendiente<?= $pendientesCount > 1 ? 's' : '' ?> por evaluar.
                                <?php else: ?>
                                    No hay fotos pendientes de revisión en este momento.
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="w-100 w-md-auto">
                        <a href="<?= base_url('admin/galeria') ?>" class="btn btn-vivid px-4 py-2.5 rounded-pill fw-bold btn-sm shadow w-100 text-center">
                            <i class="bi bi-check2-square me-2"></i> IR A EVALUAR
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (session()->get('logged_in')): ?>
            <div class="text-center mb-5">
                <button class="btn btn-vivid-premium px-5 py-3 rounded-pill fw-bold shadow-lg animate-fade-in" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUpload" aria-expanded="false" aria-controls="collapseUpload">
                    <i class="bi bi-camera-fill me-2"></i> ¿TENÉS UN CVA EN CASA? SUBÍ TU FOTO
                </button>
            </div>

            <div class="collapse" id="collapseUpload">
                <div class="upload-section p-4 p-md-5 mb-5 text-center shadow-lg border-artisan-gold mx-auto">
                    <h2 class="font-lora h3 fw-bold text-cva-brown mb-3">Compartí tu Legado</h2>
                    <p class="text-muted small mb-4">Contanos qué te parece tu mueble y unite a nuestra comunidad.</p>
                    
                    <form action="<?= base_url('galeria/subir') ?>" method="post" enctype="multipart/form-data" class="form-upload-photo mx-auto">
                        <?= csrf_field() ?>
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-cva-brown">Seleccioná tu imagen</label>
                            <input type="file" name="imagen" class="form-control rounded-pill p-3" required accept="image/*">
                        </div>
                        <div class="mb-4">
                            <textarea name="comentario" class="form-control rounded-4 p-3" rows="3" placeholder="Tu experiencia con el mueble..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-vivid w-100 py-3 rounded-pill fw-bold">ENVIAR MI FOTO</button>
                    </form>
                </div>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success mt-4 rounded-pill border-0 text-center shadow-sm mx-auto alert-max-width">
                    <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="masonry-gallery">
            <?php if (empty($fotos)): ?>
                <div class="gallery-empty-state text-center py-5 w-100">
                    <i class="gallery-empty-icon bi bi-camera text-muted opacity-25"></i>
                    <h3 class="text-muted font-lora mt-3">Pronto veremos aquí las fotos de nuestros clientes</h3>
                </div>
            <?php else: ?>
                <?php foreach ($fotos as $foto): ?>
                    <div class="gallery-item">
                        <img src="<?= base_url('assets/uploads/galeria/' . $foto['imagen']) ?>" alt="CVA en hogar">
                        <div class="gallery-info">
                            <span class="gallery-author">En el hogar de <?= esc($foto['nombre']) ?></span>
                            <p class="text-muted small mt-2 mb-0">"<?= esc($foto['comentario']) ?>"</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
