<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBids extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'auction_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'amount'     => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'is_winning' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        // Critical composite index powering max-heap style lookups:
        // highest amount first, earliest timestamp breaks ties.
        $this->forge->addKey(['auction_id', 'amount', 'created_at'], false, false, 'idx_bids_heap');
        $this->forge->addForeignKey('auction_id', 'auctions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('bids');

        // amount DESC, created_at ASC composite exactly as specified
        $this->db->query('CREATE INDEX idx_bids_heap_order ON bids (auction_id, amount DESC, created_at ASC)');
    }

    public function down(): void
    {
        $this->forge->dropTable('bids');
    }
}
