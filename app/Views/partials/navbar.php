<?php $role = session()->get('role'); $loggedIn = session()->get('isLoggedIn'); ?>
<nav class="navbar navbar-expand-lg navbar-dark app-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= base_url('/') ?>">
            <i class="bi bi-lightning-charge-fill text-warning"></i> Auction<span class="text-warning">Hub</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="<?= base_url('auctions') ?>"><i class="bi bi-hammer"></i> Auctions</a></li>
                <?php if ($loggedIn): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('dashboard') ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <?php if (in_array($role, ['seller', 'admin'], true)): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('auctions/create') ?>"><i class="bi bi-plus-circle"></i> New Auction</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('pos') ?>"><i class="bi bi-credit-card"></i> POS</a></li>
                    <?php endif; ?>
                    <?php if ($role === 'admin'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-shield-lock"></i> Admin</a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="<?= base_url('admin/auctions') ?>">All Auctions</a></li>
                                <li><a class="dropdown-item" href="<?= base_url('admin/users') ?>">Users</a></li>
                                <li><a class="dropdown-item" href="<?= base_url('admin/settings') ?>">Settings</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if ($loggedIn): ?>
                    <li class="nav-item d-flex align-items-center me-2">
                        <span class="badge role-badge role-<?= esc($role) ?>"><?= esc(ucfirst($role)) ?></span>
                    </li>
                    <li class="nav-item"><span class="nav-link text-white-50">Hi, <?= esc(session()->get('fullName')) ?></span></li>
                    <li class="nav-item"><a class="btn btn-warning btn-sm fw-semibold ms-2" href="<?= base_url('logout') ?>">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('login') ?>">Login</a></li>
                    <li class="nav-item"><a class="btn btn-warning btn-sm fw-semibold ms-2" href="<?= base_url('register') ?>">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
