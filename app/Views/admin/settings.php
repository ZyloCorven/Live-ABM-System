<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container my-5" style="max-width:560px;">
    <h1 class="fw-bold mb-4"><i class="bi bi-gear"></i> Auction Settings</h1>
    <?php
        $map = [];
        foreach ($settings as $s) { $map[$s['setting_key']] = $s['setting_value']; }
    ?>
    <div class="card auth-card p-4">
        <form action="<?= base_url('admin/settings') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Extension Window (minutes)</label>
                <input type="number" min="1" name="extension_window_minutes" class="form-control" value="<?= esc($map['extension_window_minutes'] ?? 5) ?>">
                <div class="form-text">If a bid lands within this many minutes of the end time, the auction is extended.</div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Extension Duration (minutes)</label>
                <input type="number" min="1" name="extension_duration_minutes" class="form-control" value="<?= esc($map['extension_duration_minutes'] ?? 3) ?>">
                <div class="form-text">How many minutes to add to the clock on each extension.</div>
            </div>
            <button type="submit" class="btn btn-accent btn-lg w-100 rounded-pill">Save Settings</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
