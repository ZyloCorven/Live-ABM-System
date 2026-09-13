<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container my-5">
    <a href="<?= base_url('pos') ?>" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Back to POS</a>
    <div class="d-flex justify-content-between align-items-start mt-3 mb-4 flex-wrap gap-2">
        <h1 class="fw-bold">Order #<?= $order['id'] ?></h1>
        <span class="status-badge status-<?= in_array($order['status'], ['paid','shipped','completed']) ? 'sold' : 'upcoming' ?>" style="font-size:.9rem;"><?= esc(strtoupper($order['status'])) ?></span>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="pos-screen p-4 mb-4">
                <h5 class="mb-3">Item</h5>
                <p class="fw-semibold mb-1"><?= esc($order['auction_title'] ?? 'Manual Sale') ?></p>
                <p class="text-muted">Buyer: <?= esc($order['buyer_name']) ?> (<?= esc($order['buyer_email']) ?>)</p>
                <p class="text-muted mb-0">Seller: <?= esc($order['seller_name']) ?></p>
                <?php if (! empty($order['notes'])): ?><p class="mt-2 mb-0"><em><?= esc($order['notes']) ?></em></p><?php endif; ?>
            </div>

            <div class="pos-screen p-4">
                <h5 class="mb-3">Payment History</h5>
                <?php if (empty($payments)): ?>
                    <p class="text-muted">No payments recorded yet.</p>
                <?php else: ?>
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Amount</th><th>Method</th><th>Reference</th><th>Date</th></tr></thead>
                        <tbody>
                        <?php foreach ($payments as $p): ?>
                            <tr>
                                <td>$<?= number_format((float) $p['amount'], 2) ?></td>
                                <td class="text-capitalize"><?= esc($p['method']) ?></td>
                                <td><?= esc($p['reference'] ?? '—') ?></td>
                                <td><?= esc(date('M j, g:i A', strtotime($p['paid_at']))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="pos-screen p-4 mb-4 text-center">
                <div class="text-muted small text-uppercase">Total Due</div>
                <div class="highest-bid-display mb-1">$<?= number_format((float) $order['total_amount'], 2) ?></div>
                <div class="text-success fw-semibold">Paid: $<?= number_format($totalPaid, 2) ?></div>
                <div class="fw-semibold <?= $balance > 0 ? 'text-danger' : 'text-success' ?>">Balance: $<?= number_format($balance, 2) ?></div>
            </div>

            <?php if ($balance > 0): ?>
            <div class="pos-screen p-4 mb-4">
                <h5 class="mb-3"><i class="bi bi-cash-stack"></i> Record Payment</h5>
                <form action="<?= base_url('pos/orders/' . $order['id'] . '/payment') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Amount</label>
                        <input type="number" step="0.01" min="0.01" max="<?= $balance ?>" name="amount" class="form-control" value="<?= $balance ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Method</label>
                        <select name="method" class="form-select" required>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="online">Online</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reference (optional)</label>
                        <input type="text" name="reference" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-accent w-100 rounded-pill">Record Payment</button>
                </form>
            </div>
            <?php endif; ?>

            <div class="d-grid gap-2">
                <a href="<?= base_url('pos/orders/' . $order['id'] . '/invoice') ?>" class="btn btn-outline-primary rounded-pill"><i class="bi bi-file-earmark-text"></i> View Invoice</a>
                <?php if ($order['status'] === 'paid'): ?>
                    <form action="<?= base_url('pos/orders/' . $order['id'] . '/ship') ?>" method="post">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill"><i class="bi bi-truck"></i> Mark Shipped</button>
                    </form>
                <?php endif; ?>
                <?php if ($order['status'] === 'shipped'): ?>
                    <form action="<?= base_url('pos/orders/' . $order['id'] . '/complete') ?>" method="post">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill"><i class="bi bi-check2-circle"></i> Mark Completed</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
