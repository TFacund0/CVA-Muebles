<?php
/**
 * Vista de Gestión de Inventario de Obras (Admin Product ABM/CRUD Table)
 *
 * Muestra el panel maestro de control del catálogo de muebles y sus respectivas especificaciones.
 * Incorpora:
 * 1. KPIs Rápidos: Tarjetas resumidas de total de diseños, diseños activos e industrias activas.
 * 2. Segmentación de Estados: Pestañas interactivas para alternar entre catálogo "Activos" (Públicos) y "Archivados" (Borradores).
 * 3. Filtros Avanzados en Tiempo Real: Buscador por texto libre y selector por categoría.
 * 4. Listado Maestro Responsivo: Tabla detallada que expone la imagen del mueble, ID, descripción abreviada, precio e instructivos.
 * 5. Acciones de Control: Accesos para edición, archivado, restauración o eliminación permanente de piezas del catálogo.
 *
 * @var array $productos Listado completo de muebles del catálogo (`ProductoModel`).
 *                       Estructura de cada ítem:
 *                       - 'id_producto' (int): ID único de la pieza.
 *                       - 'nombre_prod' (string): Nombre comercial.
 *                       - 'descripcion' (string|null): Reseña constructiva.
 *                       - 'categoria' (string|null): Categoría de pertenencia.
 *                       - 'precio_vta' (float): Precio de venta al público.
 *                       - 'imagen' (string): Nombre del archivo de imagen.
 *                       - 'eliminado' (string): Indicador de archivado ('SI' o 'NO').
 * @var array $categorias Listado de categorías activas para el dropdown de filtros.
 * @var string $vista Vista seleccionada por defecto ('activos' o 'archivados').
 * @var array $counts Conteo consolidado de registros:
 *                    - 'activos' (int): Cantidad de muebles públicos.
 *                    - 'eliminados' (int): Cantidad de muebles archivados.
 *
 * Componentes Reutilizados:
 * - `components/alert_message.php` para mensajes informativos sobre acciones de guardado o borrado.
 *
 * Recursos Externos:
 * - Estilos: `assets/css/admin/admin-products.css`
 * - Scripts: `assets/js/admin/products-list.js` (Lógica de filtrado dinámico en tabla, pestañas segmentadas y confirmación).
 */
?>
<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('extra-css') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/admin-products.css?v=2.0')?>">
<?= $this->endSection() ?>

<?= $this->section('breadcrumbs') ?>
    <li class="breadcrumb-item active text-gold fw-bold" aria-current="page">GESTIÓN DE CATÁLOGO</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-5 align-items-center g-4">
    <div class="col-lg-7">
        <div class="d-flex align-items-center gap-3 gap-md-4">
            <div class="dashboard-icon-main bg-brown text-gold shadow">
                <i class="bi bi-box-seam"></i>
            </div>
            <div>
                <h1 class="display-6 display-md-5 fw-bold text-cva-brown mb-1">Inventario de Obras</h1>
                <p class="text-muted mb-0 small"><i class="bi bi-check2-circle text-success me-1"></i> Control total sobre las piezas disponibles.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-5 text-lg-end">
        <a href="<?= base_url('/alta-producto') ?>" class="btn-admin-primary rounded-pill px-4 py-3 shadow-gold w-sm-100">
            <i class="bi bi-plus-lg me-2"></i> NUEVA PIEZA
        </a>
    </div>
</div>

<!-- Mensajes modularizados -->
<?= view('components/alert_message') ?>

