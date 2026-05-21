<?php
/**
 * Vista de Mis Favoritos (Customer Favorites Catalog Page)
 *
 * Renderiza el listado personalizado de muebles guardados por el cliente.
 * Incorpora:
 * 1. Barra de Búsqueda Dinámica: Filtrado instantáneo por coincidencia de texto en título.
 * 2. Filtros de Categoría: Botones de categorías dinámicas obtenidas a partir de la lista de favoritos.
 * 3. Botones de Compra/Acción: Eliminar de la lista de deseos o añadir de forma rápida al carrito de compras.
 *
 * @var array $favoritos Listado de productos marcados como favoritos por el cliente actual.
 *                       Estructura de cada ítem:
 *                       - 'producto_id' (int): ID único de la pieza.
 *                       - 'nombre_prod' (string): Nombre comercial.
 *                       - 'imagen' (string): Imagen principal.
 *                       - 'categoria' (string|null): Categoría del mueble.
 *                       - 'descripcion' (string): Breve reseña constructiva.
 *                       - 'precio_vta' (float): Precio de venta.
 * @var bool $env_cart_enabled Estado del módulo del carrito web.
 */
?>
<?= $this->extend('layout/main') ?>

<?= $this->section('extra-css') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/pages/favoritos.css?v=2.0') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<header class="fav-header text-center">
    <div class="container animate-fade-in">
        <span class="text-gold fw-bold text-uppercase x-small" style="letter-spacing: 4px;">Tu Selección Personal</span>
        <h1 class="display-3 fw-bold font-lora mt-2">Mis Favoritos</h1>
        <div class="mx-auto mt-3" style="width: 60px; height: 4px; background: var(--cva-gold); border-radius: 2px;"></div>
    </div>
</header>

