<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container my-5 d-flex justify-content-center">
    <div class="card auth-card p-4" style="max-width:420px;width:100%;">
        <div class="text-center mb-4">
            <i class="bi bi-lightning-charge-fill text-warning" style="font-size:2.5rem;"></i>
            <h2 class="fw-bold mt-2">Welcome Back</h2>
            <p class="text-muted">Log in to bid and manage your auctions</p>
        </div>
        <form action="<?= base_url('login') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Username or Email</label>
                <input type="text" name="login" class="form-control form-control-lg" value="<?= old('login') ?>" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" name="password" class="form-control form-control-lg" required>
            </div>
            <button type="submit" class="btn btn-accent btn-lg w-100 rounded-pill">Log In</button>
        </form>
        <p class="text-center text-muted mt-3 mb-0">No account? <a href="<?= base_url('register') ?>">Sign up</a></p>
        <div class="alert alert-light border mt-3 small mb-0">
            <strong>Demo accounts</strong> (password <code>Password123</code>):<br>
            admin · sarah_seller · bella_bidder
        </div>
    </div>
</div>
<?= $this->endSection() ?>
