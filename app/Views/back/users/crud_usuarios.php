<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('extra-css') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/admin-users.css?v=4.0')?>">
<?= $this->endSection() ?>

<?= $this->section('breadcrumbs') ?>
<li class="breadcrumb-item active small fw-bold text-gold" aria-current="page">GESTIÓN DE USUARIOS</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Encabezado Estilo Artisan -->
<div class="row mb-5 align-items-center g-4">
    <div class="col-lg-7">
        <div class="d-flex align-items-center gap-3 gap-md-4">
            <div class="dashboard-icon-main bg-brown text-gold shadow">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <h1 class="display-6 display-md-5 fw-bold text-cva-brown mb-1">Usuarios</h1>
                <p class="text-muted mb-0 small"><i class="bi bi-shield-lock text-gold me-1"></i> Control de accesos y perfiles.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-5 text-lg-end">
        <a href="<?= base_url('registro') ?>" class="btn btn-admin-primary rounded-pill px-4 py-2 shadow-gold w-sm-100 justify-content-center">
            <i class="bi bi-person-plus-fill me-2"></i> NUEVO USUARIO
        </a>
    </div>
</div>

<!-- Mensajes modularizados -->
<?= view('components/alert_message') ?>

<!-- KPIs de Usuarios -->
<div class="row g-3 g-md-4 mb-5">
    <div class="col-6 col-md-4">
        <div class="admin-card-v2 p-3 p-md-4 border-start border-4 border-info h-100 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="d-block x-small text-uppercase fw-bold text-muted mb-1">Total</span>
                    <h4 class="fw-bold text-cva-brown mb-0"><?= $counts['total'] ?></h4>
                </div>
                <div class="bg-light text-info p-2 p-md-3 rounded-circle d-none d-sm-block">
                    <i class="bi bi-people fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="admin-card-v2 p-3 p-md-4 border-start border-4 h-100 shadow-sm admin-kpi-accent-gold">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="d-block x-small text-uppercase fw-bold text-muted mb-1">Admins</span>
                    <h4 class="fw-bold text-gold mb-0"><?= $counts['admins'] ?></h4>
                </div>
                <div class="bg-gold-soft text-gold p-2 p-md-3 rounded-circle d-none d-sm-block">
                    <i class="bi bi-person-badge-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="admin-card-v2 p-3 p-md-4 border-start border-4 border-success h-100 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="d-block x-small text-uppercase fw-bold text-muted mb-1">Activos</span>
                    <h4 class="fw-bold text-success mb-0"><?= $counts['activos'] ?> Cuentas</h4>
                </div>
                <div class="bg-light text-success p-2 p-md-3 rounded-circle d-none d-sm-block">
                    <i class="bi bi-person-check-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Selector de Pestañas Premium (Segmented Tabs) -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-center justify-content-md-start">
            <ul class="nav nav-pills custom-segmented-tabs p-1 bg-light rounded-4 shadow-sm border" id="usuariosTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-4 px-4 py-2-5 fw-bold text-uppercase x-small d-flex align-items-center gap-2"
                        id="activos-tab"
                        type="button"
                        role="tab"
                        aria-selected="true">
                        <i class="bi bi-person-check text-gold"></i>
                        <span>Activos</span>
                        <span class="badge bg-gold text-brown rounded-pill x-small fw-bold px-2 py-1 shadow-sm"><?= $counts['activos'] ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-4 px-4 py-2-5 fw-bold text-uppercase x-small d-flex align-items-center gap-2"
                        id="suspendidos-tab"
                        type="button"
                        role="tab"
                        aria-selected="false">
                        <i class="bi bi-person-dash text-gold"></i>
                        <span>Suspendidos</span>
                        <span class="badge bg-secondary-soft text-muted rounded-pill x-small fw-bold px-2 py-1"><?= $counts['suspendidos'] ?></span>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Filtros Inteligentes -->
