<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold"><i class="bi bi-shop"></i> Seller Dashboard</h1>
        <a href="<?= base_url('auctions/create') ?>" class="btn btn-accent rounded-pill"><i class="bi bi-plus-circle"></i> New Auction</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="stat-card grad-purple"><i class="bi bi-cash-stack stat-icon"></i>
                <div class="stat-value">$<?= number_format($sales['total_sales'], 0) ?></div><div>Total Sales</div></div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card grad-orange"><i class="bi bi-hourglass-split stat-icon"></i>
                <div class="stat-value"><?= $sales['pending'] ?></div><div>Pending Payments</div></div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card grad-green"><i class="bi bi-check-circle stat-icon"></i>
                <div class="stat-value"><?= $sales['completed'] ?></div><div>Completed</div></div>
        </div>
    </div>

    <div class="d-flex gap-2 mb-4">
        <a href="<?= base_url('my-auctions') ?>" class="btn btn-outline-primary rounded-pill">All My Auctions</a>
        <a href="<?= base_url('pos') ?>" class="btn btn-outline-primary rounded-pill"><i class="bi bi-credit-card"></i> POS Dashboard</a>
    </div>

    <h5 class="mb-3">My Auctions</h5>
    <?php if (empty($auctions)): ?>
        <p class="text-muted">You haven't listed any auctions yet.</p>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach (array_slice($auctions, 0, 6) as $a): ?>
                <div class="col-sm-6 col-lg-4"><?= view('auctions/_card', ['a' => $a + ['seller_name' => 'You']]) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
