<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Algo salió mal - CVA Muebles</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Lora:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --cva-brown: #3e2723;
            --cva-gold: #cda434;
            --cva-gold-soft: #f4ebd0;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fdfaf7;
            color: var(--cva-brown);
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            padding: 20px;
        }
        .error-container {
            max-width: 600px;
            background: white;
            padding: 3rem;
            border-radius: 2rem;
            box-shadow: 0 15px 35px rgba(62, 39, 35, 0.05);
            border: 2px dashed var(--cva-gold-soft);
        }
        .error-icon {
            font-size: 5rem;
            color: var(--cva-gold);
            margin-bottom: 1rem;
        }
        h1 {
            font-family: 'Lora', serif;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--cva-brown);
        }
        p {
            color: #6c757d;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .btn-cva {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: var(--cva-brown);
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-cva:hover {
            background-color: var(--cva-gold);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(205, 164, 52, 0.3);
        }
    </style>
</head>
<body>

    <div class="error-container">
        <i class="bi bi-tools error-icon"></i>
        <h1>¡Uy! Tuvimos un contratiempo</h1>
        <p>Parece que ocurrió un problema inesperado en el taller. Nuestros artesanos ya han sido notificados y están revisando qué sucedió para solucionarlo de inmediato.</p>
        <a href="<?= base_url() ?>" class="btn-cva">
            <i class="bi bi-house-door-fill" style="margin-right: 8px;"></i> VOLVER AL INICIO
        </a>
    </div>

</body>
</html>
