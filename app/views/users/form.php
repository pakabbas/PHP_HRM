<?php
$isEdit = !empty($user);
$action = $isEdit ? route_to('users/update/' . $user['id']) : route_to('users/store');
?>
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><?= $isEdit ? 'Edit User' : 'Create User' ?></h5>
        <a href="<?= route_to('users/index') ?>" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
    <form action="<?= $action ?>" method="post" class="card-body">
        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Employee</label>
                <select name="employee_id" class="form-select">
                    <option value="">System Role</option>
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?= $employee['id'] ?>" <?= ($user['employee_id'] ?? null) == $employee['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role ?>" <?= ($user['role'] ?? 'employee') === $role ? 'selected' : '' ?>><?= ucfirst($role) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Password <?= $isEdit ? '(leave blank to keep)' : '' ?></label>
                <input type="password" name="password" class="form-control" <?= $isEdit ? '' : 'required' ?>>
            </div>
            <div class="col-md-2 d-flex align-items-center">
                <div class="form-check form-switch mt-4">
                    <input class="form-check-input" type="checkbox" name="status" value="1" <?= ($user['status'] ?? 1) ? 'checked' : '' ?>>
                    <label class="form-check-label">Active</label>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="<?= route_to('users/index') ?>" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary"><?= $isEdit ? 'Update User' : 'Create User' ?></button>
        </div>
    </form>
</div>

