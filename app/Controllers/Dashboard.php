<?php

namespace App\Controllers;

use App\Models\AuctionModel;
use App\Models\BidModel;
use App\Models\OrderModel;
use App\Models\UserModel;
use App\Services\PosService;

class Dashboard extends BaseController
{
    public function index()
    {
        return match ($this->currentRole()) {
            'admin'  => $this->admin(),
            'seller' => $this->seller(),
            default  => $this->bidder(),
        };
    }

    private function admin()
    {
        $auctions = new AuctionModel();
        $users    = new UserModel();
        $bids     = new BidModel();
        $pos      = new PosService();

        $data = [
            'title'  => 'Admin Dashboard',
            'stats'  => [
                'total_users'    => $users->countAll(),
                'total_auctions' => $auctions->countAll(),
                'live_auctions'  => $auctions->whereIn('status', ['live', 'extended'])->countAllResults(),
                'total_bids'     => $bids->countAll(),
            ],
            'sales'          => $pos->dashboardStats(),
            'recentAuctions' => $auctions->orderBy('created_at', 'DESC')->findAll(8),
        ];

        return view('dashboard/admin', $data);
    }

    private function seller()
    {
        $auctions = new AuctionModel();
        $pos      = new PosService();
        $sellerId = $this->currentUserId();

        $data = [
            'title'    => 'Seller Dashboard',
            'auctions' => $auctions->where('seller_id', $sellerId)->orderBy('created_at', 'DESC')->findAll(),
            'sales'    => $pos->dashboardStats($sellerId),
        ];

        return view('dashboard/seller', $data);
    }

    private function bidder()
    {
        $auctions = new AuctionModel();
        $bids     = new BidModel();
        $orders   = new OrderModel();
        $userId   = $this->currentUserId();

        // Auctions this bidder has participated in.
        $auctionIds = array_unique(array_column(
            $bids->where('user_id', $userId)->select('auction_id')->findAll(),
            'auction_id'
        ));

        $data = [
            'title'       => 'My Bidding Activity',
            'active'      => $auctionIds
                ? $auctions->whereIn('id', $auctionIds)->whereIn('status', ['live', 'extended'])->findAll()
                : [],
            'won'         => $auctions->where('current_winner_id', $userId)->whereIn('status', ['sold'])->findAll(),
            'orders'      => $orders->where('buyer_id', $userId)->orderBy('created_at', 'DESC')->findAll(),
        ];

        return view('dashboard/bidder', $data);
    }
}
