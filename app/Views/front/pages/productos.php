<?php
/**
 * Vista de Catálogo General Público (Public General Catalog Page)
 *
 * Muestra la grilla interactiva completa de muebles activos en el catálogo de CVA Muebles.
 * Incorpora:
 * 1. Pestañas de Filtro: Generadas dinámicamente según las categorías activas en la base de datos.
 * 2. Grilla de Fichas: Reutiliza el componente `components/product_card` inyectando datos de producto y favoritos.
 *
 * @var array $categorias Listado de categorías disponibles de `CategoriaModel`.
 * @var array $productos Listado de productos para exposición de `ProductoModel`.
 * @var array|null $user_favs Listado de IDs de favoritos del cliente logueado.
 */
?>
<?= $this->extend('layout/main') ?>

<?= $this->section('extra-css') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/pages/productos.css?v=11.0') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section id="productos" class="contenedor-productos">
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

        <!-- Pestañas de Filtro -->
        <div class="filter-container mb-5 animate-fade-in">
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
<script src="<?= base_url('assets/js/pages/products.js?v=1.0') ?>"></script>
<?= $this->endSection() ?>