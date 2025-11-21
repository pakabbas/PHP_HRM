<?php
$breadcrumbs = [
    ['label' => 'User Accounts', 'route' => 'users/index']
];
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">User Accounts</h5>
    <a href="<?= route_to('users/create') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add User</a>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form class="row g-3" method="get">
            <input type="hidden" name="route" value="users/index">
            <div class="col-md-6">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Username or employee" value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary me-2">Filter</button>
                <a href="<?= route_to('users/index') ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Username</th>
                    <th>Employee</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No users found.</td></tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: '-') ?></td>
                            <td><span class="badge bg-primary text-uppercase"><?= htmlspecialchars($user['role']) ?></span></td>
                            <td><span class="badge bg-<?= $user['status'] ? 'success' : 'secondary' ?>"><?= $user['status'] ? 'Active' : 'Disabled' ?></span></td>
                            <td class="text-end">
                                <a href="<?= route_to('users/edit/' . $user['id']) ?>" class="btn btn-outline-primary btn-sm">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

