<?= $this->extend('layout/main') ?>

<?= $this->section('extra-css') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/pages/productos.css?v=12.0') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section id="productos" class="contenedor-productos"
    data-csrf-token="<?= csrf_hash() ?>"
    data-favoritos-toggle-url="<?= base_url('favoritos/toggle/') ?>"
    data-login-url="<?= base_url('login') ?>">
    <!-- Cabecera Premium -->
    <div class="header-productos text-center shadow-sm">
        <div class="container">
            <h2 class="text-uppercase display-4">Catálogo de Productos</h2>
            <p>Descubrí piezas únicas diseñadas para durar toda la vida. Cada mueble cuenta una historia de tradición y madera seleccionada.</p>
            <div class="divider-artisan"></div>
        </div>
    </div>


    <!-- Contenedor de muebles (Mi diseño artisan) -->
    <div class="container-lg" id="catalogo-productos">

        <!-- Botón que abre el panel de filtros en móvil (oculto en escritorio,
             donde el filtro ya se ve completo como barra de pastillas). -->
        <button type="button" class="btn btn-filtro-trigger d-lg-none mb-4" data-bs-toggle="offcanvas" data-bs-target="#filtrosOffcanvas" aria-controls="filtrosOffcanvas">
            <i class="bi bi-sliders"></i> Filtrar: <span class="filtro-activo-label">Todos</span>
        </button>

        <!-- Pestañas de Filtro. offcanvas-lg: en escritorio se muestra como
             barra normal (Bootstrap la resetea a estatica en ese breakpoint);
             en móvil es un panel que sube desde abajo, disparado por el botón
             de arriba, en vez de una fila con scroll horizontal escondido. -->
        <div class="offcanvas offcanvas-bottom offcanvas-lg filter-container mb-5 animate-fade-in" tabindex="-1" id="filtrosOffcanvas" aria-labelledby="filtrosOffcanvasLabel">
            <div class="offcanvas-header d-lg-none">
                <h5 class="offcanvas-title" id="filtrosOffcanvasLabel">Filtrar por categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
            </div>
            <div class="offcanvas-body">
                <div class="filter-group d-flex">
                    <button type="button" class="btn filtro-categoria active" data-categoria="todos">Todos</button>
                    <?php
                    $descripciones_vistas = [];
                    foreach ($categorias as $cat):
                        $desc = trim(mb_strtolower($cat['descripcion']));
                        if (in_array($desc, $descripciones_vistas)) continue;
                        $descripciones_vistas[] = $desc;
                    ?>
                        <button type="button" class="btn filtro-categoria" data-categoria="<?= esc($cat['descripcion']) ?>">
                            <?= esc($cat['descripcion']) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="row g-3" id="lista-productos">
            <?php foreach ($productos as $row) { ?>
                <div class="col-lg-4 col-md-6 col-12 mb-4" data-categorias="<?= esc($row['categoria']) ?>">
                    <?= view('components/product_card', ['producto' => $row, 'user_favs' => $user_favs ?? []]) ?>
                </div>
            <?php } ?>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('extra-js') ?>
<script src="<?= base_url('assets/js/favoritos.js?v=1.0') ?>"></script>
<script src="<?= base_url('assets/js/pages/productos.js?v=1.0') ?>"></script>
<?= $this->endSection() ?>