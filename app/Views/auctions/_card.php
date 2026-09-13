<div class="card auction-card">
    <?php if (! empty($a['image'])): ?>
        <img src="<?= base_url('uploads/' . $a['image']) ?>" class="card-img-top" alt="<?= esc($a['title']) ?>">
    <?php else: ?>
        <div class="card-img-top d-flex align-items-center justify-content-center">
            <i class="bi bi-image text-white" style="font-size:2.5rem;opacity:.6;"></i>
        </div>
    <?php endif; ?>
    <div class="card-body d-flex flex-column">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <h5 class="card-title mb-0"><?= esc($a['title']) ?></h5>
            <span class="status-badge status-<?= esc($a['status']) ?>"><?= esc(strtoupper($a['status'])) ?></span>
        </div>
        <p class="text-muted small mb-2">by <?= esc($a['seller_name'] ?? 'Seller') ?></p>
        <div class="mt-auto">
            <div class="price-tag">
                <?= $a['current_highest_bid'] ? '$' . number_format((float) $a['current_highest_bid'], 2) : '$' . number_format((float) $a['starting_price'], 2) ?>
                <small class="text-muted fs-6 fw-normal"><?= $a['current_highest_bid'] ? 'current bid' : 'starting price' ?></small>
            </div>
            <a href="<?= base_url('auctions/' . $a['id']) ?>" class="btn btn-primary w-100 mt-2 rounded-pill">
                <i class="bi bi-eye"></i> View Auction
            </a>
        </div>
    </div>
</div>
