<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f8f9fa; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background-color: #212529; color: #ffffff; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; letter-spacing: 1px; }
        .content { padding: 30px; line-height: 1.6; }
        .footer { background-color: #f1f3f5; padding: 15px; text-align: center; font-size: 12px; color: #6c757d; border-top: 1px solid #dee2e6; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #0d6efd; color: #ffffff !important; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 15px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { padding: 10px; border-bottom: 1px solid #dee2e6; text-align: left; }
        .table th { background-color: #f8f9fa; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-pendiente { background-color: #ffc107; color: #000; }
        .badge-en_proceso { background-color: #0dcaf0; color: #fff; }
        .badge-terminado { background-color: #198754; color: #fff; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>CVA Muebles</h1>
        </div>
        <div class="content">
            <?= $this->renderSection('content') ?>
        </div>
        <div class="footer">
            <p>CVA Muebles - Diseño y fabricación de muebles a medida.</p>
            <p>Este es un correo automático, por favor no responda directamente a esta dirección.</p>
        </div>
    </div>
</body>
</html>
