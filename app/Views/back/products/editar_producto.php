<?php
/**
 * Vista de Edición de Producto (Admin Edit Product Form)
 *
 * Muestra el formulario avanzado premium para la modificación completa de una obra del catálogo.
 * Se divide en:
 * 1. Columna de Imagen Principal: Permite previsualizar en tiempo real el archivo cargado y reemplazarlo.
 * 2. Formulario de Datos: Edición del nombre comercial, asignación de categoría dinámica y descripción.
 * 3. Valores de Margen: Ajuste de costos del taller y precio de venta final al público.
 * 4. Galería Adicional: Carga múltiple de fotografías secundarias para visualización de detalles,
 *    con herramientas de eliminación asíncrona/directa.
 *
 * @var array $producto Ficha detallada del mueble cargado del catálogo.
 *                      Estructura esperada:
 *                      - 'id_producto' (int): ID único.
 *                      - 'nombre_prod' (string): Nombre del producto.
 *                      - 'categoria_id' (int): ID de categoría asociada.
 *                      - 'precio' (float): Costo de producción.
 *                      - 'precio_vta' (float): Precio de venta final.
 *                      - 'imagen' (string): Nombre de la foto de portada.
 *                      - 'descripcion' (string): Detalles constructivos.
 *                      - 'galeria' (array): Listado de fotos secundarias con 'id' e 'imagen'.
 * @var array $categorias Listado de categorías disponibles de `CategoriaModel` (id_categoria, descripcion).
 *
 * Recursos Externos:
 * - Estilos: `assets/css/admin/admin-products.css`
 * - Scripts: `assets/js/admin/products.js` (Manejo de previsualizaciones y cálculos financieros).
 */
?>
<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('extra-css') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/admin-products.css?v=1.1')?>">
<?= $this->endSection() ?>

<?= $this->section('breadcrumbs') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/crud-productos') ?>" class="text-decoration-none text-muted">CATÁLOGO</a></li>
    <li class="breadcrumb-item active small fw-bold text-gold" aria-current="page">EDITAR PRODUCTO</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Encabezado Estilo Artisan -->
<div class="row mb-5 align-items-center">
    <div class="col-md-8">
        <div class="d-flex align-items-center gap-4">
            <div class="dashboard-icon-main bg-brown text-gold shadow">
                <i class="bi bi-pencil-square"></i>
            </div>
            <div>
                <h1 class="display-5 fw-bold text-cva-brown mb-1">Modificar Producto</h1>
                <p class="text-muted mb-0">Actualiza los detalles técnicos, precios y stock del catálogo artesanal.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 text-md-end">
        <div class="badge bg-gold-soft text-gold px-4 py-2 rounded-pill fs-6 fw-bold border border-gold shadow-sm">
            ID PRODUCTO: #<?= $producto['id_producto'] ?>
        </div>
    </div>
</div>

