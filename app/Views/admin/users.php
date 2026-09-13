<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container my-5">
    <h1 class="fw-bold mb-4"><i class="bi bi-people"></i> Manage Users</h1>
    <div class="table-responsive bg-white rounded-4 shadow-sm p-2">
        <table class="table align-middle mb-0">
            <thead><tr><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= esc($u['full_name']) ?></td>
                    <td><?= esc($u['username']) ?></td>
                    <td><?= esc($u['email']) ?></td>
                    <td>
                        <form action="<?= base_url('admin/users/' . $u['id'] . '/role') ?>" method="post" class="d-flex gap-1">
                            <?= csrf_field() ?>
                            <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                                <?php foreach (['admin', 'seller', 'bidder'] as $r): ?>
                                    <option value="<?= $r ?>" <?= $u['role'] === $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </td>
                    <td><span class="badge <?= $u['is_active'] ? 'bg-success' : 'bg-secondary' ?>"><?= $u['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                    <td>
                        <form action="<?= base_url('admin/users/' . $u['id'] . '/toggle') ?>" method="post">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-outline-secondary"><?= $u['is_active'] ? 'Deactivate' : 'Activate' ?></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
