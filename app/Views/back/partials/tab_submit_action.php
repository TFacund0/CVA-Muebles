<?php
/**
 * Carga la variante de submitAction() consciente de la pestaña activa
 * (Activos/Archivados), que sobreescribe la función genérica de
 * admin-layout.js. Compartido por las pantallas CRUD con ese patrón
 * de pestañas (usuarios, productos) para no duplicar la misma función.
 */
?>
<script src="<?= base_url('assets/js/admin/admin-tab-submit-action.js?v=1.0') ?>"></script>
