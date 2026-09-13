<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container my-5" style="max-width:560px;">
    <h1 class="fw-bold mb-4"><i class="bi bi-cash-coin"></i> New Manual Sale</h1>
    <div class="card auth-card p-4">
        <form action="<?= base_url('pos/manual-sale') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Buyer</label>
                <select name="buyer_id" class="form-select" required>
                    <option value="">Select a buyer...</option>
                    <?php foreach ($bidders as $b): ?>
                        <option value="<?= $b['id'] ?>"><?= esc($b['full_name']) ?> (<?= esc($b['username']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Amount</label>
                <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Notes</label>
                <textarea name="notes" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-accent btn-lg w-100 rounded-pill">Create Sale</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
