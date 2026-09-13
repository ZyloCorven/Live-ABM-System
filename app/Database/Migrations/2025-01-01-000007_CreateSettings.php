<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettings extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'setting_key'   => ['type' => 'VARCHAR', 'constraint' => 100],
            'setting_value' => ['type' => 'VARCHAR', 'constraint' => 255],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('setting_key');
        $this->forge->createTable('settings');

        $this->db->table('settings')->insertBatch([
            ['setting_key' => 'extension_window_minutes', 'setting_value' => '5', 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_key' => 'extension_duration_minutes', 'setting_value' => '3', 'updated_at' => date('Y-m-d H:i:s')],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropTable('settings');
    }
}
