<?php
/**
 * Vista de Moderación de Galería de Clientes (Admin Gallery Moderation Dashboard)
 *
 * Muestra el panel interactivo de aprobación y moderación para las fotografías enviadas por los compradores.
 * Incorpora:
 * 1. Grilla de Moderación: Tarjetas dinámicas que exponen la imagen enviada, nombre del cliente y su testimonio.
 * 2. Gestión de Estados: Insignia visual indicando si la foto está "PENDIENTE" de aprobación o ya se encuentra "ACTIVA".
 * 3. Acciones Seguras: Formulario para la aprobación directa de imágenes y formulario con confirmación nativa para la eliminación permanente.
 *
 * @var array $fotos Listado de registros de la galería de clientes (`GaleriaClienteModel`).
 *                   Estructura de cada ítem:
 *                   - 'id' (int): ID único del registro.
 *                   - 'nombre' (string): Nombre del cliente que envió la reseña.
 *                   - 'comentario' (string): Breve testimonio o comentario sobre el mueble.
 *                   - 'imagen' (string): Nombre del archivo de imagen subido.
 *                   - 'activo' (string): Estado de visibilidad ('SI' o 'NO').
 *
 * Recursos Externos:
 * - Estilos: `assets/css/admin/admin-gallery.css`
 */
?>
<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('breadcrumbs') ?>
<li class="breadcrumb-item active small fw-bold text-gold" aria-current="page">MODERACIÓN DE GALERÍA</li>
<?= $this->endSection() ?>

<?= $this->section('extra-css') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/admin/admin-gallery.css?v=1.0') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-5 align-items-center g-4">
    <div class="col-lg-7 col-12">
        <div class="d-flex align-items-center gap-3 gap-md-4">
            <div class="dashboard-icon-main bg-brown text-gold shadow">
                <i class="bi bi-images"></i>
            </div>
            <div>
                <h1 class="display-6 display-md-5 fw-bold text-cva-brown mb-1">Moderación de Galería</h1>
                <p class="text-muted mb-0 small"><i class="bi bi-shield-check text-gold me-1"></i> Revisión de contenido enviado por clientes.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-5 col-12 text-lg-end text-start">
        <a href="<?= base_url('galeria') ?>" class="btn btn-admin-primary rounded-pill px-4 py-2 shadow-gold w-sm-100 justify-content-center">
            <i class="bi bi-eye me-2"></i> VER GALERÍA PÚBLICA
        </a>
    </div>
</div>

<div class="row g-4">
    <?php if (empty($fotos)): ?>
        <div class="col-12 text-center py-5">
            <i class="admin-gallery-empty-icon bi bi-inbox text-muted opacity-25"></i>
            <h4 class="text-muted mt-3">No hay fotos pendientes de revisión</h4>
        </div>
    <?php else: ?>
        <?php foreach ($fotos as $foto): ?>
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden moderation-card">
                    <div class="position-relative overflow-hidden moderation-img-container">
                        <img src="<?= base_url('assets/uploads/galeria/' . $foto['imagen']) ?>" class="w-100 h-100" alt="Foto cliente">
                        <?php if ($foto['activo'] == 'NO'): ?>
                            <span class="position-absolute top-0 end-0 m-3 badge bg-warning text-dark">PENDIENTE</span>
                        <?php else: ?>
                            <span class="position-absolute top-0 end-0 m-3 badge bg-success">ACTIVA</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <h6 class="fw-bold text-gold mb-1">Cliente: <?= esc($foto['nombre']) ?></h6>
                        <p class="small text-muted flex-grow-1 mb-4">"<?= esc($foto['comentario']) ?>"</p>

                        <div class="d-flex gap-2 align-items-center mt-auto w-100">
                            <?php if ($foto['activo'] == 'NO'): ?>
                                <form action="<?= base_url('admin/galeria/aprobar/' . $foto['id']) ?>" method="post" class="flex-grow-1 m-0 d-flex align-items-center">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-success btn-approve-gallery w-100 m-0">APROBAR</button>
                                </form>
                            <?php endif; ?>
                            <form action="<?= base_url('admin/galeria/eliminar/' . $foto['id']) ?>" method="post" class="m-0 d-flex align-items-center" onsubmit="return confirm('¿Eliminar esta foto permanentemente?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline-danger rounded-circle p-2 btn-delete-gallery m-0">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</div>
<?= $this->endSection() ?>