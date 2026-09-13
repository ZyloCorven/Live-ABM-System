<?php

namespace App\Controllers;

use App\Models\AuctionModel;
use App\Models\BidModel;
use App\Services\BidService;

/**
 * JSON/AJAX endpoints used by the auction detail page for real-time updates:
 * placing a bid, and polling the current leader/time-remaining/history.
 */
class Bids extends BaseController
{
    protected BidService $bidService;
    protected AuctionModel $auctions;
    protected BidModel $bids;

    public function __construct()
    {
        $this->bidService = new BidService();
        $this->auctions   = new AuctionModel();
        $this->bids       = new BidModel();
    }

    public function place(int $auctionId)
    {
        $amount = (float) $this->request->getPost('amount');

        $result = $this->bidService->placeBid($auctionId, (int) $this->currentUserId(), $amount);

        return $this->response->setJSON([
            'ok'      => $result['ok'],
            'message' => $result['message'],
            'state'   => $result['ok'] ? $this->buildState($auctionId) : null,
        ])->setStatusCode($result['ok'] ? 200 : 422);
    }

    /**
     * Polled by the front-end every few seconds to refresh highest bid,
     * time remaining, and recent bid history without a full page reload.
     */
    public function state(int $auctionId)
    {
        return $this->response->setJSON($this->buildState($auctionId));
    }

    private function buildState(int $auctionId): array
    {
        $auction = $this->auctions->find($auctionId);
        $leader  = $this->bidService->currentLeader($auctionId);
        $history = $this->bids->latestFirst($auctionId, 10);

        $secondsRemaining = $auction ? max(0, strtotime($auction['end_time']) - time()) : 0;

        return [
            'status'            => $auction['status'] ?? null,
            'current_highest'   => $auction['current_highest_bid'] ?? null,
            'leader_initials'   => $leader ? $this->initials($leader['user_id']) : null,
            'seconds_remaining' => $secondsRemaining,
            'end_time'          => $auction['end_time'] ?? null,
            'extension_count'   => $auction['extension_count'] ?? 0,
            'history'           => array_map(static fn ($b) => [
                'user'       => $b['full_name'] ?? $b['username'] ?? 'Bidder',
                'amount'     => $b['amount'],
                'created_at' => $b['created_at'],
            ], $history),
        ];
    }

    private function initials(int $userId): string
    {
        $user = (new \App\Models\UserModel())->find($userId);
        if (! $user) {
            return '??';
        }
        $parts = preg_split('/\s+/', trim($user['full_name']));

        return strtoupper(substr($parts[0] ?? '', 0, 1) . substr($parts[1] ?? '', 0, 1));
    }
}