<?php if(session()->getFlashData('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 shadow-sm border-0 bg-success text-white" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashData('success'); ?>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form method="POST" action="<?= base_url('/modificar-producto/' . $producto['id_producto']) ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    
    <div class="row g-4">
        <!-- COLUMNA IZQUIERDA: IMAGEN PRINCIPAL -->
        <div class="col-lg-4">
            <div class="admin-card-v2 p-0 overflow-hidden shadow-sm border-0 mb-4 sticky-top" style="top: 20px;">
                <div class="bg-brown p-3 text-center">
                    <h6 class="mb-0 fw-bold text-gold small text-uppercase tracking-wider">Imagen de Portada</h6>
                </div>
                <div class="p-4">
                    <div class="product-preview-container mb-4 position-relative bg-light rounded-4 border overflow-hidden" style="height: 350px;">
                        <img src="<?= base_url('assets/uploads/' . $producto['imagen']) ?>" 
                             class="img-fluid w-100 h-100" 
                             style="object-fit: contain;" 
                             id="main-preview">
                        <div class="position-absolute bottom-0 end-0 p-3">
                            <span class="badge bg-gold px-3 py-2 rounded-pill shadow-sm">Principal</span>
                        </div>
                    </div>

                    <div class="upload-zone p-4 border-2 border-dashed rounded-4 text-center bg-light-gold position-relative">
                        <i class="bi bi-camera fs-2 text-gold mb-2 d-block"></i>
                        <span class="d-block small fw-bold text-muted mb-2 text-uppercase">Cambiar Fotografía</span>
                        <input type="file" name="imagen" class="stretched-link opacity-0" accept="image/*" onchange="previewImage(event)">
                        <p class="x-small text-muted mb-0">Se recomienda formato cuadrado.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: DATOS Y GALERÍA -->
        <div class="col-lg-8">
            <div class="admin-card-v2 p-4 p-md-5 shadow-sm border-0">
                
                <!-- SECCIÓN 1: IDENTIDAD -->
                <div class="mb-5">
                    <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
                        <div class="p-2 bg-gold-soft rounded-3 text-gold">
                            <i class="bi bi-info-circle-fill fs-4"></i>
                        </div>
                        <h5 class="fw-bold text-cva-brown mb-0">Información del Producto</h5>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-md-8">
                            <label class="x-small fw-bold text-muted text-uppercase mb-2">Nombre Comercial</label>
                            <input type="text" name="nombre_producto" class="form-control admin-control py-3 fs-5 fw-bold text-cva-brown" 
                                   value="<?= esc($producto['nombre_prod']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="x-small fw-bold text-muted text-uppercase mb-2">Categoría</label>
                            <select name="categoria_id" class="form-select admin-control py-3 fw-bold" required>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria['id_categoria'] ?>" <?= $producto['categoria_id'] == $categoria['id_categoria'] ? 'selected' : '' ?>>
                                        <?= esc($categoria['descripcion']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 2: VALORES Y STOCK -->
                <div class="mb-5">
                    <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
                        <div class="p-2 bg-success-soft rounded-3 text-success">
                            <i class="bi bi-currency-dollar fs-4"></i>
                        </div>
                        <h5 class="fw-bold text-cva-brown mb-0">Finanzas y Existencias</h5>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6 col-12">
                            <label class="x-small fw-bold text-muted text-uppercase mb-2">Costo</label>
                            <input type="number" step="0.01" name="precio" class="form-control admin-control py-2 fw-bold" 
                                   value="<?= esc($producto['precio']) ?>" required>
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="x-small fw-bold text-muted text-uppercase mb-2">Venta</label>
                            <input type="number" step="0.01" name="precio-vta" class="form-control admin-control py-2 text-gold" 
                                   value="<?= esc($producto['precio_vta']) ?>" required>
                        </div>
                        <input type="hidden" name="stock" value="<?= esc($producto['stock'] ?? '999') ?>">
                        <input type="hidden" name="stock-min" value="<?= esc($producto['stock_min'] ?? '0') ?>">
                    </div>
                </div>

                <!-- SECCIÓN 3: GALERÍA ADICIONAL -->
                <div class="mb-5 border-top pt-5">
                    <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
                        <div class="p-2 bg-warning-soft rounded-3 text-warning">
                            <i class="bi bi-images fs-4"></i>
                        </div>
                        <h5 class="fw-bold text-cva-brown mb-0">Galería de Imágenes Secundarias</h5>
                    </div>
                    
                    <div class="upload-zone-secondary p-4 border-2 border-dashed rounded-4 text-center bg-light-gold mb-4">
                        <i class="bi bi-plus-circle text-gold fs-3 mb-2 d-block"></i>
                        <span class="d-block small fw-bold text-muted mb-2 text-uppercase">Agregar fotos adicionales</span>
                        <input type="file" name="fotos_galeria[]" class="form-control admin-control" multiple>
                        <p class="x-small text-muted mt-2 mb-0">Elegí varias fotos para que el cliente vea el mueble de todos lados.</p>
                    </div>

                    <div class="row g-2">
                        <?php if(!empty($producto['galeria'])): ?>
                            <?php foreach($producto['galeria'] as $img): ?>
                                <div class="col-4 col-md-3 col-lg-2">
                                    <div class="gallery-item-admin rounded-3 overflow-hidden shadow-sm position-relative border">
                                        <img src="<?= base_url('assets/uploads/' . $img['imagen']) ?>" class="img-fluid w-100" style="height: 100px; object-fit: cover;">
                                        <div class="gallery-actions position-absolute top-0 end-0 p-1">
                                            <a href="<?= base_url('/admin/productos/eliminar-foto/' . $img['id']) ?>" 
                                               class="btn btn-danger btn-sm rounded-circle p-1" 
                                               onclick="return confirm('¿Eliminar esta foto?')">
                                                <i class="bi bi-x-lg" style="font-size: 10px;"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- SECCIÓN 4: DESCRIPCIÓN -->
                <div class="mb-5 border-top pt-5">
                    <label class="x-small fw-bold text-muted text-uppercase mb-2">Ficha Técnica y Descripción</label>
                    <textarea name="descripcion" class="form-control admin-control p-4" rows="6" 
                              style="line-height: 1.6;"><?= esc($producto['descripcion']) ?></textarea>
                </div>

                <!-- BOTONES DE ACCIÓN -->
                <div class="row g-3 pt-4 border-top">
                    <div class="col-md-8">
                        <button type="submit" class="btn btn-admin-gold w-100 py-3 fw-bold fs-5 shadow-sm rounded-4">
                            <i class="bi bi-cloud-check me-2"></i> ACTUALIZAR TODO
                        </button>
                    </div>
                    <div class="col-md-4">
                        <a href="<?= base_url('/crud-productos') ?>" class="btn btn-outline-dark w-100 py-3 fw-bold rounded-4 text-uppercase small">
                            CANCELAR
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>



<?= $this->endSection() ?>

<?= $this->section('extra-js') ?>
<script src="<?= base_url('assets/js/admin/products.js?v=1.0') ?>"></script>
<?= $this->endSection() ?>
