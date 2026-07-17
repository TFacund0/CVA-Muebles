<?= $this->extend('emails/base_email') ?>

<?= $this->section('content') ?>
    <h2>Actualización de Pedido, <?= esc($nombre) ?></h2>
    <p>Te escribimos para avisarte que el estado de tu pedido <strong>#<?= $pedidoId ?></strong> ha sido actualizado por nuestro taller.</p>
    
    <div style="text-align: center; margin: 25px 0;">
        <span style="font-size: 16px;">Nuevo estado de tu obra:</span><br><br>
        <span class="badge badge-<?= strtolower($estado) ?>" style="font-size: 18px; padding: 10px 20px;">
            <?= str_replace('_', ' ', $estado) ?>
        </span>
    </div>
    
    <?php if ($estado === 'EN_PROCESO'): ?>
        <p>¡Manos a la obra! Hemos comenzado la etapa de fabricación de tu pedido. Estaremos cuidando cada detalle.</p>
    <?php elseif ($estado === 'TERMINADO'): ?>
        <p>¡Excelentes noticias! Tu pedido ha sido finalizado y está listo en nuestro taller. Pronto coordinaremos la entrega.</p>
    <?php elseif ($estado === 'ENTREGADO'): ?>
        <p>¡Esperamos que disfrutes tu mueble! Gracias por confiar en CVA Muebles.</p>
    <?php elseif ($estado === 'RECHAZADO'): ?>
        <p>Lamentablemente tu pedido ha sido rechazado o cancelado. Para más información, por favor ponte en contacto con nosotros.</p>
    <?php endif; ?>

    <p>Para ver los detalles completos o comprobantes, visita tu panel de cliente:</p>
    <div style="text-align: center;">
        <a href="<?= base_url('usuario/compras') ?>" class="btn">Ver estado en tiempo real</a>
    </div>
<?= $this->endSection() ?>