<div class="container mb-5 pb-5">
    <?php if (empty($favoritos)): ?>
        <div class="empty-state-fav text-center">
            <div class="mb-4">
                <i class="bi bi-heart-break text-muted opacity-25" style="font-size: 6rem;"></i>
            </div>
            <h2 class="font-lora text-cva-brown fw-bold">Tu lista está vacía</h2>
            <p class="text-muted mb-5">Parece que aún no has encontrado esa pieza especial.</p>
            <a href="<?= base_url('productos') ?>" class="btn btn-vivid px-5 py-3 rounded-pill fw-bold shadow">EXPLORAR CATÁLOGO</a>
        </div>
    <?php else: ?>
        <!-- Buscador y Filtros Premium de Favoritos -->
        <div class="row mb-5 g-3 align-items-center animate-fade-in">
            <!-- Buscador -->
            <div class="col-lg-5 col-md-6">
                <div class="input-group bg-white rounded-pill px-3 py-2 border shadow-sm transition-all focus-within-gold" style="height: 52px; align-items: center;">
                    <span class="input-group-text bg-transparent border-0 py-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="search-favs" class="form-control border-0 bg-transparent py-0" placeholder="Buscar entre tus favoritos..." aria-label="Buscar favoritos" style="box-shadow: none;">
                    <button class="btn btn-link py-0 px-2 d-none clear-search-btn" id="clear-search" type="button"><i class="bi bi-x-circle-fill fs-5"></i></button>
                </div>
            </div>

            <!-- Filtro de Categorías -->
            <div class="col-lg-7 col-md-6">
                <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                    <button class="btn btn-filter-artisan active" data-filter="todos">
                        <i class="bi bi-collection-fill me-1"></i> Todos
                    </button>
                    <?php
                    $categorias_vistas = [];
                    foreach ($favoritos as $fav) {
                        $cat_name = trim($fav['categoria'] ?? '');
                        if (empty($cat_name)) continue;
                        $cat_lower = mb_strtolower($cat_name);
                        if (in_array($cat_lower, $categorias_vistas)) continue;
                        $categorias_vistas[] = $cat_lower;
                    ?>
                        <button class="btn btn-filter-artisan" data-filter="<?= esc($cat_lower) ?>">
                            <?= esc($cat_name) ?>
                        </button>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Estado Vacío Auxiliar para Filtros/Búsqueda de Favoritos -->
        <div id="no-results-fav" class="empty-search-state d-none mb-5 animate-fade-in">
            <div class="mb-4">
                <i class="bi bi-search text-gold opacity-25" style="font-size: 5rem;"></i>
            </div>
            <h3 class="fw-bold text-cva-brown font-lora">No encontramos coincidencias</h3>
            <p class="text-muted mb-0">Prueba buscando con otros términos o seleccionando otra categoría.</p>
        </div>

        <!-- Grid de Favoritos -->
        <div class="row g-4" id="fav-container">
            <?php foreach ($favoritos as $fav): ?>
                <?php
                $cat_lower = mb_strtolower(trim($fav['categoria'] ?? ''));
                $nombre_lower = mb_strtolower(trim($fav['nombre_prod'] ?? ''));
                ?>
                <div class="col-lg-4 col-md-6 fav-item" data-categorias="<?= esc($cat_lower) ?>" data-nombre="<?= esc($nombre_lower) ?>">
                    <div class="fav-card">
                        <div class="fav-img-wrapper">
                            <img src="<?= base_url('assets/uploads/' . $fav['imagen']) ?>" alt="<?= $fav['nombre_prod'] ?>">
                            <button data-id="<?= $fav['producto_id'] ?>" class="remove-fav-btn shadow-sm" title="Quitar de favoritos">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                        <div class="p-4 d-flex flex-column" style="min-height: 250px;">
                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <span class="x-small fw-bold text-gold text-uppercase" style="letter-spacing: 2px;">Pieza de Autor</span>
                                <?php if (!empty($fav['categoria'])): ?>
                                    <span class="badge bg-light text-muted x-small shadow-sm" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1px; border: 1px solid rgba(0,0,0,0.03);"><?= esc($fav['categoria']) ?></span>
                                <?php endif; ?>
                            </div>
                            <h4 class="font-lora fw-bold text-cva-brown mb-2"><?= $fav['nombre_prod'] ?></h4>
                            <p class="small text-muted mb-4 line-clamp-2"><?= esc($fav['descripcion']) ?></p>

                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <?php if ($env_cart_enabled): ?>
                                        <span class="price-tag-fav">$<?= number_format($fav['precio_vta'], 0, ',', '.') ?></span>
                                    <?php else: ?>
                                        <span class="price-tag-fav text-muted" style="font-size: 0.8rem;">Consultar precio</span>
                                    <?php endif; ?>
                                    <a href="<?= base_url('producto/detalle/' . $fav['producto_id']) ?>" class="btn btn-sm btn-outline-brown rounded-pill px-3 fw-bold">DETALLE</a>
                                </div>
                                <?php if ($env_cart_enabled): ?>
                                    <?php if (session()->get('logged_in')): ?>
                                        <form action="<?= base_url('carrito/add') ?>" method="post" class="w-100">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="id_producto" value="<?= esc($fav['producto_id']) ?>">
                                            <input type="hidden" name="precio_vta" value="<?= esc($fav['precio_vta']) ?>">
                                            <input type="hidden" name="nombre_prod" value="<?= esc($fav['nombre_prod']) ?>">
                                            <input type="hidden" name="imagen" value="<?= esc($fav['imagen']) ?>">
                                            <button type="submit" class="btn btn-brown-solid w-100 py-2.5 rounded-pill fw-bold btn-add-cart-fav">
                                                <i class="bi bi-cart-plus me-2"></i> Agregar al Carrito
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <a href="<?= base_url('login') ?>" class="btn btn-outline-secondary w-100 py-2.5 rounded-pill small fw-bold">Iniciá sesión para comprar</a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php
                                    $whatsapp_num = $env_whatsapp;
                                    $mensaje = urlencode("Hola! Estoy interesado en: " . $fav['nombre_prod'] . ". Me podrías dar más información y precio?");
                                    $url_whatsapp = "https://wa.me/{$whatsapp_num}?text={$mensaje}";
                                    ?>
                                    <a href="<?= $url_whatsapp ?>" target="_blank" class="btn btn-whatsapp-artisan w-100 py-2.5 rounded-pill fw-bold">
                                        <i class="bi bi-whatsapp me-2"></i> Consultar por WhatsApp
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="<?= base_url('assets/js/pages/favorites.js?v=1.0') ?>"></script>
<?= $this->endSection() ?>