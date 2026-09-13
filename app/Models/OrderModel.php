<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table         = 'orders';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'auction_id', 'buyer_id', 'seller_id', 'total_amount', 'status', 'payment_method', 'notes',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public const STATUSES = ['pending', 'partial', 'paid', 'shipped', 'completed', 'cancelled'];

    public function withDetails(int $id): ?array
    {
        return $this->select('orders.*, a.title as auction_title, a.image as auction_image,
                buyer.full_name as buyer_name, buyer.email as buyer_email,
                seller.full_name as seller_name')
            ->join('auctions a', 'a.id = orders.auction_id', 'left')
            ->join('users buyer', 'buyer.id = orders.buyer_id')
            ->join('users seller', 'seller.id = orders.seller_id')
            ->find($id);
    }

    public function totalPaid(int $orderId): float
    {
        $result = $this->db->table('payments')
            ->selectSum('amount')
            ->where('order_id', $orderId)
            ->get()
            ->getRow();

        return (float) ($result->amount ?? 0);
    }
}