<div class="admin-card-v2 mb-4 border-0 shadow-sm overflow-hidden">
    <div class="bg-light p-3 border-bottom d-flex align-items-center justify-content-between admin-filter-bar">
        <h6 class="mb-0 fw-bold text-cva-brown"><i class="bi bi-filter-right me-2 text-gold"></i> Filtros de Búsqueda</h6>
        <div id="filter-status" class="x-small fw-bold text-success admin-filter-status">
            <span class="spinner-grow spinner-grow-sm me-1"></span> FILTRANDO...
        </div>
    </div>
    <div class="p-4">
        <div class="row g-3 align-items-end">
            <div class="col-lg-7 col-md-8 col-12">
                <label class="x-small fw-bold text-muted text-uppercase mb-2">Buscador en tiempo real</label>
                <div class="input-group rounded-3 overflow-hidden border">
                    <span class="input-group-text bg-white border-0">
                        <i class="bi bi-search text-gold"></i>
                    </span>
                    <input type="text" id="input-search" class="form-control border-0 py-2"
                        placeholder="Nombre, email o usuario...">
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-10">
                <label class="x-small fw-bold text-muted text-uppercase mb-2">Rango / Perfil</label>
                <select id="select-perfil" class="form-select border shadow-sm py-2 x-small fw-bold text-uppercase select-perfil-h42">
                    <option value="all">Todos los Perfiles</option>
                    <option value="ADMIN">Administradores</option>
                    <option value="CLIENTE">Clientes</option>
                </select>
            </div>
            <div class="col-lg-1 col-md-12 col-2 text-end">
                <button type="button" id="btn-reset" class="btn btn-light border py-2 w-100 rounded-3 shadow-sm select-perfil-h42">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Usuarios -->
