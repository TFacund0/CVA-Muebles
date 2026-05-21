<?php
/**
 * Vista de Detalle y Ficha de Producto (Public Product Detail Page)
 *
 * Renderiza la ficha detallada e interactiva de un mueble individual en el catálogo público.
 * Orquesta:
 * 1. Galería de Fotos: Visualizador dinámico con miniaturas táctiles de imágenes alternativas.
 * 2. Detalles Constructivos: Descripción detallada de las vetas, tipos de madera y acabados.
 * 3. Botones de Compra/Consulta: Agregar al carro o solicitar presupuestos especiales por WhatsApp.
 *
 * @var array $producto {
 *     @var int|string $id_producto Identificador del producto.
 *     @var string $nombre_prod Nombre comercial.
 *     @var string $imagen Nombre del archivo de imagen principal.
 *     @var string $categoria Nombre de la categoría.
 *     @var float|int $precio_vta Precio de venta.
 *     @var int $stock Cantidad disponible.
 *     @var string|null $descripcion Detalles constructivos.
 *     @var array|null $galeria Listado de imágenes alternativas asociadas de `ProductoImagenModel`.
 * }
 * @var bool $env_cart_enabled Habilitación global del carrito web.
 * @var string $env_whatsapp Número de WhatsApp del taller para derivaciones.
 */
?>
<?= $this->extend('layout/main') ?>

