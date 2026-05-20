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
<script>
    let csrfToken = '<?= csrf_hash() ?>';

    function toggleFav(event, id, btn) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        fetch('<?= base_url('favoritos/toggle/') ?>' + id, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.csrf) csrfToken = data.csrf; // Refresh token

                const icon = btn.querySelector('i');
                if (data.status === 'added') {
                    btn.classList.add('active');
                    icon.classList.remove('bi-heart');
                    icon.classList.add('bi-heart-fill');
                } else if (data.status === 'removed') {
                    btn.classList.remove('active');
                    icon.classList.remove('bi-heart-fill');
                    icon.classList.add('bi-heart');
                } else if (data.status === 'error') {
                    window.location.href = '<?= base_url('login') ?>';
                }
            })
            .catch(err => console.error('Error:', err));
    }

    document.addEventListener('DOMContentLoaded', function() {
        const botones = document.querySelectorAll('.filtro-categoria');
        const productos = document.querySelectorAll('#lista-productos > div');

        botones.forEach(btn => {
            btn.addEventListener('click', () => {
                // Manejo de clase activa
                botones.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const categoria = btn.dataset.categoria.toLowerCase();
                const items = document.querySelectorAll('#lista-productos > div');

                items.forEach(prod => {
                    const catProd = prod.dataset.categorias.toLowerCase();
                    if (categoria === 'todos' || catProd === categoria) {
                        prod.style.display = 'block';
                    } else {
                        prod.style.display = 'none';
                    }
                });
            });
        });
    });
</script>
<?= $this->endSection() ?>