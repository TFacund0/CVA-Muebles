<?= $this->extend('layout/main') ?>

<?= $this->section('extra-css') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/pages/productos.css?v=9.5') ?>">
<style>
    .btn-fav-artisan {
        position: absolute;
        top: 15px;
        right: 15px;
        width: 40px;
        height: 40px;
        background: white !important;
        border: none !important;
        border-radius: 50% !important;
        color: var(--cva-brown) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15) !important;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
        z-index: 10 !important;
        cursor: pointer !important;
        padding: 0 !important;
    }
    .btn-fav-artisan:hover {
        transform: scale(1.15) !important;
        color: #e74c3c !important;
        box-shadow: 0 6px 15px rgba(0,0,0,0.2) !important;
    }
    .btn-fav-artisan.active {
        color: #e74c3c !important;
    }
    .btn-fav-artisan i {
        font-size: 1.2rem !important;
        line-height: 1 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    @media (max-width: 991.98px) {
        .btn-fav-artisan {
            width: 34px !important;
            height: 34px !important;
            top: 12px !important;
            right: 12px !important;
        }
        .btn-fav-artisan i {
            font-size: 1rem !important;
        }
    }
</style>
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
    function toggleFav(event, id, btn) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        fetch('<?= base_url('favoritos/toggle/') ?>' + id, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                }
            })
            .then(response => response.json())
            .then(data => {
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