<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container my-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">Auctions</h1>
        <div class="btn-group flex-wrap" role="group">
            <a href="<?= base_url('auctions') ?>" class="btn btn-sm <?= ! $status ? 'btn-primary' : 'btn-outline-primary' ?>">All</a>
            <?php foreach (['live', 'extended', 'upcoming', 'sold', 'ended'] as $s): ?>
                <a href="<?= base_url('auctions?status=' . $s) ?>" class="btn btn-sm <?= $status === $s ? 'btn-primary' : 'btn-outline-primary' ?>"><?= ucfirst($s) ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (empty($auctions)): ?>
        <div class="text-center py-5">
            <i class="bi bi-inboxes display-1 text-muted"></i>
            <p class="text-muted mt-3">No auctions found.</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($auctions as $a): ?>
                <div class="col-sm-6 col-lg-4">
                    <?= view('auctions/_card', ['a' => $a]) ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-4 d-flex justify-content-center"><?= $pager->links() ?></div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
