<?= $this->extend('emails/base_email') ?>

<?= $this->section('content') ?>
    <h2>¡Gracias por tu pedido, <?= esc($nombre) ?>!</h2>
    <p>Hemos recibido tu pedido <strong>#<?= $pedidoId ?></strong> y nuestro equipo en el taller ya fue notificado.</p>
    
    <p><strong>Detalle de tu compra:</strong></p>
    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cant.</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($articulos as $item): ?>
            <tr>
                <td><?= esc($item['nombre']) ?></td>
                <td><?= $item['cantidad'] ?></td>
                <td>$ <?= number_format($item['subtotal'], 2, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <p style="text-align: right; font-size: 18px;"><strong>Total: $ <?= number_format($total, 2, ',', '.') ?></strong></p>
    
    <p>En breve nos pondremos en contacto contigo si requerimos más detalles constructivos o para coordinar pagos y entregas.</p>
    <p>Puedes seguir el progreso de tu obra desde tu cuenta:</p>
    <div style="text-align: center;">
        <a href="<?= base_url('usuario/compras') ?>" class="btn">Ver mis compras</a>
    </div>
<?= $this->endSection() ?>
