<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');
        $hash = static fn ($p) => password_hash($p, PASSWORD_DEFAULT);

        $this->db->table('users')->insertBatch([
            [
                'username' => 'admin', 'email' => 'admin@auction.test', 'password' => $hash('Password123'),
                'full_name' => 'Alex Admin', 'phone' => '555-0100', 'role' => 'admin', 'is_active' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'username' => 'sarah_seller', 'email' => 'sarah@auction.test', 'password' => $hash('Password123'),
                'full_name' => 'Sarah Seller', 'phone' => '555-0101', 'role' => 'seller', 'is_active' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'username' => 'mike_seller', 'email' => 'mike@auction.test', 'password' => $hash('Password123'),
                'full_name' => 'Mike Marchetti', 'phone' => '555-0102', 'role' => 'seller', 'is_active' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'username' => 'bella_bidder', 'email' => 'bella@auction.test', 'password' => $hash('Password123'),
                'full_name' => 'Bella Bidwell', 'phone' => '555-0103', 'role' => 'bidder', 'is_active' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'username' => 'carlos_bidder', 'email' => 'carlos@auction.test', 'password' => $hash('Password123'),
                'full_name' => 'Carlos Vega', 'phone' => '555-0104', 'role' => 'bidder', 'is_active' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'username' => 'nina_bidder', 'email' => 'nina@auction.test', 'password' => $hash('Password123'),
                'full_name' => 'Nina Novak', 'phone' => '555-0105', 'role' => 'bidder', 'is_active' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ],
        ]);
    }
}
