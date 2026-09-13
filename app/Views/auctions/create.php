<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container my-5" style="max-width:720px;">
    <h1 class="fw-bold mb-4"><i class="bi bi-plus-circle text-primary"></i> Create Auction</h1>
    <div class="card auth-card p-4">
        <form action="<?= base_url('auctions') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Title</label>
                <input type="text" name="title" class="form-control" value="<?= old('title') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" class="form-control" rows="4"><?= old('description') ?></textarea>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Starting Price</label>
                    <input type="number" step="0.01" min="0.01" name="starting_price" class="form-control" value="<?= old('starting_price') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Reserve Price</label>
                    <input type="number" step="0.01" min="0" name="reserve_price" class="form-control" value="<?= old('reserve_price') ?>" placeholder="Optional">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Min. Increment</label>
                    <input type="number" step="0.01" min="0.01" name="min_increment" class="form-control" value="<?= old('min_increment', '5.00') ?>" required>
                </div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Start Time</label>
                    <input type="datetime-local" name="start_time" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">End Time</label>
                    <input type="datetime-local" name="end_time" class="form-control" required>
                </div>
            </div>
            <div class="mb-3 mt-3">
                <label class="form-label fw-semibold">Image</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <button type="submit" class="btn btn-accent btn-lg w-100 rounded-pill mt-2">
                <i class="bi bi-hammer"></i> Launch Auction
            </button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
