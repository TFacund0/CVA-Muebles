<?= $this->extend('layout/admin_layout') ?>



<?= $this->section('breadcrumbs') ?>
<li class="breadcrumb-item active small fw-bold text-gold" aria-current="page">GESTIÓN DE CONSULTAS</li>
<?= $this->endSection() ?>


<?= $this->section('content') ?>
<!-- Encabezado Estilo Artisan -->
<div class="row mb-5 align-items-center g-4">
    <div class="col-lg-8">
        <div class="d-flex align-items-center gap-3 gap-md-4">
            <div class="dashboard-icon-main bg-brown text-gold shadow">
                <i class="bi bi-chat-left-text"></i>
            </div>
            <div>
                <h1 class="display-6 display-md-5 fw-bold text-cva-brown mb-1 d-flex flex-wrap align-items-center gap-2">
                    <span>Gestión de Consultas</span>
                </h1>
                <p class="text-muted mb-0 small"><i class="bi bi-envelope-paper text-gold me-1"></i> Gestión de mensajes y presupuestos.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4 text-lg-end">
        <div id="inquiry-header-status-badge" class="badge bg-gold-soft text-gold px-4 py-2 rounded-pill fs-6 fw-bold border border-gold shadow-sm w-sm-100 justify-content-center">
            <i class="bi bi-hourglass-split me-2"></i><?= $counts['activos'] ?> PENDIENTES
        </div>
    </div>
</div>

<!-- Mensajes modularizados -->
<?= view('components/alert_message') ?>

<!-- KPIs de Consultas -->
<div class="row g-3 g-md-4 mb-5">
    <div class="col-6 col-md-4">
        <div class="admin-card-v2 p-3 p-md-4 border-start border-4 border-info h-100 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="d-block x-small text-uppercase fw-bold text-muted mb-1">Mes <?= $nombreMes ?></span>
                    <h4 class="fw-bold text-cva-brown mb-0"><?= $counts['mensuales'] ?></h4>
                </div>
                <div class="bg-light text-info p-2 p-md-3 rounded-circle d-none d-sm-block">
                    <i class="bi bi-calendar3 fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="admin-card-v2 p-3 p-md-4 border-start border-4 h-100 shadow-sm" style="border-color: var(--cva-gold) !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="d-block x-small text-uppercase fw-bold text-muted mb-1">Presupuestos</span>
                    <h4 class="fw-bold text-gold mb-0"><?= $counts['presupuestos'] ?></h4>
                </div>
                <div class="bg-gold-soft text-gold p-2 p-md-3 rounded-circle d-none d-sm-block">
                    <i class="bi bi-file-earmark-spreadsheet fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="admin-card-v2 p-3 p-md-4 border-start border-4 border-success h-100 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="d-block x-small text-uppercase fw-bold text-muted mb-1">Total Histórico</span>
                    <h4 class="fw-bold text-success mb-0"><?= $counts['total'] ?> Mensajes</h4>
                </div>
                <div class="bg-light text-success p-2 p-md-3 rounded-circle d-none d-sm-block">
                    <i class="bi bi-archive fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Selector de Pestañas Premium (Segmented Tabs) -->
<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-center justify-content-md-start">
            <ul class="nav nav-pills custom-segmented-tabs p-1 bg-light rounded-4 shadow-sm border" id="consultasTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-4 px-4 py-2-5 fw-bold text-uppercase x-small d-flex align-items-center gap-2"
                        id="pendientes-tab"
                        type="button"
                        role="tab"
                        aria-selected="true">
                        <i class="bi bi-envelope-open text-gold"></i>
                        <span>Pendientes</span>
                        <span class="badge bg-gold text-brown rounded-pill x-small fw-bold px-2 py-1 shadow-sm"><?= $counts['activos'] ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-4 px-4 py-2-5 fw-bold text-uppercase x-small d-flex align-items-center gap-2"
                        id="contestados-tab"
                        type="button"
                        role="tab"
                        aria-selected="false">
                        <i class="bi bi-check2-all text-gold"></i>
                        <span>Contestados<span class="d-none d-sm-inline"> / Archivados</span></span>
                        <span class="badge bg-secondary-soft text-muted rounded-pill x-small fw-bold px-2 py-1"><?= $counts['total'] - $counts['activos'] ?></span>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Filtros Inteligentes -->
