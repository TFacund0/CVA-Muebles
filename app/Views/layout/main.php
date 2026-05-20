<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title><?= $title ?? 'CVA Muebles' ?></title>
    <meta name="description" content="<?= $meta_description ?? 'Carpintería artesanal en Mantilla, Corrientes. Muebles de algarrobo y maderas nobles diseñados para durar toda la vida.' ?>">
    <meta name="keywords" content="muebles, carpinteria, artesanal, algarrobo, corrientes, mantilla, a medida">
    <meta name="author" content="CVA Muebles">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Lora:wght@400;700&display=swap" rel="stylesheet">

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css')?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Design System & Global Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/base/global.css?v=3.0')?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/layout/main-layout.css?v=7.0')?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/components/floating-alert.css?v=2.0')?>">


    <!-- Page Specific CSS Section -->
    <?= $this->renderSection('extra-css') ?>

    <script src="<?= base_url('assets/vendor/bootstrap/bootstrap.bundle.min.js')?>" ></script>
</head>
<body>
    <!-- Barra de Navegacion -->
    <?= view('partials/navbar') ?>

    <main>
        <?php if (isset($content)): ?>
            <?= $content ?>
        <?php else: ?>
            <?= $this->renderSection('content') ?>
        <?php endif; ?>
    </main>
    
    <!-- Footer -->
    <?= view('partials/footer') ?>

    <!-- Mensajes Flotantes Globales (Toasts) -->
    <?= view('components/floating_alert') ?>

    <!-- Page Specific JS Section -->
    <?= $this->renderSection('extra-js') ?>

    <script>
        function submitAction(url, message) {
            if (confirm(message)) {
                const form = document.getElementById('global-action-form');
                form.action = url;
                form.submit();
            }
        }
    </script>
    <form id="global-action-form" method="POST" style="display: none;">
        <?= csrf_field() ?>
    </form>
</body>
</html>
