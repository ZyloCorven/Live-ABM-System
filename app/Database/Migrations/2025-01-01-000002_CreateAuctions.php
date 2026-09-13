<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuctions extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'seller_id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'title'               => ['type' => 'VARCHAR', 'constraint' => 200],
            'description'         => ['type' => 'TEXT', 'null' => true],
            'starting_price'      => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'reserve_price'       => ['type' => 'DECIMAL', 'constraint' => '12,2', 'null' => true],
            'current_highest_bid' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'null' => true],
            'current_winner_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'min_increment'       => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 1],
            'start_time'          => ['type' => 'DATETIME'],
            'end_time'            => ['type' => 'DATETIME'],
            'original_end_time'   => ['type' => 'DATETIME'],
            'status'              => [
                'type'       => 'ENUM',
                'constraint' => ['upcoming', 'live', 'extended', 'ended', 'sold', 'cancelled'],
                'default'    => 'upcoming',
            ],
            'extension_count' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'image'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('seller_id');
        $this->forge->addKey('status');
        $this->forge->addKey('end_time');
        $this->forge->addForeignKey('seller_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('auctions');
    }

    public function down(): void
    {
        $this->forge->dropTable('auctions');
    }
}
