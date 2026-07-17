<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Agrega deleted_at nativo de CI4 a productos y usuarios, y migra los datos
 * existentes desde las columnas hand-rolled (eliminado, baja). Esas columnas
 * viejas se conservan por ahora: siguen usadas fuera del alcance de este cambio.
 */
class StandardizeSoftDeletes extends Migration
{
    public function up()
    {
        $this->forge->addColumn('productos', [
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addColumn('usuarios', [
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->db->query("UPDATE `productos` SET `deleted_at` = NOW() WHERE `eliminado` = 'SI'");
        $this->db->query("UPDATE `usuarios` SET `deleted_at` = NOW() WHERE `baja` = 'SI'");
    }

    public function down()
    {
        $this->forge->dropColumn('productos', ['deleted_at']);
        $this->forge->dropColumn('usuarios', ['deleted_at']);
    }
}
