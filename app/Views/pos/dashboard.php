<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold"><i class="bi bi-credit-card-2-front"></i> Point of Sale</h1>
        <a href="<?= base_url('pos/manual-sale') ?>" class="btn btn-accent rounded-pill"><i class="bi bi-plus-circle"></i> New Manual Sale</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card grad-purple"><i class="bi bi-cash-coin stat-icon"></i>
                <div class="stat-value">$<?= number_format($stats['total_sales'], 0) ?></div><div>Total Sales</div></div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card grad-orange"><i class="bi bi-hourglass-split stat-icon"></i>
                <div class="stat-value"><?= $stats['pending'] ?></div><div>Pending Payments</div></div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card grad-green"><i class="bi bi-check2-circle stat-icon"></i>
                <div class="stat-value"><?= $stats['completed'] ?></div><div>Completed</div></div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card grad-blue"><i class="bi bi-receipt stat-icon"></i>
                <div class="stat-value"><?= $stats['total_orders'] ?></div><div>Total Orders</div></div>
        </div>
    </div>

    <div class="pos-screen p-3">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Order</th><th>Item</th><th>Buyer</th><th>Amount</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td>#<?= $o['id'] ?></td>
                        <td><?= esc($o['auction_title'] ?? 'Manual Sale') ?></td>
                        <td><?= esc($o['buyer_name']) ?></td>
                        <td>$<?= number_format((float) $o['total_amount'], 2) ?></td>
                        <td><span class="status-badge status-<?= in_array($o['status'], ['paid', 'shipped', 'completed']) ? 'sold' : 'upcoming' ?>"><?= esc(strtoupper($o['status'])) ?></span></td>
                        <td><a href="<?= base_url('pos/orders/' . $o['id']) ?>" class="btn btn-sm btn-primary rounded-pill">Open</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No orders yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3 d-flex justify-content-center"><?= $pager->links() ?></div>
</div>
<?= $this->endSection() ?>
