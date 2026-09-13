<?php

namespace App\Models;

use CodeIgniter\Model;

class AuctionExtensionModel extends Model
{
    protected $table         = 'auction_extensions';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'auction_id', 'bid_id', 'old_end_time', 'new_end_time', 'extended_by_minutes',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}
