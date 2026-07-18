<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Reemplaza los flags de borrado lógico ad-hoc (productos.eliminado
 * varchar 'SI'/'NO', usuarios.baja varchar 'SI'/'NO') por el soporte
 * nativo de soft-delete de CodeIgniter (deleted_at), agregando también
 * created_at/updated_at estándar. Migra los datos existentes antes de
 * borrar las columnas viejas.
 */
class StandardizeSoftDeletes extends Migration
{
    public function up(): void
    {
        // Nota: esta base de datos (cva_muebles) es compartida entre varias
        // ramas de este proyecto que corrieron migraciones por separado; si
        // created_at/updated_at/deleted_at ya existen (agregadas por otra
        // rama), se omite el addColumn y solo se migran los datos.
        $fields = $this->db->getFieldNames('productos');
        if (!in_array('deleted_at', $fields, true)) {
            $this->forge->addColumn('productos', [
                'created_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'descripcion'],
                'updated_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'created_at'],
                'deleted_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'updated_at'],
            ]);
        }

        $fields = $this->db->getFieldNames('usuarios');
        if (!in_array('deleted_at', $fields, true)) {
            $this->forge->addColumn('usuarios', [
                'created_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'perfil_id'],
                'updated_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'created_at'],
                'deleted_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'updated_at'],
            ]);
        }

        $this->db->query("UPDATE productos SET deleted_at = NOW() WHERE eliminado = 'SI' AND deleted_at IS NULL");
        $this->db->query("UPDATE usuarios SET deleted_at = NOW() WHERE baja = 'SI' AND deleted_at IS NULL");

        $this->forge->dropColumn('productos', 'eliminado');
        $this->forge->dropColumn('usuarios', 'baja');
    }

    public function down(): void
    {
        $this->forge->addColumn('productos', [
            'eliminado' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => false, 'default' => 'NO'],
        ]);
        $this->forge->addColumn('usuarios', [
            'baja' => ['type' => 'VARCHAR', 'constraint' => 2, 'null' => false, 'default' => 'NO'],
        ]);

        $this->db->query("UPDATE productos SET eliminado = 'SI' WHERE deleted_at IS NOT NULL");
        $this->db->query("UPDATE usuarios SET baja = 'SI' WHERE deleted_at IS NOT NULL");

        $this->forge->dropColumn('productos', ['created_at', 'updated_at', 'deleted_at']);
        $this->forge->dropColumn('usuarios', ['created_at', 'updated_at', 'deleted_at']);
    }
}
