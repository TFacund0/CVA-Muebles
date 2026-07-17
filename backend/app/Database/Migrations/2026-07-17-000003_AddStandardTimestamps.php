<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Agrega created_at/updated_at nativos de CI4 a las tablas que no tenían
 * ninguna columna de fecha propia (usuarios, productos, categorias).
 */
class AddStandardTimestamps extends Migration
{
    private array $tables = ['usuarios', 'productos', 'categorias'];

    public function up()
    {
        $fields = [
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ];

        foreach ($this->tables as $table) {
            $this->forge->addColumn($table, $fields);
        }
    }

    public function down()
    {
        foreach ($this->tables as $table) {
            $this->forge->dropColumn($table, ['created_at', 'updated_at']);
        }
    }
}
