<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$secondsRemaining = max(0, strtotime($auction['end_time']) - time());
$isOwner = session()->get('userId') && (int) session()->get('userId') === (int) $auction['seller_id'];
$canBid = session()->get('isLoggedIn') && ! $isOwner && in_array($auction['status'], ['live', 'extended'], true);
?>

<div class="container my-5" id="bid-app"
     data-auction-id="<?= $auction['id'] ?>"
     data-state-url="<?= base_url('auctions/' . $auction['id'] . '/state') ?>"
     data-bid-url="<?= base_url('auctions/' . $auction['id'] . '/bid') ?>"
     data-csrf-name="<?= csrf_token() ?>"
     data-csrf-hash="<?= csrf_hash() ?>"
     data-seconds-remaining="<?= $secondsRemaining ?>">

    <nav aria-label="breadcrumb" class="mb-3">
        <a href="<?= base_url('auctions') ?>" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Back to auctions</a>
    </nav>

    <div class="row g-4">
        <div class="col-lg-7">
            <?php if (! empty($auction['image'])): ?>
                <img src="<?= base_url('uploads/' . $auction['image']) ?>" class="img-fluid rounded-4 shadow-sm w-100" style="max-height:420px;object-fit:cover;" alt="<?= esc($auction['title']) ?>">
            <?php else: ?>
                <div class="rounded-4 d-flex align-items-center justify-content-center" style="height:320px;background:linear-gradient(135deg,#E9D5FF,#FDE68A);">
                    <i class="bi bi-image text-white" style="font-size:4rem;opacity:.6;"></i>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-start mt-4">
                <h1 class="fw-bold"><?= esc($auction['title']) ?></h1>
                <span id="status-badge" class="status-badge status-<?= esc($auction['status']) ?>"><?= esc(strtoupper($auction['status'])) ?></span>
            </div>
            <p class="text-muted">Sold by <strong><?= esc($auction['seller_name']) ?></strong></p>
            <p><?= nl2br(esc($auction['description'])) ?></p>

            <div class="row text-center g-3 my-3">
                <div class="col-4">
                    <div class="p-3 bg-white rounded-3 shadow-sm">
                        <div class="text-muted small">Starting Price</div>
                        <div class="fw-bold">$<?= number_format((float) $auction['starting_price'], 2) ?></div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 bg-white rounded-3 shadow-sm">
                        <div class="text-muted small">Min. Increment</div>
                        <div class="fw-bold">$<?= number_format((float) $auction['min_increment'], 2) ?></div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 bg-white rounded-3 shadow-sm">
                        <div class="text-muted small">Reserve Price</div>
                        <div class="fw-bold"><?= $auction['reserve_price'] ? '$' . number_format((float) $auction['reserve_price'], 2) : 'None' ?></div>
                    </div>
                </div>
            </div>

            <h5 class="mt-4 mb-3"><i class="bi bi-clock-history"></i> Bid History</h5>
            <div id="bid-history">
                <?php if (empty($history)): ?>
                    <p class="text-muted">No bids yet — be the first!</p>
                <?php else: ?>
                    <?php foreach ($history as $i => $b): ?>
                        <div class="bid-history-item<?= $i === 0 ? ' leading' : '' ?>">
                            <strong><?= esc($b['full_name'] ?? $b['username']) ?></strong> bid $<?= number_format((float) $b['amount'], 2) ?>
                            <div class="text-muted small"><?= esc(date('g:i:s A', strtotime($b['created_at']))) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="bid-panel p-4 sticky-top" style="top:90px;">
                <div class="text-center mb-3">
                    <div class="text-muted small text-uppercase">Current Highest Bid</div>
                    <div class="highest-bid-display" id="highest-bid-amount">
                        $<?= number_format((float) ($auction['current_highest_bid'] ?? $auction['starting_price']), 2) ?>
                    </div>
                    <div class="text-muted small">Leader: <span id="leader-initials" class="fw-semibold">—</span></div>
                </div>

                <div class="text-center mb-4">
                    <div class="text-muted small text-uppercase">Time Remaining</div>
                    <div class="countdown <?= $secondsRemaining <= 300 ? 'urgent' : '' ?>" id="countdown">
                        <?= $secondsRemaining > 0 ? gmdate('H\h i\m s\s', $secondsRemaining) : 'Ended' ?>
                    </div>
                    <div id="extension-note" class="alert alert-warning py-1 px-2 small mt-2 d-none">
                        <i class="bi bi-hourglass-split"></i> Auction extended due to a last-minute bid!
                    </div>
                </div>

                <?php if ($canBid): ?>
                    <form id="bid-form">
                        <label class="form-label fw-semibold">Your Bid (min $<?= number_format($minNextBid, 2) ?>)</label>
                        <div class="input-group input-group-lg mb-2">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="<?= $minNextBid ?>" class="form-control" id="bid-amount" value="<?= $minNextBid ?>" required>
                        </div>
                        <button type="submit" class="btn btn-accent btn-lg w-100 rounded-pill">
                            <i class="bi bi-lightning-charge-fill"></i> Place Bid
                        </button>
                        <div id="bid-feedback" class="mt-2"></div>
                    </form>
                <?php elseif (! session()->get('isLoggedIn')): ?>
                    <a href="<?= base_url('login') ?>" class="btn btn-primary w-100 rounded-pill">Log in to bid</a>
                <?php elseif ($isOwner): ?>
                    <div class="alert alert-info mb-0">This is your auction — you can't bid on it.</div>
                <?php else: ?>
                    <div class="alert alert-secondary mb-0">Bidding is closed for this auction.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/bid.js') ?>"></script>
<?= $this->endSection() ?>
