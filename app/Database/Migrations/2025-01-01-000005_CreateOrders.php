<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrders extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'auction_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'buyer_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'seller_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'total_amount'   => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'status'         => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'partial', 'paid', 'shipped', 'completed', 'cancelled'],
                'default'    => 'pending',
            ],
            'payment_method' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'notes'          => ['type' => 'TEXT', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('auction_id');
        $this->forge->addKey('buyer_id');
        $this->forge->addKey('seller_id');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('auction_id', 'auctions', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('buyer_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('seller_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('orders');
    }

    public function down(): void
    {
        $this->forge->dropTable('orders');
    }
}
