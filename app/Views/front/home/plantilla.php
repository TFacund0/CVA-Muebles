<?= $this->extend('layout/main') ?>

<?= $this->section('extra-css') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/carrusel.css?v=5.0')?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/catalogo.css?v=3.1')?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div id="home-page">
    <!-- Section Carrusel -->
    <?= view('front/home/section-carrusel') ?>

    <!-- Section Catalogo -->
    <?= view('front/home/section-catalogo', ['destacados' => $destacados ?? []]) ?>
</div>
<?= $this->endSection() ?>
