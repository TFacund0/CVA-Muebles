<?= $this->extend('emails/base_email') ?>

<?= $this->section('content') ?>
    <h2>¡Hola <?= esc($nombre) ?>!</h2>
    <p>Te damos una cálida bienvenida a <strong>CVA Muebles</strong>. Nos alegra mucho tenerte en nuestra plataforma.</p>
    <p>A partir de ahora podrás explorar nuestro catálogo de muebles a medida, solicitar presupuestos personalizados y hacer seguimiento en tiempo real de la fabricación de tus pedidos.</p>
    <p>Si tienes alguna duda o consulta, no dudes en contactarnos.</p>
    <div style="text-align: center;">
        <a href="<?= base_url() ?>" class="btn">Visitar la tienda</a>
    </div>
<?= $this->endSection() ?>
