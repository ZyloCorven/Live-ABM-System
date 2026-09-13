<?php

namespace App\Models;

use CodeIgniter\Model;

class BidModel extends Model
{
    protected $table         = 'bids';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['auction_id', 'user_id', 'amount', 'is_winning'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    protected $validationRules = [
        'auction_id' => 'required|integer',
        'user_id'    => 'required|integer',
        'amount'     => 'required|decimal|greater_than[0]',
    ];

    /**
     * Root of the max-heap for a single auction: the highest bid amount,
     * ties broken by the earliest created_at (and then lowest id).
     * This mirrors extracting the max from a heap keyed on (amount DESC, created_at ASC).
     */
    public function heapRoot(int $auctionId): ?array
    {
        return $this->where('auction_id', $auctionId)
            ->orderBy('amount', 'DESC')
            ->orderBy('created_at', 'ASC')
            ->orderBy('id', 'ASC')
            ->first();
    }

    /**
     * Full ordered heap array for an auction (highest first) — equivalent to
     * repeatedly popping the max off a max-heap of bids.
     */
    public function heapOrder(int $auctionId, int $limit = 100): array
    {
        return $this->where('auction_id', $auctionId)
            ->orderBy('amount', 'DESC')
            ->orderBy('created_at', 'ASC')
            ->orderBy('id', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    public function latestFirst(int $auctionId, int $limit = 50): array
    {
        return $this->select('bids.*, users.username, users.full_name')
            ->join('users', 'users.id = bids.user_id')
            ->where('auction_id', $auctionId)
            ->orderBy('bids.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function countRecentByUser(int $auctionId, int $userId, int $seconds): int
    {
        return $this->where('auction_id', $auctionId)
            ->where('user_id', $userId)
            ->where('created_at >=', date('Y-m-d H:i:s', time() - $seconds))
            ->countAllResults();
    }
}
