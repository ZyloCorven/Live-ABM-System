<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container my-5" style="max-width:720px;">
    <h1 class="fw-bold mb-4"><i class="bi bi-pencil-square text-primary"></i> Edit Auction</h1>
    <div class="card auth-card p-4">
        <form action="<?= base_url('auctions/' . $auction['id'] . '/edit') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Title</label>
                <input type="text" name="title" class="form-control" value="<?= old('title', $auction['title']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" class="form-control" rows="4"><?= old('description', $auction['description']) ?></textarea>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Starting Price</label>
                    <input type="number" step="0.01" name="starting_price" class="form-control" value="<?= old('starting_price', $auction['starting_price']) ?>" <?= $auction['status'] !== 'upcoming' ? 'disabled' : '' ?>>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Reserve Price</label>
                    <input type="number" step="0.01" name="reserve_price" class="form-control" value="<?= old('reserve_price', $auction['reserve_price']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Min. Increment</label>
                    <input type="number" step="0.01" name="min_increment" class="form-control" value="<?= old('min_increment', $auction['min_increment']) ?>" required>
                </div>
            </div>
            <?php if ($auction['status'] === 'upcoming'): ?>
            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Start Time</label>
                    <input type="datetime-local" name="start_time" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($auction['start_time'])) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">End Time</label>
                    <input type="datetime-local" name="end_time" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($auction['end_time'])) ?>">
                </div>
            </div>
            <?php else: ?>
                <p class="text-muted small mt-2"><i class="bi bi-info-circle"></i> Timing can only be changed before an auction goes live.</p>
            <?php endif; ?>
            <div class="mb-3 mt-3">
                <label class="form-label fw-semibold">Replace Image</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill mt-2">Save Changes</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
