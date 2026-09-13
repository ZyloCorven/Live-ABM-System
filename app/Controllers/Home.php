<?php

namespace App\Controllers;

use App\Models\AuctionModel;
use App\Services\AuctionService;

class Home extends BaseController
{
    public function index(): string
    {
        (new AuctionService())->runSweep();

        $auctions = new AuctionModel();

        $data = [
            'title'         => 'Welcome',
            'liveAuctions'  => $auctions->select('auctions.*, users.full_name as seller_name')
                ->join('users', 'users.id = auctions.seller_id')
                ->whereIn('auctions.status', ['live', 'extended'])
                ->orderBy('auctions.end_time', 'ASC')
                ->findAll(6),
        ];

        return view('home', $data);
    }
}
