<?php

namespace App\Models;

use CodeIgniter\Model;

class AuctionModel extends Model
{
    protected $table         = 'auctions';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'seller_id', 'title', 'description', 'starting_price', 'reserve_price',
        'current_highest_bid', 'current_winner_id', 'min_increment',
        'start_time', 'end_time', 'original_end_time', 'status',
        'extension_count', 'image',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'title'          => 'required|min_length[3]|max_length[200]',
        'starting_price' => 'required|decimal|greater_than[0]',
        'min_increment'  => 'required|decimal|greater_than[0]',
        'start_time'     => 'required|valid_date',
        'end_time'       => 'required|valid_date',
    ];

    public const STATUSES = ['upcoming', 'live', 'extended', 'ended', 'sold', 'cancelled'];

    /**
     * Auctions that are currently open for bidding.
     */
    public function scopeOpenForBidding(): self
    {
        return $this->whereIn('status', ['live', 'extended']);
    }

    public function withSeller(int $id): ?array
    {
        return $this->select('auctions.*, users.full_name as seller_name, users.username as seller_username')
            ->join('users', 'users.id = auctions.seller_id')
            ->find($id);
    }

    /**
     * Auctions whose end_time has passed but are still marked live/extended.
     * Used by the sweeper that closes auctions and hands off to the POS.
     */
    public function dueForClose(): array
    {
        return $this->whereIn('status', ['live', 'extended'])
            ->where('end_time <=', date('Y-m-d H:i:s'))
            ->findAll();
    }

    /**
     * Auctions whose start_time has arrived but are still "upcoming".
     */
    public function dueToStart(): array
    {
        return $this->where('status', 'upcoming')
            ->where('start_time <=', date('Y-m-d H:i:s'))
            ->findAll();
    }
}
