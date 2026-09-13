<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePayments extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'order_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'amount'      => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'method'      => ['type' => 'ENUM', 'constraint' => ['cash', 'card', 'online', 'other'], 'default' => 'cash'],
            'reference'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'paid_at'     => ['type' => 'DATETIME', 'null' => true],
            'recorded_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('order_id');
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('recorded_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('payments');
    }

    public function down(): void
    {
        $this->forge->dropTable('payments');
    }
}
