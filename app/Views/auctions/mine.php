<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">My Auctions</h1>
        <a href="<?= base_url('auctions/create') ?>" class="btn btn-accent rounded-pill"><i class="bi bi-plus-circle"></i> New Auction</a>
    </div>
    <?php if (empty($auctions)): ?>
        <p class="text-muted">You haven't listed any auctions yet.</p>
    <?php else: ?>
        <div class="table-responsive bg-white rounded-4 shadow-sm p-2">
        <table class="table align-middle mb-0">
            <thead><tr><th>Title</th><th>Status</th><th>Current Bid</th><th>Ends</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($auctions as $a): ?>
                <tr>
                    <td><a href="<?= base_url('auctions/' . $a['id']) ?>" class="text-decoration-none fw-semibold"><?= esc($a['title']) ?></a></td>
                    <td><span class="status-badge status-<?= esc($a['status']) ?>"><?= esc(strtoupper($a['status'])) ?></span></td>
                    <td><?= $a['current_highest_bid'] ? '$' . number_format((float) $a['current_highest_bid'], 2) : '—' ?></td>
                    <td><?= esc(date('M j, g:i A', strtotime($a['end_time']))) ?></td>
                    <td><a href="<?= base_url('auctions/' . $a['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
