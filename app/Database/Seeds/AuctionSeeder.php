<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AuctionSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        // seller ids 2 = sarah, 3 = mike (per UserSeeder insert order)
        $auctions = [
            [
                'seller_id' => 2, 'title' => 'Vintage Gibson Les Paul Guitar',
                'description' => 'A beautifully preserved 1970s Gibson Les Paul, single owner.',
                'starting_price' => 500.00, 'reserve_price' => 800.00, 'min_increment' => 25.00,
                'current_highest_bid' => null, 'current_winner_id' => null,
                'start_time' => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'end_time' => date('Y-m-d H:i:s', strtotime('+2 hours')),
                'original_end_time' => date('Y-m-d H:i:s', strtotime('+2 hours')),
                'status' => 'live', 'extension_count' => 0, 'image' => null,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'seller_id' => 3, 'title' => 'Antique Oak Writing Desk',
                'description' => 'Solid oak roll-top desk, early 1900s, excellent condition.',
                'starting_price' => 150.00, 'reserve_price' => 200.00, 'min_increment' => 10.00,
                'current_highest_bid' => null, 'current_winner_id' => null,
                'start_time' => date('Y-m-d H:i:s', strtotime('-30 minutes')),
                'end_time' => date('Y-m-d H:i:s', strtotime('+4 minutes')),
                'original_end_time' => date('Y-m-d H:i:s', strtotime('+4 minutes')),
                'status' => 'live', 'extension_count' => 0, 'image' => null,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'seller_id' => 2, 'title' => 'Signed Original Basketball Jersey',
                'description' => 'Game-worn and autographed jersey with certificate of authenticity.',
                'starting_price' => 300.00, 'reserve_price' => null, 'min_increment' => 20.00,
                'current_highest_bid' => null, 'current_winner_id' => null,
                'start_time' => date('Y-m-d H:i:s', strtotime('+1 day')),
                'end_time' => date('Y-m-d H:i:s', strtotime('+1 day +3 hours')),
                'original_end_time' => date('Y-m-d H:i:s', strtotime('+1 day +3 hours')),
                'status' => 'upcoming', 'extension_count' => 0, 'image' => null,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'seller_id' => 3, 'title' => 'Rare First-Edition Novel Collection',
                'description' => 'Set of 5 first-edition classic novels, well preserved dust jackets.',
                'starting_price' => 90.00, 'reserve_price' => 100.00, 'min_increment' => 5.00,
                'current_highest_bid' => 120.00, 'current_winner_id' => 4,
                'start_time' => date('Y-m-d H:i:s', strtotime('-3 days')),
                'end_time' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'original_end_time' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'status' => 'sold', 'extension_count' => 1, 'image' => null,
                'created_at' => $now, 'updated_at' => $now,
            ],
        ];

        $this->db->table('auctions')->insertBatch($auctions);

        // Seed a few bids on auction #4 (the already-sold one) for history/audit display.
        $this->db->table('bids')->insertBatch([
            ['auction_id' => 4, 'user_id' => 5, 'amount' => 95.00, 'is_winning' => 0, 'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))],
            ['auction_id' => 4, 'user_id' => 6, 'amount' => 105.00, 'is_winning' => 0, 'created_at' => date('Y-m-d H:i:s', strtotime('-2 days +1 hour'))],
            ['auction_id' => 4, 'user_id' => 4, 'amount' => 120.00, 'is_winning' => 1, 'created_at' => date('Y-m-d H:i:s', strtotime('-1 day -5 minutes'))],
        ]);

        $this->db->table('orders')->insert([
            'auction_id' => 4, 'buyer_id' => 4, 'seller_id' => 3, 'total_amount' => 120.00,
            'status' => 'pending', 'payment_method' => null, 'notes' => null,
            'created_at' => $now, 'updated_at' => $now,
        ]);
    }
}
