<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container my-5">
    <h1 class="fw-bold mb-4"><i class="bi bi-trophy"></i> My Bidding Activity</h1>

    <h5 class="mb-3">Active Auctions You're Bidding On</h5>
    <?php if (empty($active)): ?>
        <p class="text-muted mb-4">No active bids right now — <a href="<?= base_url('auctions') ?>">browse auctions</a>.</p>
    <?php else: ?>
        <div class="row g-4 mb-4">
            <?php foreach ($active as $a): ?>
                <div class="col-sm-6 col-lg-4"><?= view('auctions/_card', ['a' => $a + ['seller_name' => '']]) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h5 class="mb-3">Auctions You've Won</h5>
    <?php if (empty($won)): ?>
        <p class="text-muted mb-4">Nothing won yet — good luck out there!</p>
    <?php else: ?>
        <div class="row g-4 mb-4">
            <?php foreach ($won as $a): ?>
                <div class="col-sm-6 col-lg-4"><?= view('auctions/_card', ['a' => $a + ['seller_name' => '']]) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h5 class="mb-3">My Orders</h5>
    <?php if (empty($orders)): ?>
        <p class="text-muted">No orders yet.</p>
    <?php else: ?>
        <div class="table-responsive bg-white rounded-4 shadow-sm p-2">
            <table class="table align-middle mb-0">
                <thead><tr><th>Order</th><th>Total</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td>#<?= $o['id'] ?></td>
                        <td>$<?= number_format((float) $o['total_amount'], 2) ?></td>
                        <td><span class="status-badge status-<?= $o['status'] === 'paid' || $o['status'] === 'completed' ? 'sold' : 'upcoming' ?>"><?= esc(strtoupper($o['status'])) ?></span></td>
                        <td><?php if (in_array($o['status'], ['paid', 'shipped', 'completed'])): ?><a href="<?= base_url('pos/orders/' . $o['id'] . '/invoice') ?>" class="btn btn-sm btn-outline-primary">Invoice</a><?php endif; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