<div class="admin-card-v2 border-0 shadow-sm overflow-hidden mb-5">
    <div class="table-responsive-stack">
        <table class="table table-hover align-middle mb-0" id="users-table" data-initial-view="<?= esc($vista) ?>">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 py-3 text-uppercase x-small fw-bold text-muted">ID</th>
                    <th class="py-3 text-uppercase x-small fw-bold text-muted">Identidad</th>
                    <th class="py-3 text-uppercase x-small fw-bold text-muted">Acceso y Contacto</th>
                    <th class="py-3 text-uppercase x-small fw-bold text-muted text-center">Perfil</th>
                    <th class="pe-4 py-3 text-uppercase x-small fw-bold text-muted text-center">Acciones</th>
                </tr>
            </thead>
            <tbody id="user-table-body">
                <?php foreach ($usuarios as $u):
                    $isSelf = (session()->get('id_usuario') == $u['id_usuario']);
                    $u['baja'] = $u['deleted_at'] !== null ? 'SI' : 'NO';
                ?>
                    <tr class="user-row"
                        data-search="<?= strtolower(esc($u['nombre'] . ' ' . $u['apellido'] . ' ' . $u['email'] . ' ' . $u['usuario'])) ?>"
                        data-baja="<?= $u['baja'] ?>"
                        data-perfil="<?= $u['perfil_id'] == 1 ? 'ADMIN' : 'CLIENTE' ?>">
                        <td class="ps-4 d-none d-lg-table-cell" data-label="ID">
                            <span class="badge bg-light text-muted border">#<?= $u['id_usuario'] ?></span>
                        </td>
                        <td data-label="IDENTIDAD">
                            <div class="d-flex align-items-center gap-3 py-1 user-info-wrapper">
                                <div class="position-relative">
                                    <div class="avatar-premium bg-brown text-gold rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm">
                                        <?php if (!empty($u['imagen'])): ?>
                                            <img src="<?= imagen_url($u['imagen'], 'perfil') ?>" class="rounded-circle w-100 h-100 avatar-fit-cover">
                                        <?php else: ?>
                                            <?= substr($u['nombre'], 0, 1) ?><?= substr($u['apellido'], 0, 1) ?>
                                        <?php endif; ?>
                                    </div>
                                    <span class="position-absolute top-0 start-0 badge rounded-pill bg-dark shadow-sm d-md-none badge-corner-id">#<?= $u['id_usuario'] ?></span>
                                </div>
                                <div class="user-text-details">
                                    <div class="fw-bold text-cva-brown">
                                        <?= esc($u['nombre']) ?> <?= esc($u['apellido']) ?>
                                        <?php if ($u['baja'] == 'SI'): ?>
                                            <span class="badge bg-danger-soft text-danger x-small ms-1 badge-suspendido">SUSPENDIDO</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex gap-2 align-items-center">
                                        <span class="badge bg-light text-muted border d-none d-md-inline-block text-2xs">ID: #<?= $u['id_usuario'] ?></span>
                                        <?php if ($isSelf): ?>
                                            <span class="badge bg-gold-soft text-gold x-small fw-bold text-3xs">TU SESIÓN</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td data-label="ACCESO">
                            <div class="small fw-bold text-muted mb-1 user-access-info"><i class="bi bi-at text-gold me-1"></i><?= esc($u['usuario']) ?></div>
                            <div class="x-small text-muted user-access-info"><i class="bi bi-envelope me-1"></i><?= esc($u['email']) ?></div>
                        </td>
                        <td class="text-center" data-label="PERFIL">
                            <?php if ($u['perfil_id'] == 1): ?>
                                <span class="badge bg-brown text-gold px-3 py-2 rounded-pill x-small fw-bold border border-gold border-opacity-25 shadow-sm">
                                    <i class="bi bi-shield-fill-check me-1"></i> ADMIN
                                </span>
                            <?php else: ?>
                                <span class="badge bg-light text-muted px-3 py-2 rounded-pill x-small fw-bold border shadow-sm">
                                    <i class="bi bi-person me-1"></i> CLIENTE
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="pe-4 text-center" data-label="ACCIONES">
                            <div class="d-flex justify-content-center gap-2">
                                <?php if ($isSelf): ?>
                                    <a href="<?= base_url('/perfil') ?>" class="btn btn-action-premium text-gold border-gold border-opacity-25 shadow-sm">
                                        <i class="bi bi-person-gear"></i>
                                    </a>
                                <?php else: ?>
                                    <button type="button" class="btn btn-action-premium text-primary border-primary border-opacity-25 shadow-sm js-submit-action"
                                        data-url="<?= base_url('/editar-usuario/' . $u['id_usuario']) ?>"
                                        data-confirm-msg="¿Cambiar perfil de este usuario?"
                                        title="Cambiar Rango">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>

                                    <div class="action-toggle-container d-flex gap-2">
                                        <button type="button" class="btn btn-action-premium text-danger border-danger border-opacity-25 shadow-sm btn-archive js-submit-action <?= $u['baja'] == 'SI' ? 'd-none' : '' ?>"
                                            data-url="<?= base_url('/delete-usuario/' . $u['id_usuario']) ?>"
                                            data-confirm-msg="¿Confirmas suspender a este usuario?"
                                            title="Suspender Usuario">
                                            <i class="bi bi-person-x-fill"></i>
                                        </button>
                                        <button type="button" class="btn btn-action-premium text-success border-success border-opacity-25 shadow-sm btn-restore js-submit-action <?= $u['baja'] == 'NO' ? 'd-none' : '' ?>"
                                            data-url="<?= base_url('/activar-usuario/' . $u['id_usuario']) ?>"
                                            data-confirm-msg="¿Confirmas reactivar a este usuario?"
                                            title="Reactivar Usuario">
                                            <i class="bi bi-person-check-fill"></i>
                                        </button>
                                        <button type="button" class="btn btn-action-premium text-danger border-danger border-opacity-25 shadow-sm btn-delete-permanent js-submit-action <?= $u['baja'] == 'NO' ? 'd-none' : '' ?>"
                                            data-url="<?= base_url('/eliminar-usuario-permanente/' . $u['id_usuario']) ?>"
                                            data-confirm-msg="¿Confirmas eliminar PERMANENTEMENTE a este usuario? Esta acción es irreversible y borrará todos sus accesos."
                                            title="Eliminar Permanente">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <!-- Filas de Estados Vacíos -->
                <tr id="no-results-row" class="admin-hidden-row">
                    <td colspan="5" class="text-center py-5">
                        <i class="bi bi-search display-4 text-muted opacity-25"></i>
                        <p class="text-muted mt-3">No hay usuarios que coincidan con los filtros.</p>
                    </td>
                </tr>
                <tr id="empty-active-row" class="admin-hidden-row">
                    <td colspan="5" class="text-center py-5">
                        <i class="bi bi-people display-4 text-muted opacity-25"></i>
                        <p class="text-muted mt-3">No hay usuarios activos en el sistema.</p>
                    </td>
                </tr>
                <tr id="empty-suspended-row" class="admin-hidden-row">
                    <td colspan="5" class="text-center py-5">
                        <i class="bi bi-person-dash display-4 text-muted opacity-25"></i>
                        <p class="text-muted mt-3">No hay usuarios suspendidos.</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<?= $this->endSection() ?>

<?= $this->section('extra-js') ?>
<?= view('back/partials/tab_submit_action') ?>
<script src="<?= base_url('assets/js/admin-tab-filter.js?v=1.0') ?>"></script>
<script src="<?= base_url('assets/js/admin/admin-crud-usuarios.js?v=1.0') ?>"></script>
<?= $this->endSection() ?>