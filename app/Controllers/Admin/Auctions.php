<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AuctionModel;
use App\Services\AuctionService;

class Auctions extends BaseController
{
    protected AuctionModel $auctions;
    protected AuctionService $auctionService;

    public function __construct()
    {
        $this->auctions       = new AuctionModel();
        $this->auctionService = new AuctionService();
    }

    public function index()
    {
        return view('admin/auctions', [
            'title'    => 'All Auctions',
            'auctions' => $this->auctions->select('auctions.*, users.full_name as seller_name')
                ->join('users', 'users.id = auctions.seller_id')
                ->orderBy('auctions.created_at', 'DESC')
                ->findAll(),
        ]);
    }

    public function forceEnd(int $id)
    {
        $result = $this->auctionService->forceEnd($id);

        return redirect()->to('/admin/auctions')
            ->with($result['ok'] ? 'success' : 'error', $result['ok'] ? 'Auction force-ended.' : $result['message']);
    }

    public function cancel(int $id)
    {
        $this->auctions->update($id, ['status' => 'cancelled']);

        return redirect()->to('/admin/auctions')->with('success', 'Auction cancelled.');
    }
}
