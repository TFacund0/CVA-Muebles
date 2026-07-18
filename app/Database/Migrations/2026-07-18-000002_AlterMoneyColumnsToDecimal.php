<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Convierte las columnas de dinero de float(10,2) a decimal(10,2) para
 * evitar errores de redondeo en cálculos de precios, totales y pagos.
 */
class AlterMoneyColumnsToDecimal extends Migration
{
    private array $columns = [
        'productos'       => ['precio', 'precio_vta'],
        'ventas_cabecera' => ['total_venta'],
        'ventas_detalle'  => ['precio'],
        'ventas_pagos'    => ['monto'],
    ];

    public function up(): void
    {
        foreach ($this->columns as $table => $fields) {
            foreach ($fields as $field) {
                $this->db->query(
                    "ALTER TABLE `{$table}` MODIFY `{$field}` DECIMAL(10,2) NOT NULL",
                );
            }
        }
    }

    public function down(): void
    {
        foreach ($this->columns as $table => $fields) {
            foreach ($fields as $field) {
                $this->db->query(
                    "ALTER TABLE `{$table}` MODIFY `{$field}` FLOAT(10,2) NOT NULL",
                );
            }
        }
    }
}
