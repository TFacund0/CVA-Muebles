<?= $this->extend('layout/main') ?>

<?= $this->section('extra-css') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/carrusel.css?v=2.0')?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/section-artisan.css?v=5.0')?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/catalogo.css?v=1.0')?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <!-- Section Carrusel -->
    <?= view('front/home/section-carrusel') ?>

    <!-- Section Catalogo -->
    <?= view('front/home/section-catalogo') ?>    
<?= $this->endSection() ?>