<!-- Resumen del Catálogo (KPIs Rápidos) -->
<div class="row g-3 g-md-4 mb-5">
    <?php 
        $total_items = count($productos);
        $categorias_activas = [];
        foreach($productos as $p) {
            if(!empty($p['categoria'])) $categorias_activas[$p['categoria']] = true;
        }
    ?>
    <div class="col-6 col-md-4">
        <div class="admin-card-v2 p-3 p-md-4 border-start border-4 border-info h-100 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="d-block x-small text-uppercase fw-bold text-muted mb-1">Catálogo Total</span>
                    <h4 class="fw-bold text-cva-brown mb-0"><?= $total_items ?> Diseños</h4>
                </div>
                <div class="bg-info-soft text-info p-2 p-md-3 rounded-circle d-none d-sm-block">
                    <i class="bi bi-collection fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="admin-card-v2 p-3 p-md-4 border-start border-4 h-100 shadow-sm" style="border-color: var(--cva-gold) !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="d-block x-small text-uppercase fw-bold text-muted mb-1">Diseños Activos</span>
                    <h4 class="fw-bold text-gold mb-0"><?= $counts['activos'] ?> en Tienda</h4>
                </div>
                <div class="bg-gold-soft text-gold p-2 p-md-3 rounded-circle d-none d-sm-block">
                    <i class="bi bi-hammer fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="admin-card-v2 p-3 p-md-4 border-start border-4 border-success h-100 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="d-block x-small text-uppercase fw-bold text-muted mb-1">Estilos Activos</span>
                    <h4 class="fw-bold text-success mb-0"><?= count($categorias_activas) ?> Categorías</h4>
                </div>
                <div class="bg-success-soft text-success p-2 p-md-3 rounded-circle d-none d-sm-block">
                    <i class="bi bi-tags fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Selector de Pestañas Premium (Segmented Tabs) -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-center justify-content-md-start">
            <ul class="nav nav-pills custom-segmented-tabs p-1 bg-light rounded-4 shadow-sm border" id="productosTab" role="tablist" data-vista="<?= esc($vista) ?>">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-4 px-4 py-2-5 fw-bold text-uppercase x-small d-flex align-items-center gap-2" 
                            id="activos-tab" 
                            type="button" 
                            role="tab" 
                            aria-selected="true">
                        <i class="bi bi-eye text-gold"></i>
                        <span>Activos<span class="d-none d-sm-inline"> (Públicos)</span></span>
                        <span class="badge bg-gold text-brown rounded-pill x-small fw-bold px-2 py-1 shadow-sm"><?= $counts['activos'] ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-4 px-4 py-2-5 fw-bold text-uppercase x-small d-flex align-items-center gap-2" 
                            id="archivados-tab" 
                            type="button" 
                            role="tab" 
                            aria-selected="false">
                        <i class="bi bi-archive text-gold"></i>
                        <span>Archivados<span class="d-none d-sm-inline"> (Borradores)</span></span>
                        <span class="badge bg-secondary-soft text-muted rounded-pill x-small fw-bold px-2 py-1"><?= $counts['eliminados'] ?></span>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Filtros Avanzados -->
<div class="admin-card-v2 mb-4 border-0 shadow-sm overflow-hidden">
    <div class="bg-light p-3 border-bottom d-flex align-items-center justify-content-between" style="min-height: 52px;">
        <h6 class="mb-0 fw-bold text-cva-brown"><i class="bi bi-filter-right me-2 text-gold"></i> Filtros de Búsqueda</h6>
        <div id="filter-status" class="x-small fw-bold text-success" style="opacity: 0; transition: opacity 0.2s ease;">
            <span class="spinner-grow spinner-grow-sm me-1"></span> FILTRANDO...
        </div>
    </div>
    <div class="p-4">
        <form id="filter-form" data-filter-mode="<?= esc($filterMode ?? 'client') ?>">
            <input type="hidden" name="vista" id="input-vista" value="<?= esc($vista) ?>">
            <div class="row g-3 align-items-end">
                <div class="col-md-7 col-12">
                    <label class="x-small fw-bold text-muted text-uppercase mb-2">Buscador en tiempo real</label>
                    <div class="input-group shadow-sm rounded-3 overflow-hidden border">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search text-gold"></i></span>
                        <input type="text" id="input-search" name="search" value="<?= esc($search ?? '') ?>" class="form-control border-0 py-2" placeholder="Buscar pieza...">
                    </div>
                </div>
                <div class="col-md-4 col-6">
                    <label class="x-small fw-bold text-muted text-uppercase mb-2">Categoría</label>
                    <select id="select-category" name="category" class="form-select border shadow-sm py-2 x-small fw-bold text-uppercase">
                        <option value="all" <?= ($category ?? 'all') === 'all' ? 'selected' : '' ?>>Todas</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= esc($cat['descripcion']) ?>" <?= ($category ?? '') === $cat['descripcion'] ? 'selected' : '' ?>><?= esc($cat['descripcion']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-1 col-6">
                    <button type="button" id="btn-reset" class="btn btn-light border py-2 w-100 rounded-3 shadow-sm">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Listado Maestro -->
