<?= $this->extend('layout/main') ?>

<?= $this->section('extra-css') ?>
<style>
    .fav-header {
        background: linear-gradient(rgba(45, 27, 25, 0.9), rgba(45, 27, 25, 0.7)), url('<?= base_url('assets/img/ui/backgrounds/artisan_fav_bg.png') ?>');
        background-size: cover; background-position: center;
        padding: 100px 0; color: white; margin-bottom: 50px;
    }
    .fav-item {
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    .fav-card {
        border: none; border-radius: 2rem; overflow: hidden;
        transition: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
        background: white; box-shadow: 0 15px 35px rgba(62, 39, 35, 0.05);
        position: relative; height: 100%;
        display: flex;
        flex-direction: column;
    }
    .fav-card:hover { transform: translateY(-10px); box-shadow: 0 30px 60px rgba(62, 39, 35, 0.12); }
    .fav-img-wrapper { height: 280px; overflow: hidden; position: relative; }
    .fav-img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.8s; }
    .fav-card:hover .fav-img-wrapper img { transform: scale(1.1); }
    
    .remove-fav-btn {
        position: absolute; top: 15px; right: 15px;
        width: 40px; height: 40px; border-radius: 50%;
        background: rgba(255,255,255,0.9); border: none;
        color: #e74c3c; display: flex; align-items: center; justify-content: center;
        transition: all 0.3s; z-index: 10; backdrop-filter: blur(5px);
    }
    .remove-fav-btn:hover { background: #e74c3c; color: white; transform: rotate(90deg); }
    
    .price-tag-fav {
        background: var(--cva-gold); color: white;
        padding: 5px 15px; border-radius: 10px;
        font-weight: 800; font-size: 0.9rem;
    }
    .empty-state-fav { padding: 100px 0; }

    /* Search & Filter Styles */
    .btn-filter-artisan {
        background: white;
        color: var(--cva-brown);
        border: 1.5px solid #eeebe6;
        border-radius: 50px;
        padding: 0.6rem 1.25rem;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        box-shadow: 0 2px 8px rgba(0,0,0,0.01);
    }

    .btn-filter-artisan:hover {
        border-color: var(--cva-gold);
        color: var(--cva-gold);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(184, 134, 11, 0.08);
    }

    .btn-filter-artisan.active {
        background: var(--cva-brown);
        color: var(--cva-gold);
        border-color: var(--cva-brown);
        box-shadow: 0 8px 20px rgba(45, 27, 25, 0.15);
    }

    .focus-within-gold:focus-within {
        border-color: var(--cva-gold) !important;
        box-shadow: 0 8px 25px rgba(184, 134, 11, 0.08) !important;
    }

    .clear-search-btn {
        color: #a08d7c;
        transition: color 0.2s ease;
        background: none;
        border: none;
    }

    .clear-search-btn:hover {
        color: var(--cva-brown);
    }

    /* Card additions */
    .btn-add-cart-fav {
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    .btn-add-cart-fav:hover {
        background-color: var(--cva-gold) !important;
        color: white !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(184, 134, 11, 0.2);
    }
    
    .empty-search-state {
        text-align: center;
        padding: 5rem 2rem;
        background: white;
        border-radius: 2rem;
        box-shadow: 0 15px 35px rgba(62, 39, 35, 0.03);
        border: 1px dashed rgba(184, 134, 11, 0.2);
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;  
        overflow: hidden;
    }
</style>
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
                            <button onclick="toggleFav(<?= $fav['producto_id'] ?>, this)" class="remove-fav-btn shadow-sm" title="Quitar de favoritos">
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
                                    <span class="price-tag-fav">$<?= number_format($fav['precio_vta'], 0, ',', '.') ?></span>
                                    <a href="<?= base_url('producto/detalle/' . $fav['producto_id']) ?>" class="btn btn-sm btn-outline-brown rounded-pill px-3 fw-bold">DETALLE</a>
                                </div>
                                <?php if (env('SHOPPING_CART_ENABLED')): ?>
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
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function toggleFav(id, btn) {
    if(!confirm('¿Quitar este mueble de tus favoritos?')) return;
    
    fetch('<?= base_url('favoritos/toggle/') ?>' + id, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'removed') {
                const item = btn.closest('.fav-item');
                item.style.opacity = '0';
                item.style.transform = 'scale(0.8)';
                setTimeout(() => {
                    item.remove();
                    if (document.querySelectorAll('.fav-item').length === 0) {
                        location.reload();
                    }
                }, 400);
            }
        })
        .catch(err => console.error('Error:', err));
}

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-favs');
    const clearBtn = document.getElementById('clear-search');
    const filterBtns = document.querySelectorAll('.btn-filter-artisan');
    const cards = document.querySelectorAll('.fav-item');
    const noResults = document.getElementById('no-results-fav');
    
    let currentSearch = '';
    let currentFilter = 'todos';

    function filterFavorites() {
        let visibleCount = 0;
        
        cards.forEach(card => {
            const nombre = card.dataset.nombre.toLowerCase();
            const category = card.dataset.categorias.toLowerCase();
            
            const matchesSearch = nombre.includes(currentSearch);
            const matchesFilter = currentFilter === 'todos' || category === currentFilter;
            
            if (matchesSearch && matchesFilter) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        if (visibleCount === 0) {
            noResults.classList.remove('d-none');
        } else {
            noResults.classList.add('d-none');
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            currentSearch = this.value.toLowerCase().trim();
            if (currentSearch.length > 0) {
                clearBtn.classList.remove('d-none');
            } else {
                clearBtn.classList.add('d-none');
            }
            filterFavorites();
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            currentSearch = '';
            this.classList.add('d-none');
            filterFavorites();
        });
    }

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            filterFavorites();
        });
    });
});
</script>
<?= $this->endSection() ?>
