<?php

namespace App\Services;

use App\Models\AuctionModel;
use App\Models\BidModel;

/**
 * Auction lifecycle: creation, status transitions, and closing auctions off
 * to the POS system once their clock (as extended by BidService) runs out.
 */
class AuctionService
{
    private AuctionModel $auctions;
    private BidModel $bids;
    private PosService $pos;

    public function __construct()
    {
        $this->auctions = new AuctionModel();
        $this->bids     = new BidModel();
        $this->pos      = new PosService();
    }

    public function create(array $data): int|false
    {
        $data['original_end_time']   = $data['end_time'];
        $data['current_highest_bid'] = null;
        $data['current_winner_id']   = null;
        $data['extension_count']     = 0;
        $data['status']              = $data['status'] ?? 'upcoming';

        return $this->auctions->insert($data, true) ? $this->auctions->getInsertID() : false;
    }

    public function update(int $id, array $data): bool
    {
        unset($data['current_highest_bid'], $data['current_winner_id'], $data['extension_count']);

        return $this->auctions->update($id, $data);
    }

    /**
     * Cron/sweeper entry point: flips "upcoming" auctions whose start_time has
     * arrived to "live", and closes out auctions whose end_time has passed.
     * Call this from a scheduled task (spark command) or on-demand from a controller.
     */
    public function runSweep(): array
    {
        $started = 0;
        foreach ($this->auctions->dueToStart() as $auction) {
            $this->auctions->update($auction['id'], ['status' => 'live']);
            $started++;
        }

        $closed = 0;
        foreach ($this->auctions->dueForClose() as $auction) {
            $this->closeAuction((int) $auction['id']);
            $closed++;
        }

        return ['started' => $started, 'closed' => $closed];
    }

    /**
     * Close a single auction: determine the winner via the max-heap leader,
     * mark status, and hand off to the POS to auto-create a pending order.
     */
    public function closeAuction(int $auctionId): array
    {
        $auction = $this->auctions->find($auctionId);
        if (! $auction || ! in_array($auction['status'], ['live', 'extended'], true)) {
            return ['ok' => false, 'message' => 'Auction cannot be closed from its current status.'];
        }

        $bidService = new BidService();
        $winner     = $bidService->currentLeader($auctionId);

        $reserveMet = $auction['reserve_price'] === null
            || ($winner && (float) $winner['amount'] >= (float) $auction['reserve_price']);

        $status = $winner && $reserveMet ? 'sold' : 'ended';

        $this->auctions->update($auctionId, [
            'status'              => $status,
            'current_highest_bid' => $winner['amount'] ?? $auction['current_highest_bid'],
            'current_winner_id'   => $winner['user_id'] ?? $auction['current_winner_id'],
        ]);

        $order = null;
        if ($status === 'sold') {
            $order = $this->pos->createOrderFromAuction($auctionId);
        }

        return ['ok' => true, 'status' => $status, 'winner' => $winner, 'order' => $order];
    }

    /**
     * Admin override: force-end an auction immediately regardless of end_time.
     */
    public function forceEnd(int $auctionId): array
    {
        $this->auctions->update($auctionId, ['end_time' => date('Y-m-d H:i:s')]);

        return $this->closeAuction($auctionId);
    }
}
