<?php
/**
 * Componente Tarjeta de Producto (Product Card Component)
 *
 * Renderiza la ficha individual de un mueble en el catálogo público o sección de favoritos.
 * Permite interactuar directamente agregando a favoritos (mediante JS/fetch), añadiendo
 * al carrito de compras, o derivando la consulta directamente a WhatsApp según la
 * disponibilidad de stock y habilitación del carrito.
 *
 * @var array $producto {
 *     @var int|string $id_producto Identificador único del mueble.
 *     @var string $nombre_prod Nombre comercial del mueble.
 *     @var string $imagen Archivo físico de la imagen principal.
 *     @var string|null $categoria Nombre de la categoría asociada (opcional).
 *     @var string $descripcion Descripción corta o especificación constructiva.
 *     @var float|int $precio_vta Precio de venta al público.
 *     @var int $stock Stock físico disponible.
 * }
 * @var array|null $user_favs Listado de IDs de productos marcados como favoritos por el usuario actual.
 * @var bool $env_cart_enabled Indica si la funcionalidad del carrito web está activa.
 * @var string $env_whatsapp Número de WhatsApp del taller para derivación directa de consultas.
 */
?>
<div class="product-card h-100 d-flex flex-column" data-aos="fade-up">
    <div class="img-wrapper position-relative">
        <img src="<?= base_url('assets/uploads/' . $producto['imagen']) ?>"
            class="card-img-top img-fluid"
            alt="<?= esc($producto['nombre_prod']) ?>"
            loading="lazy">

        <!-- Badge de Favorito -->
        <?php if (session()->get('logged_in')): ?>
            <?php $isFavorite = isset($user_favs) && in_array($producto['id_producto'], $user_favs); ?>
            <button class="btn-fav-artisan <?= $isFavorite ? 'active' : '' ?>"
                data-id="<?= $producto['id_producto'] ?>"
                aria-label="<?= $isFavorite ? 'Quitar favorito' : 'Agregar favorito' ?>">
                <i class="bi <?= $isFavorite ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
            </button>
        <?php endif; ?>

        <!-- Overlay de Categoría -->
        <div class="category-overlay">
            <span class="badge bg-blur"><?= esc($producto['categoria'] ?? 'Mueble') ?></span>
        </div>
    </div>

    <div class="card-body d-flex flex-column p-4">
        <h5 class="card-title font-lora fw-bold text-cva-brown mb-2"><?= esc($producto['nombre_prod']) ?></h5>
        <p class="card-text text-muted small mb-4 line-clamp-2"><?= esc($producto['descripcion']) ?></p>

        <div class="d-flex justify-content-between align-items-center mb-4 mt-auto">
            <?php if ($env_cart_enabled): ?>
                <span class="precio-tag">$<?= number_format($producto['precio_vta'], 0, ',', '.') ?></span>
            <?php else: ?>
                <span class="precio-tag text-muted" style="font-size: 0.85rem;">Consultar precio</span>
            <?php endif; ?>
            <span class="badge-stock bespoke"><i class="bi bi-hammer me-1"></i> Fabricación bajo pedido</span>
        </div>

        <div class="action-buttons mb-3">
            <a href="<?= base_url('producto/detalle/' . $producto['id_producto']) ?>" class="btn btn-artisan-gold w-100 py-3 fw-bold">
                VER DETALLES
            </a>
        </div>

        <div class="cart-actions mt-auto">
            <?php if ($env_cart_enabled): ?>
                <?php if (session()->get('logged_in')): ?>
                    <?php if ($producto['stock'] > 0): ?>
                        <form action="<?= base_url('carrito/add') ?>" method="post">
                            <?= csrf_field(); ?>
                            <input type="hidden" name="id_producto" value="<?= esc($producto['id_producto']) ?>">
                            <input type="hidden" name="precio_vta" value="<?= esc($producto['precio_vta']) ?>">
                            <input type="hidden" name="nombre_prod" value="<?= esc($producto['nombre_prod']) ?>">
                            <input type="hidden" name="imagen" value="<?= esc($producto['imagen']) ?>">
                            <button type="submit" class="btn btn-brown-solid w-100 py-3">
                                <i class="bi bi-cart-plus me-2"></i> Agregar al Carrito
                            </button>
                        </form>
                    <?php else: ?>
                        <?php
                        $whatsapp_num = $env_whatsapp;
                        $msg_stock = urlencode("Hola! Me interesa el mueble " . $producto['nombre_prod'] . " y me gustaría consultar para encargarlo.");
                        ?>
                        <a href="https://wa.me/<?= $whatsapp_num ?>?text=<?= $msg_stock ?>"
                            target="_blank" class="btn btn-outline-brown w-100 py-3">
                            <i class="bi bi-whatsapp me-2"></i> Consultar Fabricación
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?= base_url('login') ?>" class="btn btn-outline-secondary w-100 py-3 small">Iniciá sesión para comprar</a>
                <?php endif; ?>
            <?php else: ?>
                <?php
                $whatsapp_num = $env_whatsapp;
                $mensaje = urlencode("Hola! Estoy interesado en el producto: " . $producto['nombre_prod'] . ". Me podrías dar más información?");
                $url_whatsapp = "https://wa.me/{$whatsapp_num}?text={$mensaje}";
                ?>
                <a href="<?= $url_whatsapp ?>" target="_blank" class="btn btn-whatsapp-artisan w-100 py-3">
                    <i class="bi bi-whatsapp me-2"></i> Consultar por WhatsApp
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>