<?php
/**
 * Vista de Plantilla de Inicio (Home Plantilla)
 *
 * Página de bienvenida principal de la plataforma CVA Muebles.
 * Se extiende de `layout/main` y orquesta la carga de las secciones destacadas de la home:
 * - `section-carrusel.php` (Carrusel con llamado a la acción)
 * - `section-catalogo.php` (Muestra destacada de categorías y obras destacadas)
 */
?>
<?= $this->extend('layout/main') ?>

<?= $this->section('extra-css') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/carrusel.css?v=4.0')?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/section-artisan.css?v=5.0')?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/catalogo.css?v=2.0')?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <!-- Section Carrusel -->
    <?= view('front/home/section-carrusel') ?>

    <!-- Section Catalogo -->
    <?= view('front/home/section-catalogo') ?>    
<?= $this->endSection() ?>
