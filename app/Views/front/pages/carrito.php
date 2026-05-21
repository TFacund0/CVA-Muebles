<?= $this->extend('layout/main') ?>

<?= $this->section('extra-css') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/pages/frontend-pages.css?v=1.0') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="carrito-wrapper py-5">
    <div class="container">
        <!-- Header -->
        <div class="mb-5 text-center text-lg-start">
            <span class="cart-header-badge">ESTUDIO DE CARPINTERÍA</span>
            <h1 class="cart-title-main">Mi Carrito</h1>
            <p class="text-muted fs-5">Piezas artesanales seleccionadas para tu espacio personal.</p>
            <div style="width: 100px; height: 4px; background: var(--cva-gold); border-radius: 2px;"></div>
        </div>



        <?php if (empty($cart)): ?>
            <div class="empty-cart-state bg-white rounded-5 p-5 shadow-sm text-center border">
                <div class="mb-4">
                    <i class="bi bi-cart-x display-1 text-gold opacity-25"></i>
                </div>
                <h3 class="fw-bold text-cva-brown">Tu selección está vacía</h3>
                <p class="text-muted mb-4">Explora nuestro catálogo para encontrar la pieza perfecta para tu hogar.</p>
                <a href="<?= base_url('productos') ?>" class="btn btn-brown rounded-pill px-5 py-3 fw-bold text-gold">
                    EXPLORAR CATÁLOGO
                </a>
            </div>
        <?php else: ?>
            <div class="row g-5">
                <!-- Listado de Productos -->
                <div class="col-lg-7">
                    <form id="cart-form" action="<?= base_url('carrito_comprar') ?>" method="post">
                        <?= csrf_field() ?>

                        <?php $gran_total = 0; ?>
                        <?php foreach ($cart as $item): ?>
                            <?php $gran_total += $item['price'] * $item['qty']; ?>
                            <div class="cart-item-card">
                                <div class="d-flex flex-column align-items-center">
                                    <input class="form-check-input item-checkbox cart-item-checkbox" type="checkbox" name="selected_items[]" value="<?= $item['rowid'] ?>" checked
                                        data-price="<?= $item['price'] ?>" data-qty="<?= $item['qty'] ?>" data-subtotal="<?= $item['subtotal'] ?>">
                                    <span class="x-small text-muted mt-2 fw-bold" style="font-size: 0.6rem;">PEDIR</span>
                                </div>
                                <div class="cart-img-wrapper">
                                    <img src="<?= base_url('assets/uploads/' . $item['imagen']) ?>" alt="<?= esc($item['name']) ?>">
                                </div>

                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="cart-item-label-badge mb-1 d-inline-block">Pieza Única</span>
                                            <h5 class="fw-bold text-cva-brown mb-1"><?= esc($item['name']) ?></h5>
                                            <p class="small text-muted mb-0">Colección Artesanal CVA</p>
                                        </div>
                                        <button type="button" onclick="submitAction('<?= base_url('carrito_elimina/' . $item['rowid']) ?>', '¿Quitar este producto del carrito?')"
                                            class="btn btn-trash-artisan" title="Quitar del carrito">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <button type="button" class="cart-qty-btn ajax-qty" data-url="<?= base_url('carrito_resta/' . $item['rowid']) ?>" data-rowid="<?= $item['rowid'] ?>">-</button>
                                            <span class="fw-bold text-cva-brown qty-display" id="qty-<?= $item['rowid'] ?>"><?= esc($item['qty']) ?></span>
                                            <button type="button" class="cart-qty-btn ajax-qty" data-url="<?= base_url('carrito_suma/' . $item['rowid']) ?>" data-rowid="<?= $item['rowid'] ?>">+</button>
                                        </div>

                                        <div class="text-end">
                                            <p class="small text-muted mb-0">Precio Unitario: $<?= number_format($item['price'], 0, ',', '.') ?></p>
                                            <p class="fw-bold text-cva-brown fs-5 mb-0" id="subtotal-<?= $item['rowid'] ?>">$<?= number_format($item['subtotal'], 0, ',', '.') ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="mt-4">
                            <a href="<?= base_url('productos') ?>" class="text-decoration-none text-brown fw-bold">
                                <i class="bi bi-arrow-left me-2"></i> SEGUIR VIENDO PRODUCTOS
                            </a>
                        </div>
                </div>

                <!-- Resumen de Compra -->
                <div class="col-lg-5">
                    <div class="cart-summary-card">
                        <h5 class="fw-bold text-cva-brown mb-4">Finalizar Pedido</h5>

                        <div class="specifications-box mb-4">
                            <label class="cart-section-label mb-2"><i class="bi bi-pencil-square me-2"></i>Tus especificaciones y detalles a medida:</label>
                            <textarea name="observaciones" class="form-control" rows="6" placeholder="Ej: Tipo de madera (Roble, Pino, etc.), dimensiones personalizadas, color de acabado (mate, satinado), o cualquier detalle de diseño especial..."></textarea>
                            <p class="x-small text-muted mt-2 mb-0 italic">Añade aquí las medidas, color o detalles técnicos que deseas para tu mueble artesanal.</p>
                        </div>

                        <hr class="summary-divider">
                        <span class="cart-section-label">Resumen de Orden</span>

                        <div class="cart-total-line">
                            <span>Subtotal Obras</span>
                            <span class="text-cva-brown fw-bold">$<?= number_format($gran_total, 0, ',', '.') ?></span>
                        </div>
                        <div class="cart-total-line">
                            <span>Logística / Envío</span>
                            <span class="text-gold fw-bold">A convenir</span>
                        </div>
                        <div class="cart-total-line">
                            <span>IVA / Impuestos</span>
                            <span class="text-muted">Bonificado</span>
                        </div>

                        <div class="cart-total-final">
                            <span class="fw-bold text-cva-brown">TOTAL</span>
                            <div class="amount">$<?= number_format($gran_total, 0, ',', '.') ?></div>
                        </div>

                        <div class="mt-5">
                            <button type="submit" class="btn btn-checkout-artisan w-100 mb-3">
                                <i class="bi bi-hammer me-2"></i> SOLICITAR PEDIDO
                            </button>
                            <a href="<?= base_url('borrar') ?>" class="btn btn-link text-danger w-100 text-decoration-none fw-bold x-small opacity-50 hover-opacity-100 transition-all">
                                VACIAR CARRITO
                            </a>
                        </div>
                        </form>

                        <div class="mt-4 p-3 bg-light rounded-4 text-center border-start border-gold border-3 shadow-sm">
                            <p class="x-small text-brown mb-0 fw-semibold">
                                <i class="bi bi-telephone-inbound-fill me-2 text-gold animate-pulse"></i>El dueño se comunicará con usted para establecer los detalles de su pedido y coordinar la entrega.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('extra-js') ?>
<script src="<?= base_url('assets/js/pages/cart.js?v=1.0') ?>"></script>
<?= $this->endSection() ?>