<div class="admin-card-v2 border-0 shadow-sm overflow-hidden mb-5" id="product-grid">
    <div class="table-responsive-stack">
        <table class="table table-hover align-middle mb-0" id="products-table">
            <thead class="bg-brown text-white">
                <tr>
                    <th class="ps-4 py-4 text-gold x-small fw-bold border-0 text-uppercase tracking-widest" style="width: 55%;">PIEZA Y ESPECIFICACIONES</th>
                    <th class="py-4 text-gold x-small fw-bold border-0 text-uppercase tracking-widest">CATEGORÍA</th>
                    <th class="py-4 text-gold x-small fw-bold border-0 text-end text-uppercase tracking-widest">PRECIO VENTA</th>
                    <th class="pe-4 py-4 text-gold x-small fw-bold border-0 text-center text-uppercase tracking-widest">GESTIÓN</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <?php if(!empty($productos)): ?>
                    <?php foreach ($productos as $p): ?>
                        <tr class="product-row" 
                            data-name="<?= strtolower(esc($p['nombre_prod'])) ?>" 
                            data-category="<?= esc($p['categoria'] ?? 'Sin Categoría') ?>"
                            data-eliminado="<?= $p['eliminado'] ?>">
                            <td class="ps-4" data-label="PIEZA">
                                <div class="d-flex align-items-center gap-3 py-3 product-info-wrapper">
                                    <div class="flex-shrink-0 position-relative product-img-container">
                                        <img src="<?= base_url('assets/uploads/' . $p['imagen']) ?>" 
                                             class="rounded-3 shadow-sm border p-1 bg-white transition-transform" style="width: 80px; height: 80px; object-fit: cover;">
                                        <span class="position-absolute top-0 start-0 badge rounded-pill bg-dark shadow-sm d-md-none" style="transform: translate(-20%, -20%); font-size: 0.7rem; border: 1px solid var(--cva-gold); z-index: 1;">#<?= $p['id_producto'] ?></span>
                                    </div>
                                    <div class="product-text-details">
                                        <div class="h6 fw-bold text-cva-brown mb-1"><?= esc($p['nombre_prod']) ?></div>
                                        <div class="small text-muted mb-2 d-none d-md-block"><?= character_limiter(esc($p['descripcion'] ?? ''), 60) ?></div>
                                        <div class="d-flex gap-2">
                                            <span class="x-small fw-bold text-uppercase badge bg-light text-muted border px-2 py-1" style="font-size: 0.55rem;">ID: <?= $p['id_producto'] ?></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td data-label="CATEGORÍA">
                                <span class="badge bg-gold-soft text-gold px-3 py-2 rounded-pill small fw-bold border border-warning border-opacity-10">
                                    <i class="bi bi-tag-fill me-1"></i> <?= esc($p['categoria'] ?? 'Sin Categoría') ?>
                                </span>
                            </td>
                            <td class="text-end" data-label="PRECIO">
                                <div class="fw-bold text-cva-brown mb-0">$<?= number_format($p['precio_vta'], 2, ',', '.') ?></div>
                            </td>
                            <td class="text-center" data-label="GESTIÓN">
                                <div class="d-flex flex-column flex-sm-row flex-lg-column gap-2 align-items-center justify-content-center w-100">
                                    <a href="<?= base_url('/editar-producto/' . $p['id_producto']) ?>" 
                                       class="btn btn-action-premium text-primary border-primary border-opacity-25 w-100">
                                        <i class="bi bi-pencil-fill me-2"></i> EDITAR
                                    </a>
                                    
                                    <div class="w-100 btn-group-actions">
                                        <button type="button" onclick="submitAction('<?= base_url('/delete-producto/' . $p['id_producto']) ?>', '¿Confirmas archivar esta pieza del catálogo?')" 
                                                class="btn btn-action-premium text-danger border-danger border-opacity-25 w-100 action-archive <?= $p['eliminado'] == 'SI' ? 'd-none' : '' ?>">
                                            <i class="bi bi-archive-fill me-2"></i> ARCHIVAR
                                        </button>
                                        <button type="button" onclick="submitAction('<?= base_url('/activar-producto/' . $p['id_producto']) ?>', '¿Confirmas reactivar esta pieza?')" 
                                                class="btn btn-action-premium text-success border-success border-opacity-25 w-100 action-restore <?= $p['eliminado'] == 'NO' ? 'd-none' : '' ?>">
                                            <i class="bi bi-arrow-counterclockwise me-2"></i> RESTAURAR
                                        </button>
                                        <button type="button" onclick="submitAction('<?= base_url('/eliminar-producto-permanente/' . $p['id_producto']) ?>', '¿Confirmas eliminar PERMANENTEMENTE este mueble del catálogo? Esta acción no se puede deshacer y borrará toda la información asociada.')" 
                                                class="btn btn-action-premium text-danger border-danger border-opacity-25 w-100 mt-2 action-delete-permanent <?= $p['eliminado'] == 'NO' ? 'd-none' : '' ?>">
                                            <i class="bi bi-trash-fill me-2"></i> ELIMINAR
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <!-- Fila de "No hay resultados" (JS la controla en client mode, PHP en server mode) -->
                <tr id="no-results-row" style="display: <?= empty($productos) ? '' : 'none' ?>;">
                    <td colspan="4" class="text-center py-5 bg-light bg-opacity-50">
                        <div class="py-5 text-center">
                            <i class="bi bi-search display-1 text-muted opacity-25"></i>
                            <h4 class="text-muted mt-4">Sin coincidencias</h4>
                            <p class="text-muted small">No encontramos nada que coincida con tus filtros actuales.</p>
                            <button type="button" onclick="resetFilters()" class="btn btn-outline-gold rounded-pill px-4 mt-3 animate-pulse-gold">Limpiar búsqueda</button>
                        </div>
                    </td>
                </tr>

                <tr id="empty-active-row" style="display: none;">
                    <td colspan="4" class="text-center py-5 bg-light bg-opacity-50">
                        <div class="py-5 text-center">
                            <i class="bi bi-box-seam-fill display-1 text-muted opacity-25"></i>
                            <h4 class="text-muted mt-4">Catálogo Vacío</h4>
                            <p class="text-muted small">No hay piezas registradas en el catálogo activo.</p>
                            <a href="<?= base_url('/alta-producto') ?>" class="btn btn-admin-primary rounded-pill px-5 mt-3 shadow animate-pulse-gold">REGISTRAR PRIMERA OBRA</a>
                        </div>
                    </td>
                </tr>

                <tr id="empty-archive-row" style="display: none;">
                    <td colspan="4" class="text-center py-5 bg-light bg-opacity-50">
                        <div class="py-5 text-center">
                            <i class="bi bi-archive-fill display-1 text-muted opacity-25"></i>
                            <h4 class="text-muted mt-4">Archivo Vacío</h4>
                            <p class="text-muted small">No tienes piezas archivadas en este momento.</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <?php if ($pager): ?>
        <div class="card-footer bg-white border-top py-3" id="pagination-container">
            <?= $pager->links('default', 'admin_pager') ?>
        </div>
    <?php endif; ?>
</div>


<script src="<?= base_url('assets/js/admin/products-list.js?v=1.0') ?>"></script>
<?= $this->endSection() ?>