<div class="admin-card-v2 mb-4 border-0 shadow-sm overflow-hidden">
    <div class="bg-light p-3 border-bottom d-flex align-items-center justify-content-between" style="min-height: 52px;">
        <h6 class="mb-0 fw-bold text-cva-brown"><i class="bi bi-filter-right me-2 text-gold"></i> Filtros de Búsqueda</h6>
        <div id="filter-status" class="x-small fw-bold text-success" style="opacity: 0; transition: opacity 0.2s ease;">
            <span class="spinner-grow spinner-grow-sm me-1"></span> FILTRANDO...
        </div>
    </div>
    <div class="p-4">
        <div class="row g-3 align-items-end">
            <div class="col-lg-7 col-md-8 col-12">
                <label class="x-small fw-bold text-muted text-uppercase mb-2">Buscador en tiempo real</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 border-2" style="border-radius: 1rem 0 0 1rem;">
                        <i class="bi bi-search text-gold"></i>
                    </span>
                    <input type="text" id="input-search" class="form-control border-start-0 border-2 py-2"
                        style="border-radius: 0 1rem 1rem 0;"
                        placeholder="Buscar...">
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-6">
                <label class="x-small fw-bold text-muted text-uppercase mb-2">Asunto</label>
                <select id="select-asunto" class="form-select border-2 py-2 rounded-3 x-small fw-bold text-uppercase">
                    <option value="ALL">TODOS</option>
                    <option value="Consulta general">Gral.</option>
                    <option value="Solicitud de presupuesto">Presup.</option>
                    <option value="Estado de mi pedido">Pedido</option>
                    <option value="Consulta sobre garantía">Garantía</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>
            <div class="col-lg-1 col-6">
                <button type="button" id="btn-reset" class="btn btn-light border py-2 w-100 rounded-3 shadow-sm x-small fw-bold text-uppercase">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Listado de Mensajes -->
