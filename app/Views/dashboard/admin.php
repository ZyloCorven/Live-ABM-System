<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container my-5">
    <h1 class="fw-bold mb-4"><i class="bi bi-speedometer2"></i> Admin Dashboard</h1>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card grad-purple"><i class="bi bi-people stat-icon"></i>
                <div class="stat-value"><?= $stats['total_users'] ?></div><div>Total Users</div></div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card grad-orange"><i class="bi bi-hammer stat-icon"></i>
                <div class="stat-value"><?= $stats['total_auctions'] ?></div><div>Total Auctions</div></div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card grad-green"><i class="bi bi-broadcast stat-icon"></i>
                <div class="stat-value"><?= $stats['live_auctions'] ?></div><div>Live Now</div></div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card grad-blue"><i class="bi bi-currency-dollar stat-icon"></i>
                <div class="stat-value">$<?= number_format($sales['total_sales'], 0) ?></div><div>Total Sales</div></div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4"><div class="p-3 bg-white rounded-3 shadow-sm text-center"><div class="text-muted small">Pending Payments</div><div class="fs-4 fw-bold text-warning"><?= $sales['pending'] ?></div></div></div>
        <div class="col-md-4"><div class="p-3 bg-white rounded-3 shadow-sm text-center"><div class="text-muted small">Completed Sales</div><div class="fs-4 fw-bold text-success"><?= $sales['completed'] ?></div></div></div>
        <div class="col-md-4"><div class="p-3 bg-white rounded-3 shadow-sm text-center"><div class="text-muted small">Total Bids</div><div class="fs-4 fw-bold text-primary"><?= $stats['total_bids'] ?></div></div></div>
    </div>

    <div class="d-flex gap-2 mb-4">
        <a href="<?= base_url('admin/auctions') ?>" class="btn btn-primary rounded-pill"><i class="bi bi-hammer"></i> Manage Auctions</a>
        <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-primary rounded-pill"><i class="bi bi-people"></i> Manage Users</a>
        <a href="<?= base_url('admin/settings') ?>" class="btn btn-outline-primary rounded-pill"><i class="bi bi-gear"></i> Settings</a>
        <a href="<?= base_url('pos') ?>" class="btn btn-outline-primary rounded-pill"><i class="bi bi-credit-card"></i> POS</a>
    </div>

    <h5 class="mb-3">Recent Auctions</h5>
    <div class="table-responsive bg-white rounded-4 shadow-sm p-2">
        <table class="table align-middle mb-0">
            <thead><tr><th>Title</th><th>Status</th><th>Current Bid</th><th>Ends</th></tr></thead>
            <tbody>
            <?php foreach ($recentAuctions as $a): ?>
                <tr>
                    <td><a href="<?= base_url('auctions/' . $a['id']) ?>" class="text-decoration-none"><?= esc($a['title']) ?></a></td>
                    <td><span class="status-badge status-<?= esc($a['status']) ?>"><?= esc(strtoupper($a['status'])) ?></span></td>
                    <td><?= $a['current_highest_bid'] ? '$' . number_format((float) $a['current_highest_bid'], 2) : '—' ?></td>
                    <td><?= esc(date('M j, g:i A', strtotime($a['end_time']))) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
