<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="hero text-center">
    <div class="container">
        <h1>Bid Bold. Win Big. <span class="text-warning">Live.</span></h1>
        <p class="lead mt-3 mb-4" style="max-width:640px;margin-inline:auto;">
            Real-time auctions with instant bid tracking, automatic last-minute extensions,
            and a seamless checkout — from hammer to invoice.
        </p>
        <a href="<?= base_url('auctions') ?>" class="btn btn-accent btn-lg rounded-pill">
            <i class="bi bi-hammer"></i> Browse Live Auctions
        </a>
        <?php if (! session()->get('isLoggedIn')): ?>
            <a href="<?= base_url('register') ?>" class="btn btn-outline-light btn-lg rounded-pill ms-2">Get Started</a>
        <?php endif; ?>
    </div>
</section>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-display fw-bold">🔥 Live Right Now</h2>
        <a href="<?= base_url('auctions') ?>" class="btn btn-sm btn-outline-primary rounded-pill">View all</a>
    </div>

    <?php if (empty($liveAuctions)): ?>
        <p class="text-muted">No live auctions at the moment — check back soon!</p>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($liveAuctions as $a): ?>
                <div class="col-sm-6 col-lg-4">
                    <?= view('auctions/_card', ['a' => $a]) ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
