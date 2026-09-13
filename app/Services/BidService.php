<?php

namespace App\Services;

use App\Models\AuctionExtensionModel;
use App\Models\AuctionModel;
use App\Models\BidModel;
use App\Models\SettingModel;
use RuntimeException;

/**
 * Owns all bid placement logic: validation, max-heap ranking, persistence,
 * and the automatic "extend the clock" behaviour for last-minute bids.
 */
class BidService
{
    private const RATE_LIMIT_SECONDS = 3; // min gap between bids from the same user on the same auction

    private AuctionModel $auctions;
    private BidModel $bids;
    private AuctionExtensionModel $extensions;
    private SettingModel $settings;

    public function __construct()
    {
        $this->auctions   = new AuctionModel();
        $this->bids       = new BidModel();
        $this->extensions = new AuctionExtensionModel();
        $this->settings   = new SettingModel();
    }

    /**
     * Compare two bid rows for max-heap ordering:
     * higher amount wins; ties go to the earlier created_at, then lower id.
     */
    public static function bidComparator(): callable
    {
        return static function (array $a, array $b): int {
            $byAmount = (float) $a['amount'] <=> (float) $b['amount'];
            if ($byAmount !== 0) {
                return $byAmount;
            }

            $byTime = strtotime($b['created_at']) <=> strtotime($a['created_at']); // earlier wins => reversed
            if ($byTime !== 0) {
                return $byTime;
            }

            return ($b['id'] ?? 0) <=> ($a['id'] ?? 0);
        };
    }

    /**
     * Rebuild the max-heap for an auction from all its bids and return the
     * current leader (heap root) without mutating anything.
     */
    public function currentLeader(int $auctionId): ?array
    {
        $bids = $this->bids->heapOrder($auctionId, 500);
        if ($bids === []) {
            return null;
        }

        $heap = MaxHeap::fromArray($bids, self::bidComparator());

        return $heap->peek();
    }

    /**
     * Full leaderboard (highest first) via repeated heap extraction.
     */
    public function leaderboard(int $auctionId, int $limit = 20): array
    {
        $bids = $this->bids->heapOrder($auctionId, 500);
        if ($bids === []) {
            return [];
        }

        $heap = MaxHeap::fromArray($bids, self::bidComparator());

        return array_slice($heap->toSortedArray(), 0, $limit);
    }

    /**
     * Validate and place a bid. Returns an array:
     *   ['ok' => bool, 'message' => string, 'auction' => array|null, 'bid' => array|null, 'extended' => bool]
     */
    public function placeBid(int $auctionId, int $userId, float $amount): array
    {
        $auction = $this->auctions->find($auctionId);

        if (! $auction) {
            return $this->fail('Auction not found.');
        }

        if (! in_array($auction['status'], ['live', 'extended'], true)) {
            return $this->fail('This auction is not currently open for bidding.');
        }

        if ((int) $auction['seller_id'] === $userId) {
            return $this->fail('You cannot bid on your own auction.');
        }

        if (! is_numeric($amount) || $amount <= 0) {
            return $this->fail('Bid amount must be a positive number.');
        }

        if (strtotime($auction['end_time']) < time()) {
            return $this->fail('This auction has already ended.');
        }

        // Anti-spam: rate limit rapid repeated submissions from the same bidder.
        if ($this->bids->countRecentByUser($auctionId, $userId, self::RATE_LIMIT_SECONDS) > 0) {
            return $this->fail('Please wait a few seconds before bidding again.');
        }

        $currentHighest = $this->currentLeader($auctionId);
        $floor          = $currentHighest ? (float) $currentHighest['amount'] : (float) $auction['starting_price'];
        $minRequired    = $currentHighest
            ? $floor + (float) $auction['min_increment']
            : $floor;

        if ($amount < $minRequired) {
            return $this->fail(sprintf(
                'Bid must be at least %s (current highest + minimum increment).',
                number_format($minRequired, 2)
            ));
        }

        $db = db_connect();

        $db->transStart();

        $bidId = $this->bids->insert([
            'auction_id' => $auctionId,
            'user_id'    => $userId,
            'amount'     => $amount,
            'is_winning' => 0,
        ], true);

        // Recompute the heap root with the new bid included (O(log n) insert).
        $allBids = $this->bids->heapOrder($auctionId, 500);
        $heap    = MaxHeap::fromArray($allBids, self::bidComparator());
        $winner  = $heap->peek();

        // Clear old winning flags, set the new one — heap root is authoritative.
        $this->bids->where('auction_id', $auctionId)->set(['is_winning' => 0])->update();
        if ($winner) {
            $this->bids->update($winner['id'], ['is_winning' => 1]);
        }

        $updateData = [
            'current_highest_bid' => $winner['amount'] ?? null,
            'current_winner_id'   => $winner['user_id'] ?? null,
        ];

        // Automatic extension: a valid bid inside the closing window pushes the clock out.
        $extended    = false;
        $windowMin   = (float) $this->settings->get('extension_window_minutes', 5);
        $durationMin = (int) $this->settings->get('extension_duration_minutes', 3);

        $secondsRemaining = strtotime($auction['end_time']) - time();
        if ($secondsRemaining <= $windowMin * 60 && $secondsRemaining >= 0) {
            $oldEnd = $auction['end_time'];
            $newEnd = date('Y-m-d H:i:s', strtotime($auction['end_time']) + $durationMin * 60);

            $updateData['end_time']         = $newEnd;
            $updateData['status']           = 'extended';
            $updateData['extension_count']  = (int) $auction['extension_count'] + 1;

            $this->extensions->insert([
                'auction_id'          => $auctionId,
                'bid_id'              => $bidId,
                'old_end_time'        => $oldEnd,
                'new_end_time'        => $newEnd,
                'extended_by_minutes' => $durationMin,
            ]);

            $extended = true;
        }

        $this->auctions->update($auctionId, $updateData);

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new RuntimeException('Failed to place bid due to a database error.');
        }

        return [
            'ok'       => true,
            'message'  => $extended ? 'Bid placed! Auction extended.' : 'Bid placed successfully.',
            'auction'  => $this->auctions->find($auctionId),
            'bid'      => $this->bids->find($bidId),
            'extended' => $extended,
        ];
    }

    private function fail(string $message): array
    {
        return ['ok' => false, 'message' => $message, 'auction' => null, 'bid' => null, 'extended' => false];
    }
}
