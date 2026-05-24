<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Pedido #<?= $venta['id'] ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap');
        
        body {
            font-family: 'Montserrat', sans-serif;
            color: #333;
            margin: 0;
            padding: 40px;
            background-color: #ffffff;
        }

        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #D4AF37;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header-left h1 {
            color: #2b1f15;
            font-size: 28px;
            margin: 0 0 5px 0;
            font-weight: 700;
        }

        .header-left p {
            margin: 0;
            color: #666;
            font-size: 12px;
        }

        .header-right {
            text-align: right;
        }

        .header-right h2 {
            color: #D4AF37;
            font-size: 24px;
            margin: 0 0 5px 0;
            font-weight: 700;
        }

        .header-right p {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: #2b1f15;
        }

        .details-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            background-color: #fcfbf8;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .details-box h3 {
            font-size: 12px;
            color: #888;
            text-transform: uppercase;
            margin: 0 0 5px 0;
            letter-spacing: 1px;
        }

        .details-box p {
            margin: 0 0 5px 0;
            font-size: 14px;
            color: #2b1f15;
            font-weight: 600;
        }

        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table.items-table th {
            background-color: #2b1f15;
            color: #D4AF37;
            text-align: left;
            padding: 12px 15px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        table.items-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            color: #444;
        }

        table.items-table th.text-right,
        table.items-table td.text-right {
            text-align: right;
        }
        
        table.items-table th.text-center,
        table.items-table td.text-center {
            text-align: center;
        }

        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 40px;
        }

        .totals-table {
            width: 300px;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 10px 15px;
            font-size: 14px;
        }

        .totals-table .total-row td {
            font-size: 18px;
            font-weight: 700;
            color: #2b1f15;
            border-top: 2px solid #D4AF37;
            padding-top: 15px;
        }

        .text-success { color: #198754 !important; }
        .text-danger { color: #dc3545 !important; }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 12px;
            color: #888;
        }
        
        .observaciones {
            margin-top: 30px;
            padding: 15px;
            border-left: 3px solid #D4AF37;
            background-color: #f9f9f9;
        }
        
        .observaciones h4 {
            margin: 0 0 5px 0;
            font-size: 12px;
            text-transform: uppercase;
            color: #2b1f15;
        }
        
        .observaciones p {
            margin: 0;
            font-size: 13px;
            color: #555;
            font-style: italic;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-left">
            <h1>CVA MUEBLES</h1>
            <p>Corrientes, Argentina</p>
            <p>WhatsApp: +54 9 379 409-8511</p>
        </div>
        <div class="header-right">
            <h2>COMPROBANTE</h2>
            <p>Pedido #<?= str_pad($venta['id'], 5, '0', STR_PAD_LEFT) ?></p>
            <p style="color: #888; font-size: 12px; font-weight: normal;">Fecha: <?= date('d/m/Y', strtotime($venta['fecha'])) ?></p>
        </div>
    </div>

    <div class="details-section">
        <div class="details-box">
            <h3>Facturar a:</h3>
            <p><?= esc($usuario['nombre'] . ' ' . $usuario['apellido']) ?></p>
            <p style="font-weight: normal; color: #666;"><?= esc($usuario['email']) ?></p>
        </div>
        <div class="details-box" style="text-align: right;">
            <h3>Estado del Pedido:</h3>
            <p style="color: #D4AF37; font-size: 16px;"><?= esc($venta['estado']) ?></p>
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Descripción de la Obra</th>
                <th class="text-center">Cant.</th>
                <th class="text-right">Precio Unitario</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detalles as $item): ?>
            <tr>
                <td>
                    <div style="font-weight: 600; color: #2b1f15; margin-bottom: 3px;"><?= esc($item['nombre_prod'] ?? 'Mueble a Medida') ?></div>
                    <?php if(!empty($item['descripcion'])): ?>
                        <div style="font-size: 12px; color: #777; font-style: italic;"><?= esc($item['descripcion']) ?></div>
                    <?php endif; ?>
                </td>
                <td class="text-center"><?= $item['cantidad'] ?></td>
                <td class="text-right">$<?= number_format($item['precio'], 0, ',', '.') ?></td>
                <td class="text-right">$<?= number_format($item['cantidad'] * $item['precio'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totals-section">
        <table class="totals-table">
            <tr>
                <td>Total de la Obra:</td>
                <td class="text-right" style="font-weight: 600;">$<?= number_format($venta['total_venta'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>Pagos Realizados:</td>
                <td class="text-right text-success" style="font-weight: 600;">-$<?= number_format($total_pagado, 0, ',', '.') ?></td>
            </tr>
            <tr class="total-row">
                <td>Saldo Pendiente:</td>
                <td class="text-right <?= $saldo_pendiente > 0 ? 'text-danger' : 'text-success' ?>">$<?= number_format($saldo_pendiente, 0, ',', '.') ?></td>
            </tr>
        </table>
    </div>

    <?php if (!empty($venta['observaciones'])): ?>
        <div class="observaciones">
            <h4>Especificaciones Constructivas</h4>
            <p>"<?= esc($venta['observaciones']) ?>"</p>
        </div>
    <?php endif; ?>

    <div class="footer">
        <p>Este comprobante es de uso interno e informativo. Los trabajos a medida requieren confirmación del taller.</p>
        <p style="font-weight: bold; margin-top: 5px;">¡Gracias por confiar en CVA Muebles!</p>
    </div>

</body>
</html>