<?= $this->section('extra-css') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/detalle_producto.css?v=3.0') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="product-detail-wrapper">
    <!-- Breadcrumb Nav (Sutil) -->
    <div class="container mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('productos') ?>" class="text-decoration-none text-gold fw-bold small">CATÁLOGO</a></li>
                <li class="breadcrumb-item active small" aria-current="page"><?= strtoupper(esc($producto['nombre_prod'])) ?></li>
            </ol>
        </nav>
    </div>

    <div class="container main-artisan-card rounded-4 overflow-hidden animate-fade-in-up">
        <div class="row g-0">
            <!-- Columna de Imagen -->
            <div class="col-lg-6 image-column">
                <div class="image-container main-image-box shadow-sm rounded-4 overflow-hidden mb-3 position-relative">
                    <img src="<?= base_url('assets/uploads/' . $producto['imagen']) ?>" 
                         class="img-fluid main-img" 
                         id="main-product-img"
                         alt="<?= esc($producto['nombre_prod']) ?>">
                    
                    <?php if(!empty($producto['galeria'])): ?>
                        <!-- Flechas de navegación (Aseguramos que estén dentro del position-relative) -->
                        <button class="gallery-arrow arrow-left" aria-label="Anterior">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button class="gallery-arrow arrow-right" aria-label="Siguiente">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    <?php endif; ?>
                </div>
                
                <?php if(!empty($producto['galeria'])): ?>
                    <div class="product-gallery-thumbs d-flex gap-2 overflow-auto pb-2">
                        <!-- Imagen Principal como miniatura -->
                        <div class="thumb-item active">
                            <img src="<?= base_url('assets/uploads/' . $producto['imagen']) ?>" alt="Principal" loading="lazy">
                        </div>
                        <?php foreach($producto['galeria'] as $img): ?>
                            <div class="thumb-item">
                                <img src="<?= base_url('assets/uploads/' . $img['imagen']) ?>" alt="Galería" loading="lazy">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Columna de Información -->
            <div class="col-lg-6 info-column d-flex flex-column">
                <div class="header-info">
                    <span class="category-tag"><?= esc($producto['categoria']) ?></span>
                    <h1 class="product-title"><?= esc($producto['nombre_prod']) ?></h1>
                    <div class="title-divider"></div>
                </div>

                <div class="price-box">
                    <div class="price-label">Inversión artesanal</div>
                    <div class="price-value">
                        <span class="price-symbol">$</span>
                        <span><?= number_format($producto['precio_vta'], 0, ',', '.') ?></span>
                    </div>
                    <div class="stock-info">
                        <i class="bi bi-hammer text-gold" style="color: var(--cva-gold, #b8860b) !important;"></i>
                        <span class="fw-bold" style="color: var(--cva-gold, #b8860b);">Fabricación bajo pedido (Consultar tiempos de entrega)</span>
                    </div>
                </div>

                <div class="features-list">
                    <div class="feature-item">
                        <div class="feature-icon"><i class="bi bi-hammer"></i></div>
                        <div class="feature-text">
                            <h6>Mano de obra local</h6>
                            <p>Fabricado artesanalmente en Mantilla, Corrientes.</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="bi bi-tree"></i></div>
                        <div class="feature-text">
                            <h6>Maderas seleccionadas</h6>
                            <p>Piezas de alta calidad y origen responsable.</p>
                        </div>
                    </div>
                </div>

                <div class="actions-area mt-auto">
                    <?php if ($env_cart_enabled): ?>
                        <?php if (session()->get('logged_in')): ?>
                            <?php if ($producto['stock'] > 0): ?>
                                <form action="<?= base_url('carrito/add') ?>" method="post" class="mb-3">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id_producto" value="<?= esc($producto['id_producto']) ?>">
                                    <input type="hidden" name="precio_vta" value="<?= esc($producto['precio_vta']) ?>">
                                    <input type="hidden" name="nombre_prod" value="<?= esc($producto['nombre_prod']) ?>">
                                    <input type="hidden" name="imagen" value="<?= esc($producto['imagen']) ?>">
                                    <button type="submit" class="btn btn-artisan-primary w-100 py-3 rounded-3 fs-5">
                                        <i class="bi bi-cart-plus me-2"></i> AGREGAR AL CARRITO
                                    </button>
                                </form>
                            <?php else: ?>
                                <a href="https://wa.me/<?= $env_whatsapp ?>?text=Hola!%20Me%20interesa%20el%20mueble%20<?= urlencode($producto['nombre_prod']) ?>%20y%20me%20gustaría%20consultar%20para%20encargarlo." 
                                   target="_blank" class="btn btn-outline-brown w-100 py-3 mb-3 rounded-3 fs-5">
                                    <i class="bi bi-whatsapp me-2"></i> CONSULTAR FABRICACIÓN
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="<?= base_url('login') ?>" class="btn btn-outline-secondary w-100 py-3 mb-3 rounded-3">
                                <i class="bi bi-person-lock me-2"></i> INICIÁ SESIÓN PARA COMPRAR
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if (session()->get('logged_in')): ?>
                            <?php 
                                $whatsapp_num = $env_whatsapp;
                                $mensaje = urlencode("Hola! Me interesa este mueble artesanal: " . $producto['nombre_prod'] . ". ¿Podrías darme más detalles?");
                                $url_whatsapp = "https://wa.me/{$whatsapp_num}?text={$mensaje}";
                            ?>
                            <a href="<?= $url_whatsapp ?>" target="_blank" class="btn btn-whatsapp-premium mb-3">
                                <i class="bi bi-whatsapp"></i> CONSULTAR POR WHATSAPP
                            </a>
                        <?php else: ?>
                            <a href="<?= base_url('login') ?>" class="btn btn-outline-secondary w-100 py-3 mb-3 rounded-3">
                                <i class="bi bi-person-lock me-2"></i> INICIÁ SESIÓN PARA CONSULTAR
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (session()->get('logged_in')): ?>
                        <a href="https://wa.me/<?= $env_whatsapp ?>?text=Hola!%20Me%20gustaría%20personalizar%20el%20mueble%20<?= urlencode($producto['nombre_prod']) ?>" 
                           class="btn btn-personalizar">
                            <i class="bi bi-pencil-square"></i> SOLICITAR MEDIDAS ESPECIALES
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Ficha Técnica -->
        <div class="technical-section">
            <div class="row align-items-center">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h2 class="technical-title">Detalles <br><span>Constructivos</span></h2>
                    <p class="text-muted">Cada veta y nudo de la madera es único, lo que garantiza que tu mueble sea una pieza irrepetible.</p>
                </div>
                <div class="col-lg-8">
                    <div class="description-box">
                        <?php if (!empty($producto['descripcion'])): ?>
                            <?= nl2br(esc($producto['descripcion'])) ?>
                        <?php else: ?>
                            <p class="mb-0"><i class="bi bi-info-circle me-2"></i> Esta pieza se fabrica siguiendo técnicas de carpintería tradicional. Se entrega terminada con productos protectores que resaltan la belleza natural de la madera. Consultanos por diferentes acabados (brillante, mate o satinado).</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Trust Badges -->
            <div class="row trust-badges g-4">
                <div class="col-md-4">
                    <div class="badge-card">
                        <div class="badge-icon-wrap">🚚</div>
                        <h5>Envío Seguro</h5>
                        <p class="small text-muted">Coordinamos la logística para que tu mueble llegue impecable.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="badge-card">
                        <div class="badge-icon-wrap">🛡️</div>
                        <h5>Garantía de Obra</h5>
                        <p class="small text-muted">Aseguramos la integridad estructural de cada pieza.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="badge-card">
                        <div class="badge-icon-wrap">🌿</div>
                        <h5>Madera Sustentable</h5>
                        <p class="small text-muted">Utilizamos recursos de bosques gestionados responsablemente.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('extra-js') ?>
<script src="<?= base_url('assets/js/pages/product-detail.js?v=1.0') ?>"></script>
<?= $this->endSection() ?>

