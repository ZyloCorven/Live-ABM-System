<?= $this->extend('layouts/main') ?>
<?= $this->section('head') ?>
<style>
@media print { .no-print { display: none !important; } body { background: #fff; } }
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="container my-5" style="max-width:760px;">
    <div class="no-print mb-3 d-flex justify-content-between">
        <a href="<?= base_url('pos/orders/' . $order['id']) ?>" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Back</a>
        <button onclick="window.print()" class="btn btn-primary rounded-pill btn-sm"><i class="bi bi-printer"></i> Print / Save PDF</button>
    </div>

    <div class="invoice-box p-5">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h2 class="fw-bold mb-0"><i class="bi bi-lightning-charge-fill text-warning"></i> AuctionHub</h2>
                <p class="text-muted mb-0">Invoice #<?= str_pad((string) $order['id'], 6, '0', STR_PAD_LEFT) ?></p>
            </div>
            <div class="text-end">
                <span class="status-badge status-<?= in_array($order['status'], ['paid','shipped','completed']) ? 'sold' : 'upcoming' ?>"><?= esc(strtoupper($order['status'])) ?></span>
                <p class="text-muted small mt-1 mb-0">Issued: <?= esc(date('F j, Y', strtotime($order['created_at']))) ?></p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <div class="text-muted small text-uppercase">Billed To</div>
                <div class="fw-semibold"><?= esc($order['buyer_name']) ?></div>
                <div class="text-muted"><?= esc($order['buyer_email']) ?></div>
            </div>
            <div class="col-6 text-end">
                <div class="text-muted small text-uppercase">Sold By</div>
                <div class="fw-semibold"><?= esc($order['seller_name']) ?></div>
            </div>
        </div>

        <table class="table">
            <thead><tr><th>Description</th><th class="text-end">Amount</th></tr></thead>
            <tbody>
                <tr>
                    <td><?= esc($order['auction_title'] ?? 'Manual Sale') ?></td>
                    <td class="text-end">$<?= number_format((float) $order['total_amount'], 2) ?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th class="text-end">$<?= number_format((float) $order['total_amount'], 2) ?></th>
                </tr>
                <tr>
                    <td class="text-muted">Amount Paid</td>
                    <td class="text-end text-success">$<?= number_format($totalPaid, 2) ?></td>
                </tr>
                <tr>
                    <td class="fw-bold">Balance Due</td>
                    <td class="text-end fw-bold">$<?= number_format((float) $order['total_amount'] - $totalPaid, 2) ?></td>
                </tr>
            </tfoot>
        </table>

        <p class="text-center text-muted small mt-5 mb-0">Thank you for using AuctionHub!</p>
    </div>
</div>
<?= $this->endSection() ?>
