<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\UserModel;
use App\Services\PosService;

class Pos extends BaseController
{
    protected OrderModel $orders;
    protected PosService $posService;

    public function __construct()
    {
        $this->orders     = new OrderModel();
        $this->posService = new PosService();
    }

    /**
     * POS dashboard: orders belonging to this seller (or all, for admin).
     */
    public function index()
    {
        $builder = $this->orders->select('orders.*, a.title as auction_title, buyer.full_name as buyer_name')
            ->join('auctions a', 'a.id = orders.auction_id', 'left')
            ->join('users buyer', 'buyer.id = orders.buyer_id')
            ->orderBy('orders.created_at', 'DESC');

        if ($this->currentRole() !== 'admin') {
            $builder->where('orders.seller_id', $this->currentUserId());
        }

        $data = [
            'title'  => 'Point of Sale',
            'orders' => $builder->paginate(15),
            'pager'  => $this->orders->pager,
            'stats'  => $this->posService->dashboardStats(
                $this->currentRole() === 'admin' ? null : $this->currentUserId()
            ),
        ];

        return view('pos/dashboard', $data);
    }

    public function show(int $orderId)
    {
        $order = $this->orders->withDetails($orderId);
        if (! $order) {
            return redirect()->to('/pos')->with('error', 'Order not found.');
        }

        if ($this->currentRole() !== 'admin' && (int) $order['seller_id'] !== $this->currentUserId()) {
            return redirect()->to('/pos')->with('error', 'You cannot view this order.');
        }

        $payments = db_connect()->table('payments')->where('order_id', $orderId)
            ->orderBy('paid_at', 'DESC')->get()->getResultArray();

        $data = [
            'title'      => 'Order #' . $orderId,
            'order'      => $order,
            'payments'   => $payments,
            'totalPaid'  => $this->orders->totalPaid($orderId),
            'balance'    => (float) $order['total_amount'] - $this->orders->totalPaid($orderId),
        ];

        return view('pos/order', $data);
    }

    public function recordPayment(int $orderId)
    {
        $rules = [
            'amount' => 'required|decimal|greater_than[0]',
            'method' => 'required|in_list[cash,card,online,other]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $result = $this->posService->recordPayment(
            $orderId,
            (float) $this->request->getPost('amount'),
            $this->request->getPost('method'),
            (int) $this->currentUserId(),
            $this->request->getPost('reference')
        );

        return redirect()->to('/pos/orders/' . $orderId)
            ->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    public function markShipped(int $orderId)
    {
        $this->posService->markShipped($orderId);

        return redirect()->to('/pos/orders/' . $orderId)->with('success', 'Marked as shipped.');
    }

    public function markCompleted(int $orderId)
    {
        $this->posService->markCompleted($orderId);

        return redirect()->to('/pos/orders/' . $orderId)->with('success', 'Order completed.');
    }

    public function invoice(int $orderId)
    {
        $order = $this->orders->withDetails($orderId);
        if (! $order) {
            return redirect()->to('/pos')->with('error', 'Order not found.');
        }

        $userId = $this->currentUserId();
        $isParty = in_array($userId, [(int) $order['buyer_id'], (int) $order['seller_id']], true);
        if ($this->currentRole() !== 'admin' && ! $isParty) {
            return redirect()->to('/dashboard')->with('error', 'You cannot view this invoice.');
        }

        $data = [
            'title'     => 'Invoice #' . $orderId,
            'order'     => $order,
            'totalPaid' => $this->orders->totalPaid($orderId),
        ];

        return view('pos/invoice', $data);
    }

    /**
     * Manual (non-auction) POS sale form.
     */
    public function createManual()
    {
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'buyer_id' => 'required|integer',
                'amount'   => 'required|decimal|greater_than[0]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $orderId = $this->posService->createManualOrder(
                (int) $this->currentUserId(),
                (int) $this->request->getPost('buyer_id'),
                (float) $this->request->getPost('amount'),
                $this->request->getPost('notes')
            );

            return redirect()->to('/pos/orders/' . $orderId)->with('success', 'Manual sale created.');
        }

        $users = new UserModel();

        return view('pos/create_manual', [
            'title'   => 'New Manual Sale',
            'bidders' => $users->where('role', 'bidder')->findAll(),
        ]);
    }
}
