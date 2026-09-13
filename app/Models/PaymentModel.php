<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table         = 'payments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['order_id', 'amount', 'method', 'reference', 'paid_at', 'recorded_by'];
    protected $useTimestamps = false;

    protected $validationRules = [
        'order_id' => 'required|integer',
        'amount'   => 'required|decimal|greater_than[0]',
        'method'   => 'required|in_list[cash,card,online,other]',
    ];
}
