<?php
/**
 * Vista de Carga de Pedidos Manuales o Personalizados (Admin Add Custom Order Form)
 *
 * Muestra el formulario avanzado de administración interna para ingresar una orden de trabajo artesanal directa.
 * Se utiliza para registrar ventas hechas de forma física, solicitudes de WhatsApp, o encargos especiales que no
 * están preestablecidos en el catálogo público de la web.
 * Incorpora:
 * 1. Sección de Cliente: Búsqueda dinámica de usuarios registrados en el sistema, o ingreso de un nombre de referencia.
 * 2. Sección de Obra: Caja de texto para especificaciones detalladas (medidas, tipo de madera) y cargador de bocetos/fotos de referencia.
 * 3. Acuerdo Comercial: Entradas para la inversión total acordada y la seña inicial recibida.
 *
 * @var array $clientes Listado de usuarios registrados en el sistema para vincular a la orden (`UsuarioModel`).
 *                      Estructura esperada:
 *                      - 'id_usuario' (int): ID único del perfil.
 *                      - 'nombre' (string): Nombre del cliente.
 *                      - 'apellido' (string): Apellido del cliente.
 *                      - 'usuario' (string): Nombre de usuario/teléfono.
 *
 * Recursos Externos:
 * - Estilos: `assets/css/admin/admin-sales.css`
 * - Scripts: `assets/js/admin/sales.js` (Lógica de previsualización e interacciones de formularios).
 */
?>
<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('extra-css') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/admin-sales.css?v=1.0')?>">
<?= $this->endSection() ?>

<?= $this->section('breadcrumbs') ?>
    <li class="breadcrumb-item active text-uppercase small fw-bold" aria-current="page">NUEVO PEDIDO MANUAL</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Encabezado Estilo Artisan (Consistente con otras pestañas) -->
        <div class="row mb-5 align-items-center g-4">
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-3 gap-md-4">
                    <div class="dashboard-icon-main bg-brown text-gold shadow">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div>
                        <h1 class="display-6 display-md-5 fw-bold text-cva-brown mb-1">Pedido Manual</h1>
                        <p class="text-muted mb-0 small"><i class="bi bi-hammer text-gold me-1"></i> Registro de trabajos especiales y ventas directas.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 text-lg-end">
                <a href="<?= base_url('ventas-list') ?>" class="btn btn-admin-outline rounded-pill px-4 shadow-sm">
                    <i class="bi bi-arrow-left me-2"></i> VOLVER AL LISTADO
                </a>
            </div>
        </div>

        <!-- Card Principal (Uso de admin-card-v2 estándar) -->
        <div class="admin-card-v2 mb-5">
            <div class="admin-card-header-v2">
                <h6 class="mb-0 fw-bold text-brown"><i class="bi bi-journal-plus me-2 text-gold"></i> Formulario de Registro</h6>
                <span class="badge bg-gold-soft text-gold x-small px-3 border border-gold border-opacity-25">Ficha Interna</span>
            </div>
            
            <div class="admin-card-body-v2">
                <form action="<?= base_url('ventas/guardar-personalizado') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <!-- Sección: Cliente -->
                    <div class="mb-5">
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <div class="bg-gold-soft p-2 rounded-circle">
                                <i class="bi bi-person-badge text-gold fs-5"></i>
                            </div>
                            <h5 class="fw-bold text-brown mb-0">Información del Cliente</h5>
                        </div>
                        
                        <div class="bg-light p-3 rounded-4 mb-4 border-start border-4 border-info">
                            <div class="d-flex gap-3 align-items-start">
                                <i class="bi bi-info-circle-fill text-info mt-1"></i>
                                <p class="small mb-0 text-muted">
                                    Si el cliente tiene usuario, búscalo en la lista. Si es externo (WhatsApp/Local), completa el nombre de referencia.
                                </p>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-12">
                                <div class="p-4 bg-light rounded-4 border">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="admin-label">Usuario Registrado</label>
                                            <select name="usuario_id" class="form-select admin-control">
                                                <option value="">-- No registrado / Cliente Nuevo --</option>
                                                <?php foreach ($clientes as $cliente): ?>
                                                    <option value="<?= $cliente['id_usuario'] ?>">
                                                        <?= esc($cliente['nombre'] . ' ' . $cliente['apellido']) ?> (<?= esc($cliente['usuario']) ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="admin-label">Nombre de Referencia Completo</label>
                                            <input type="text" name="nombre_cliente" class="admin-control" 
                                                   required placeholder="Escribe el nombre y contacto del cliente...">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Detalles -->
                    <div class="mb-5">
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <div class="bg-gold-soft p-2 rounded-circle">
                                <i class="bi bi-tools text-gold fs-5"></i>
                            </div>
                            <h5 class="fw-bold text-brown mb-0">Especificaciones de la Obra</h5>
                        </div>

                        <div class="row g-4">
                            <div class="col-12">
                                <label class="admin-label">Descripción Técnica y Medidas (Abarca el detalle aquí)</label>
                                <textarea name="detalles_obra" class="admin-control" rows="10" 
                                          required placeholder="Describe maderas, herrajes, medidas exactas y terminación final..."></textarea>
                            </div>
                            <div class="col-12">
                                <label class="admin-label">Imagen o Boceto de Referencia (Opcional)</label>
                                <div class="admin-img-preview" style="min-height: 120px;" onclick="document.getElementById('ref_img').click()">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="bi bi-cloud-arrow-up display-6 text-gold opacity-50"></i>
                                        <div class="text-start">
                                            <p class="mb-0 fw-bold text-brown">Cargar referencia visual</p>
                                            <p class="text-muted x-small mb-0">Click para seleccionar archivo</p>
                                        </div>
                                    </div>
                                    <input type="file" name="imagen_referencia" id="ref_img" class="d-none" accept="image/*">
                                </div>
                                <div id="file-name" class="mt-2 small text-success fw-bold text-center"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Acuerdo -->
                    <div class="mb-5 pt-4 border-top">
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <div class="bg-gold-soft p-2 rounded-circle">
                                <i class="bi bi-cash-stack text-gold fs-5"></i>
                            </div>
                            <h5 class="fw-bold text-brown mb-0">Acuerdo Comercial</h5>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="admin-label">Inversión Total ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-2 border-end-0 fw-bold text-muted" style="border-radius: 1rem 0 0 1rem;">$</span>
                                    <input type="number" step="0.01" name="total_venta" class="form-control admin-control border-start-0" 
                                           style="border-radius: 0 1rem 1rem 0;" required placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="admin-label">Entrega Inicial / Seña ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-2 border-end-0 fw-bold text-muted" style="border-radius: 1rem 0 0 1rem;">$</span>
                                    <input type="number" step="0.01" name="monto_sena" class="form-control admin-control border-start-0" 
                                           style="border-radius: 0 1rem 1rem 0;" value="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center pt-4 border-top">
                        <button type="submit" class="btn btn-admin-primary px-5 py-3 shadow-gold fs-5">
                            <i class="bi bi-check2-circle me-2"></i> REGISTRAR PEDIDO EN EL SISTEMA
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('extra-js') ?>
<script src="<?= base_url('assets/js/admin/sales.js?v=1.0') ?>"></script>
<?= $this->endSection() ?>
