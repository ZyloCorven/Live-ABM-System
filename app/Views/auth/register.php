<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container my-5 d-flex justify-content-center">
    <div class="card auth-card p-4" style="max-width:460px;width:100%;">
        <div class="text-center mb-4">
            <i class="bi bi-person-plus-fill text-primary" style="font-size:2.5rem;"></i>
            <h2 class="fw-bold mt-2">Create Your Account</h2>
        </div>
        <form action="<?= base_url('register') ?>" method="post">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?= old('full_name') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Username</label>
                    <input type="text" name="username" class="form-control" value="<?= old('username') ?>" required>
                </div>
            </div>
            <div class="mb-3 mt-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= old('phone') ?>">
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Confirm Password</label>
                    <input type="password" name="password_confirm" class="form-control" required>
                </div>
            </div>
            <div class="mb-3 mt-3">
                <label class="form-label fw-semibold">I want to...</label>
                <select name="role" class="form-select" required>
                    <option value="bidder">Bid on auctions</option>
                    <option value="seller">Sell items (create auctions)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-accent btn-lg w-100 rounded-pill mt-2">Create Account</button>
        </form>
        <p class="text-center text-muted mt-3 mb-0">Already have an account? <a href="<?= base_url('login') ?>">Log in</a></p>
    </div>
</div>
<?= $this->endSection() ?>
