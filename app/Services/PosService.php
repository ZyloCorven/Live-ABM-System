<?php

namespace App\Services;

use App\Models\AuctionModel;
use App\Models\OrderModel;
use App\Models\PaymentModel;

/**
 * Point-of-Sale layer: turns a won auction into an order/invoice, records
 * payments (including partial payments), and reports sales dashboard stats.
 */
class PosService
{
    private OrderModel $orders;
    private PaymentModel $payments;
    private AuctionModel $auctions;

    public function __construct()
    {
        $this->orders   = new OrderModel();
        $this->payments = new PaymentModel();
        $this->auctions = new AuctionModel();
    }

    /**
     * Auto-create a pending order when an auction closes as "sold".
     */
    public function createOrderFromAuction(int $auctionId): ?array
    {
        $auction = $this->auctions->find($auctionId);
        if (! $auction || ! $auction['current_winner_id']) {
            return null;
        }

        // Avoid duplicate orders if this is called more than once.
        $existing = $this->orders->where('auction_id', $auctionId)->first();
        if ($existing) {
            return $existing;
        }

        $orderId = $this->orders->insert([
            'auction_id'   => $auctionId,
            'buyer_id'     => $auction['current_winner_id'],
            'seller_id'    => $auction['seller_id'],
            'total_amount' => $auction['current_highest_bid'],
            'status'       => 'pending',
        ], true);

        return $this->orders->find($orderId);
    }

    /**
     * Manual POS sale for a non-auction item.
     */
    public function createManualOrder(int $sellerId, int $buyerId, float $amount, ?string $notes = null): int
    {
        return $this->orders->insert([
            'auction_id'   => null,
            'buyer_id'     => $buyerId,
            'seller_id'    => $sellerId,
            'total_amount' => $amount,
            'status'       => 'pending',
            'notes'        => $notes,
        ], true);
    }

    /**
     * Record a (possibly partial) payment against an order and recompute status.
     */
    public function recordPayment(int $orderId, float $amount, string $method, int $recordedBy, ?string $reference = null): array
    {
        $order = $this->orders->find($orderId);
        if (! $order) {
            return ['ok' => false, 'message' => 'Order not found.'];
        }
        if ($amount <= 0) {
            return ['ok' => false, 'message' => 'Payment amount must be positive.'];
        }

        $db = db_connect();
        $db->transStart();

        $this->payments->insert([
            'order_id'    => $orderId,
            'amount'      => $amount,
            'method'      => $method,
            'reference'   => $reference,
            'paid_at'     => date('Y-m-d H:i:s'),
            'recorded_by' => $recordedBy,
        ]);

        $totalPaid = $this->orders->totalPaid($orderId);
        $newStatus = $totalPaid >= (float) $order['total_amount'] ? 'paid' : 'partial';

        $this->orders->update($orderId, [
            'status'         => $newStatus,
            'payment_method' => $method,
        ]);

        $db->transComplete();

        return [
            'ok'         => $db->transStatus() !== false,
            'message'    => 'Payment recorded.',
            'total_paid' => $totalPaid,
            'status'     => $newStatus,
        ];
    }

    public function markShipped(int $orderId): bool
    {
        return $this->orders->update($orderId, ['status' => 'shipped']);
    }

    public function markCompleted(int $orderId): bool
    {
        return $this->orders->update($orderId, ['status' => 'completed']);
    }

    /**
     * Sales dashboard stats, optionally scoped to a seller.
     */
    public function dashboardStats(?int $sellerId = null): array
    {
        $builder = $this->orders->builder();
        if ($sellerId !== null) {
            $builder->where('seller_id', $sellerId);
        }

        $totalSales = (clone $builder)->selectSum('total_amount')->get()->getRow()->total_amount ?? 0;

        $pending = (clone $builder)->whereIn('status', ['pending', 'partial'])->countAllResults(false);
        $paid    = (clone $builder)->whereIn('status', ['paid', 'shipped', 'completed'])->countAllResults(false);
        $total   = (clone $builder)->countAllResults();

        return [
            'total_sales'  => (float) $totalSales,
            'pending'      => $pending,
            'completed'    => $paid,
            'total_orders' => $total,
        ];
    }
}
