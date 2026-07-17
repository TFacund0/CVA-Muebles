<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Marca el punto de partida del historial de migraciones de CI4.
 * El esquema existente antes de esta migración fue creado a partir de cva_muebles.sql
 * (fuera del sistema de migraciones); de aquí en adelante, las migraciones son la fuente de verdad.
 */
class Baseline extends Migration
{
    public function up()
    {
        // No-op: el esquema base ya existe en la BD, aplicado vía cva_muebles.sql.
    }

    public function down()
    {
        // No-op.
    }
}
