<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container my-5">
    <h1 class="fw-bold mb-4"><i class="bi bi-hammer"></i> All Auctions</h1>
    <div class="table-responsive bg-white rounded-4 shadow-sm p-2">
        <table class="table align-middle mb-0">
            <thead><tr><th>Title</th><th>Seller</th><th>Status</th><th>Current Bid</th><th>Ends</th><th>Extensions</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($auctions as $a): ?>
                <tr>
                    <td><a href="<?= base_url('auctions/' . $a['id']) ?>" class="text-decoration-none"><?= esc($a['title']) ?></a></td>
                    <td><?= esc($a['seller_name']) ?></td>
                    <td><span class="status-badge status-<?= esc($a['status']) ?>"><?= esc(strtoupper($a['status'])) ?></span></td>
                    <td><?= $a['current_highest_bid'] ? '$' . number_format((float) $a['current_highest_bid'], 2) : '—' ?></td>
                    <td><?= esc(date('M j, g:i A', strtotime($a['end_time']))) ?></td>
                    <td><?= (int) $a['extension_count'] ?></td>
                    <td class="d-flex gap-1">
                        <?php if (in_array($a['status'], ['live', 'extended'], true)): ?>
                            <form action="<?= base_url('admin/auctions/' . $a['id'] . '/force-end') ?>" method="post">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger">Force End</button>
                            </form>
                        <?php endif; ?>
                        <?php if (! in_array($a['status'], ['sold', 'cancelled'], true)): ?>
                            <form action="<?= base_url('admin/auctions/' . $a['id'] . '/cancel') ?>" method="post">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-secondary">Cancel</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
