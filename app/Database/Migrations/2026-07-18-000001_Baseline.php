<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migración baseline: documenta que el esquema preexistente proviene de
 * database/cva_muebles.sql. No crea ni modifica tablas — a partir de acá,
 * el historial de migraciones es la fuente de verdad para cambios de BD.
 */
class Baseline extends Migration
{
    public function up(): void
    {
    }

    public function down(): void
    {
    }
}