<div class="admin-card-v2 border-0 shadow-sm overflow-hidden mb-5">
    <div class="bg-light p-3 border-bottom d-flex align-items-center justify-content-between" style="min-height: 52px;">
        <h6 class="mb-0 fw-bold text-cva-brown d-flex align-items-center gap-2" id="inquiry-list-title">
            <span class="status-dot status-dot-pulse-gold"></span>
            <i class="bi bi-envelope-open me-1 text-gold"></i> Listado de Consultas Pendientes
        </h6>
    </div>
    <div class="table-responsive-stack table-layout-fixed">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 py-3 text-uppercase x-small fw-bold text-muted" style="width: 25%;">Interesado</th>
                    <th class="py-3 text-uppercase x-small fw-bold text-muted d-none d-lg-table-cell" style="width: 10%;">Fecha</th>
                    <th class="py-3 text-uppercase x-small fw-bold text-muted d-none d-lg-table-cell" style="width: 20%;">Contacto</th>
                    <th class="py-3 text-uppercase x-small fw-bold text-muted" style="width: 30%;">Mensaje</th>
                    <th class="pe-4 py-3 text-uppercase x-small fw-bold text-muted text-center" style="width: 15%;">Acciones</th>
                </tr>
            </thead>
            <tbody id="inquiry-table-body">
                <?php if (!empty($consultas)): ?>
                    <?php foreach ($consultas as $index => $consulta): ?>
                        <tr class="inquiry-row"
                            data-search="<?= $consulta['search_data'] ?>"
                            data-asunto="<?= esc($consulta['asunto']) ?>"
                            data-activo="<?= $consulta['activo'] ?>">
                            <td class="ps-4" data-label="INTERESADO">
                                <div class="d-flex align-items-center gap-3 py-1 inquiry-info-wrapper">
                                    <div class="position-relative">
                                        <div class="avatar-premium bg-brown text-gold rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm">
                                            <?= strtoupper(substr($consulta['nombre'], 0, 1)) ?><?= strtoupper(substr($consulta['apellido'], 0, 1)) ?>
                                        </div>
                                        <span class="position-absolute top-0 start-0 badge rounded-pill bg-dark shadow-sm d-lg-none" style="transform: translate(-30%, -30%); font-size: 0.6rem; border: 1px solid var(--cva-gold);">#<?= $consulta['id_consulta'] ?></span>
                                    </div>
                                    <div class="inquiry-text-details">
                                        <div class="fw-bold text-cva-brown"><?= esc($consulta['nombre']) ?> <?= esc($consulta['apellido']) ?></div>
                                        <div class="d-flex gap-2 align-items-center mt-1">
                                            <span class="badge bg-light text-muted border d-none d-md-inline-block" style="font-size: 0.65rem;">ID: #<?= $consulta['id_consulta'] ?></span>
                                            <span class="badge bg-gold-soft text-gold border border-gold border-opacity-25 x-small" style="font-size: 0.6rem;"><?= esc($consulta['asunto']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-lg-table-cell" data-label="FECHA">
                                <div class="fw-bold text-cva-brown small"><?= date('d/m/Y', strtotime($consulta['fecha'])) ?></div>
                            </td>
                            <td class="d-none d-lg-table-cell" data-label="CONTACTO">
                                <div class="small text-end text-md-start"><i class="bi bi-envelope me-1 text-gold"></i> <?= esc($consulta['email']) ?></div>
                                <div class="x-small fw-bold text-muted text-end text-md-start"><i class="bi bi-phone me-1"></i> <?= esc($consulta['telefono']) ?></div>
                            </td>

                            <td data-label="MENSAJE" class="message-cell">
                                <div class="message-preview text-truncate small text-muted mb-1"><?= esc($consulta['descripcion']) ?></div>
                                <div class="message-action">
                                    <a href="#" class="text-gold x-small fw-bold text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalConsulta<?= $index ?>">
                                        LEER MÁS <i class="bi bi-chevron-right"></i>
                                    </a>
                                </div>
                            </td>
                            <td class="pe-4 text-center" data-label="ACCIONES">
                                <div class="d-flex justify-content-center gap-2">
                                    <?php
                                    $num = preg_replace('/[^0-9]/', '', $consulta['telefono']);
                                    $msg = urlencode("Hola " . $consulta['nombre'] . "! Soy César de CVA Muebles, respondo a tu consulta sobre: " . $consulta['asunto']);
                                    $url_wa = "https://wa.me/" . (str_starts_with($num, '54') ? $num : "54" . $num) . "?text=" . $msg;
                                    ?>
                                    <a href="<?= $url_wa ?>" target="_blank" class="btn btn-action-premium text-success border-success border-opacity-25 shadow-sm" title="WhatsApp">
                                        <i class="bi bi-whatsapp"></i>
                                    </a>

                                    <form action="<?= base_url('ventas/nuevo-personalizado') ?>" method="get" class="m-0">
                                        <input type="hidden" name="nombre" value="<?= esc($consulta['nombre'] . ' ' . $consulta['apellido']) ?>">
                                        <input type="hidden" name="detalle" value="<?= esc($consulta['asunto'] . ': ' . $consulta['descripcion']) ?>">
                                        <button type="submit" class="btn btn-action-premium text-gold border-gold border-opacity-25 shadow-sm" title="Convertir a Pedido">
                                            <i class="bi bi-hammer"></i>
                                        </button>
                                    </form>

                                    <?php if ($consulta['activo'] == 'SI'): ?>
                                        <button type="button" onclick="submitAction('<?= base_url('consultas/eliminar/' . $consulta['id_consulta']) ?>?vista=SI', '¿Marcar como contestada/archivada esta consulta?')"
                                            class="btn btn-action-premium text-gold border-gold border-opacity-25 shadow-sm" title="Archivar/Contestado">
                                            <i class="bi bi-check2-circle"></i>
                                        </button>
                                    <?php else: ?>
                                        <button type="button" onclick="mostrarModalEliminarConsulta('<?= $consulta['id_consulta'] ?>', '<?= esc($consulta['nombre'] . ' ' . $consulta['apellido']) ?>', '<?= esc($consulta['asunto']) ?>', '<?= date('d/m/Y', strtotime($consulta['fecha'])) ?>')"
                                            class="btn btn-action-premium text-danger border-danger border-opacity-25 shadow-sm" title="Eliminar Permanente">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>

                        <!-- MODAL DE LECTURA PREMIUM -->
                        <div class="modal fade" id="modalConsulta<?= $index ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 rounded-5 shadow-lg overflow-hidden">
                                    <div class="modal-header bg-brown text-gold p-4">
                                        <div>
                                            <h5 class="modal-title fw-bold mb-1">Detalle del Mensaje</h5>
                                            <span class="x-small opacity-75">ID: <?= $consulta['id_consulta'] ?></span>
                                        </div>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4 bg-light">
                                        <div class="d-flex align-items-center gap-3 mb-4">
                                            <div class="avatar-md bg-brown text-gold rounded-circle d-flex align-items-center justify-content-center fw-bold fs-4 shadow-sm" style="width: 50px; height: 50px;">
                                                <?= substr($consulta['nombre'], 0, 1) ?>
                                            </div>
                                            <div>
                                                <h5 class="fw-bold text-cva-brown mb-0"><?= esc($consulta['nombre']) ?> <?= esc($consulta['apellido']) ?></h5>
                                                <div class="x-small text-muted"><?= date('d M Y, H:i', strtotime($consulta['fecha'])) ?> hs</div>
                                            </div>
                                        </div>
                                        <div class="p-4 bg-white border border-gold border-opacity-25 rounded-4 mb-4 shadow-sm">
                                            <div class="x-small fw-bold text-gold text-uppercase mb-2 border-bottom pb-2">
                                                <i class="bi bi-tag-fill me-1"></i> Asunto: <?= esc($consulta['asunto']) ?>
                                            </div>
                                            <p class="mb-0 text-dark lh-lg" style="font-size: 0.95rem;"><?= nl2br(esc($consulta['descripcion'])) ?></p>
                                        </div>
                                        <div class="d-flex gap-4 text-muted x-small justify-content-center py-2 bg-light rounded-3">
                                            <div><i class="bi bi-envelope-fill text-gold me-1"></i> <?= esc($consulta['email']) ?></div>
                                            <div><i class="bi bi-telephone-fill text-gold me-1"></i> <?= esc($consulta['telefono']) ?></div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light p-3 border-0">
                                        <button type="button" class="btn btn-outline-dark rounded-pill px-4 x-small fw-bold" data-bs-dismiss="modal">CERRAR</button>
                                        <a href="<?= $url_wa ?>" target="_blank" class="btn btn-admin-primary rounded-pill px-4 x-small fw-bold">
                                            <i class="bi bi-whatsapp me-2"></i> RESPONDER POR WHATSAPP
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr id="empty-row">
                        <td colspan="5" class="text-center py-5">
                            <div class="py-4">
                                <i class="bi bi-chat-dots fs-1 text-muted opacity-25"></i>
                                <p class="text-muted mt-3 fw-bold">No hay consultas registradas en el sistema.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
 
                <!-- Fila de Sin Resultados JS -->
                <tr id="no-results-row" style="display: none;">
                    <td colspan="5" class="text-center py-5">
                        <i class="bi bi-search display-4 text-muted opacity-25"></i>
                        <p class="text-muted mt-3">No hay consultas que coincidan con la búsqueda.</p>
                    </td>
                </tr>
 
                <!-- Fila de Vista Vacía JS -->
                <tr id="empty-view-row" style="display: none;">
                    <td colspan="5" class="text-center py-5">
                        <div class="py-4">
                            <i id="empty-view-icon" class="bi bi-chat-dots display-4 text-muted opacity-25"></i>
                            <p id="empty-view-text" class="text-muted mt-3 fw-bold">No hay consultas en esta bandeja.</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL DE CONFIRMACIÓN DE ELIMINACIÓN PERMANENTE CON VALIDACIÓN (PREMIUM) -->
<div class="modal fade" id="modalConfirmarEliminarConsulta" tabindex="-1" aria-labelledby="modalConfirmarEliminarConsultaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-5 shadow-lg overflow-hidden">
            <div class="modal-header bg-danger text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; font-size: 1.5rem;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalConfirmarEliminarConsultaLabel">Advertencia de Seguridad</h5>
                        <span class="x-small opacity-75">Eliminación Física de Mensaje</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-eliminar-permanente" action="" method="POST" class="m-0">
                <?= csrf_field() ?>
                <div class="modal-body p-4 bg-light">
                    <!-- Caja de advertencia -->
                    <div class="p-3 bg-white border border-danger border-opacity-25 rounded-4 mb-4 shadow-sm">
                        <h6 class="fw-bold text-danger mb-2"><i class="bi bi-info-circle-fill me-1"></i> ¿Por qué no es recomendable borrar consultas?</h6>
                        <p class="small text-muted mb-2">
                            Las consultas de contacto representan la <strong>memoria comercial</strong> de CVA Muebles. Al borrarlas físicamente:
                        </p>
                        <ul class="small text-muted ps-3 mb-0" style="font-size: 0.85rem; line-height: 1.5;">
                            <li class="mb-1"><strong>Pérdida de Historial:</strong> Si el cliente vuelve a escribir, no sabrás qué solicitó anteriormente.</li>
                            <li class="mb-1"><strong>Pérdida de Presupuestos:</strong> Desaparece cualquier detalle de diseño o medidas que te haya enviado.</li>
                            <li class="mb-1"><strong>Irreversible:</strong> La información se eliminará permanentemente de la base de datos sin respaldo.</li>
                        </ul>
                    </div>

                    <!-- Datos de la consulta a borrar -->
                    <div class="p-3 bg-white border border-secondary border-opacity-10 rounded-4 mb-4 shadow-sm">
                        <div class="x-small text-uppercase fw-bold text-muted mb-2">Mensaje seleccionado:</div>
                        <div class="fw-bold text-cva-brown" id="del-inquiry-name" style="font-size: 0.95rem;">-</div>
                        <div class="small text-muted" id="del-inquiry-details">-</div>
                    </div>

                    <!-- Preguntas para validar y concientizar -->
                    <div class="mb-4">
                        <label class="small fw-bold text-dark mb-2 d-block">1. Justificación obligatoria para la eliminación:</label>
                        <div class="bg-white p-3 border rounded-4 shadow-sm">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="razon_eliminacion" id="reason-spam" value="Es Spam o Publicidad no deseada" required>
                                <label class="form-check-label small text-muted cursor-pointer" for="reason-spam">
                                    Es Spam / Mensaje de publicidad no deseado
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="razon_eliminacion" id="reason-test" value="Mensaje de prueba o Duplicado">
                                <label class="form-check-label small text-muted cursor-pointer" for="reason-test">
                                    Mensaje de prueba / Duplicado
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="razon_eliminacion" id="reason-privacy" value="El cliente solicitó el borrado de sus datos (Protección de Datos)">
                                <label class="form-check-label small text-muted cursor-pointer" for="reason-privacy">
                                    El cliente solicitó el borrado de sus datos (Ley de Protección de Datos)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="razon_eliminacion" id="reason-other" value="Otro motivo administrativo">
                                <label class="form-check-label small text-muted cursor-pointer" for="reason-other">
                                    Otro motivo administrativo
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Input de Confirmación mediante texto -->
                    <div>
                        <label for="confirm-delete-word" class="small fw-bold text-dark mb-2 d-block">
                            2. Para confirmar los riesgos, escribe la palabra <span class="text-danger fw-extrabold">ELIMINAR</span>:
                        </label>
                        <input type="text" id="confirm-delete-word" class="form-control text-center fw-bold text-uppercase border-2"
                            style="letter-spacing: 2px; border-radius: 0.75rem;"
                            placeholder="Escribe ELIMINAR aquí" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer bg-light p-3 border-0 gap-2">
                    <button type="button" class="btn btn-outline-dark rounded-pill px-4 x-small fw-bold" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" id="btn-submit-delete-permanente" class="btn btn-danger rounded-pill px-4 x-small fw-bold shadow" disabled>
                        <i class="bi bi-trash-fill me-2"></i> CONFIRMAR Y ELIMINAR
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<?= $this->endSection() ?>

<?= $this->section('extra-js') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputSearch = document.getElementById('input-search');
        const selectAsunto = document.getElementById('select-asunto');
        const pendientesTab = document.getElementById('pendientes-tab');
        const contestadosTab = document.getElementById('contestados-tab');
        const listTitle = document.getElementById('inquiry-list-title');
        const rows = document.querySelectorAll('.inquiry-row');
        const noResults = document.getElementById('no-results-row');
        const emptyViewRow = document.getElementById('empty-view-row');
        const filterStatus = document.getElementById('filter-status');
        const btnReset = document.getElementById('btn-reset');

        // Detectar vista inicial desde query parameter para mantener el foco
        const urlParams = new URLSearchParams(window.location.search);
        let currentView = urlParams.get('vista') === 'NO' ? 'NO' : 'SI';

        function filterInquiries() {
            const searchTerm = inputSearch.value.toLowerCase();
            const asuntoFilter = selectAsunto.value;
            let visibleCount = 0;
            let totalInCurrentView = 0;

            filterStatus.style.opacity = '1';

            rows.forEach(row => {
                const searchData = row.getAttribute('data-search');
                const asunto = row.getAttribute('data-asunto');
                const activo = row.getAttribute('data-activo');

                const isCorrectView = (activo === currentView);
                const matchesSearch = searchData.includes(searchTerm);
                const matchesAsunto = (asuntoFilter === 'ALL' || asunto === asuntoFilter);

                if (isCorrectView) {
                    totalInCurrentView++;
                    if (matchesSearch && matchesAsunto) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                } else {
                    row.style.display = 'none';
                }
            });

            if (noResults) {
                noResults.style.display = (visibleCount === 0 && totalInCurrentView > 0) ? '' : 'none';
            }

            if (emptyViewRow) {
                if (totalInCurrentView === 0) {
                    emptyViewRow.style.display = '';
                    const emptyText = document.getElementById('empty-view-text');
                    const emptyIcon = document.getElementById('empty-view-icon');
                    if (currentView === 'SI') {
                        emptyText.innerText = "¡Excelente! No tienes consultas pendientes de respuesta.";
                        emptyIcon.className = "bi bi-check2-all display-4 text-success opacity-75";
                    } else {
                        emptyText.innerText = "No tienes consultas archivadas o contestadas en el historial.";
                        emptyIcon.className = "bi bi-archive display-4 text-muted opacity-50";
                    }
                } else {
                    emptyViewRow.style.display = 'none';
                }
            }

            setTimeout(() => {
                filterStatus.style.opacity = '0';
            }, 300);
        }

        function switchView(view) {
            currentView = view;

            const activeBadge = document.getElementById('inquiry-active-badge');
            const headerStatusBadge = document.getElementById('inquiry-header-status-badge');

            if (currentView === 'SI') {
                pendientesTab.classList.add('active');
                pendientesTab.setAttribute('aria-selected', 'true');
                contestadosTab.classList.remove('active');
                contestadosTab.setAttribute('aria-selected', 'false');

                if (activeBadge) {
                    activeBadge.className = "badge bg-gold text-brown fs-6 px-3 py-1 rounded-pill border border-gold shadow-sm";
                    activeBadge.innerHTML = '<i class="bi bi-envelope-open-fill me-1"></i> PENDIENTES';
                }
                if (headerStatusBadge) {
                    headerStatusBadge.className = "badge bg-gold-soft text-gold px-4 py-2 rounded-pill fs-6 fw-bold border border-gold shadow-sm w-sm-100 justify-content-center";
                    headerStatusBadge.innerHTML = '<i class="bi bi-hourglass-split me-2"></i><?= $counts['activos'] ?> PENDIENTES';
                }
                if (listTitle) {
                    listTitle.innerHTML = '<span class="status-dot status-dot-pulse-gold"></span> <i class="bi bi-envelope-open me-1 text-gold"></i> Listado de Consultas Pendientes';
                }
            } else {
                contestadosTab.classList.add('active');
                contestadosTab.setAttribute('aria-selected', 'true');
                pendientesTab.classList.remove('active');
                pendientesTab.setAttribute('aria-selected', 'false');

                if (activeBadge) {
                    activeBadge.className = "badge bg-brown text-gold fs-6 px-3 py-1 rounded-pill border border-gold shadow-sm";
                    activeBadge.innerHTML = '<i class="bi bi-check2-all me-1"></i> CONTESTADAS / ARCHIVADAS';
                }
                if (headerStatusBadge) {
                    headerStatusBadge.className = "badge bg-light text-muted px-4 py-2 rounded-pill fs-6 fw-bold border shadow-sm w-sm-100 justify-content-center";
                    headerStatusBadge.innerHTML = '<i class="bi bi-archive-fill me-2"></i><?= $counts['total'] - $counts['activos'] ?> CONTESTADOS';
                }
                if (listTitle) {
                    listTitle.innerHTML = '<span class="status-dot status-dot-green"></span> <i class="bi bi-check2-all me-1 text-gold"></i> Listado de Consultas Contestadas / Archivadas';
                }
            }

            filterInquiries();
        }

        if (pendientesTab) {
            pendientesTab.addEventListener('click', () => switchView('SI'));
        }
        if (contestadosTab) {
            contestadosTab.addEventListener('click', () => switchView('NO'));
        }

        inputSearch.addEventListener('input', filterInquiries);
        selectAsunto.addEventListener('change', filterInquiries);

        btnReset.addEventListener('click', function() {
            inputSearch.value = '';
            selectAsunto.value = 'ALL';
            filterInquiries();
        });

        // Modal de Eliminación Permanente
        const modalDeleteEl = document.getElementById('modalConfirmarEliminarConsulta');
        const modalDelete = modalDeleteEl ? new bootstrap.Modal(modalDeleteEl) : null;
        const formDelete = document.getElementById('form-eliminar-permanente');
        const delName = document.getElementById('del-inquiry-name');
        const delDetails = document.getElementById('del-inquiry-details');
        const inputConfirmWord = document.getElementById('confirm-delete-word');
        const btnSubmitDelete = document.getElementById('btn-submit-delete-permanente');
        const radioReasons = document.querySelectorAll('input[name="razon_eliminacion"]');

        window.mostrarModalEliminarConsulta = function(id, nombre, asunto, fecha) {
            if (!modalDelete) return;
            formDelete.action = "<?= base_url('consultas/eliminar-permanente') ?>/" + id + "?vista=NO";
            delName.textContent = nombre;
            delDetails.textContent = asunto + ' (' + fecha + ')';

            // Resetear inputs del modal
            inputConfirmWord.value = '';
            btnSubmitDelete.disabled = true;
            radioReasons.forEach(radio => radio.checked = false);

            modalDelete.show();
        };

        function validarConfirmacionEliminar() {
            if (!btnSubmitDelete) return;
            let reasonSelected = false;
            radioReasons.forEach(radio => {
                if (radio.checked) reasonSelected = true;
            });

            const textMatches = (inputConfirmWord.value.trim().toUpperCase() === 'ELIMINAR');

            btnSubmitDelete.disabled = !(reasonSelected && textMatches);
        }

        if (inputConfirmWord) {
            inputConfirmWord.addEventListener('input', validarConfirmacionEliminar);
        }
        radioReasons.forEach(radio => radio.addEventListener('change', validarConfirmacionEliminar));

        // Inicializar con la vista correcta
        switchView(currentView);
    });
</script>
<?= $this->endSection() ?>