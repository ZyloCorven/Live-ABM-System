<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuctionExtensions extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'auction_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'bid_id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'old_end_time'        => ['type' => 'DATETIME'],
            'new_end_time'        => ['type' => 'DATETIME'],
            'extended_by_minutes' => ['type' => 'INT', 'constraint' => 11],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('auction_id');
        $this->forge->addForeignKey('auction_id', 'auctions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('bid_id', 'bids', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('auction_extensions');
    }

    public function down(): void
    {
        $this->forge->dropTable('auction_extensions');
    }
}
