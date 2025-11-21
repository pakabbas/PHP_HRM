<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form class="row g-3 align-items-end" method="get">
            <input type="hidden" name="route" value="employees/index">
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name or code" value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-select">
                    <option value="">All</option>
                    <?php foreach ($departments as $department): ?>
                        <option value="<?= $department['id'] ?>" <?= ($filters['department_id'] ?? null) == $department['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($department['department_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?= $status ?>" <?= ($filters['status'] ?? null) === $status ? 'selected' : '' ?>>
                            <?= ucfirst($status) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i> Filter</button>
                <a href="<?= route_to('employees/index') ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body table-responsive">
        <table class="table align-middle">
            <thead class="table-light text-uppercase small">
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($employees)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No employees found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($employees as $employee): ?>
                        <tr>
                            <td class="fw-semibold"><?= htmlspecialchars($employee['emp_code']) ?></td>
                            <td><?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></td>
                            <td><?= htmlspecialchars($employee['department_name']) ?></td>
                            <td><?= htmlspecialchars($employee['designation_name']) ?></td>
                            <td><span class="badge bg-<?= $employee['status'] === 'active' ? 'success' : 'secondary' ?>"><?= ucfirst($employee['status']) ?></span></td>
                            <td><?= htmlspecialchars($employee['joining_date']) ?></td>
                            <td class="text-end">
                                <a href="<?= route_to('employees/show/' . $employee['id']) ?>" class="btn btn-sm btn-outline-primary">View</a>
                                <a href="<?= route_to('employees/edit/' . $employee['id']) ?>" class="btn btn-sm btn-primary">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">Showing page <?= $paginator['page'] ?> of <?= $paginator['total_pages'] ?></small>
        <div class="btn-group">
            <?php if ($paginator['has_prev']): ?>
                <a class="btn btn-outline-secondary btn-sm" href="<?= route_to('employees/index', array_merge($_GET, ['page' => $paginator['page'] - 1])) ?>">Prev</a>
            <?php endif; ?>
            <?php if ($paginator['has_next']): ?>
                <a class="btn btn-outline-secondary btn-sm" href="<?= route_to('employees/index', array_merge($_GET, ['page' => $paginator['page'] + 1])) ?>">Next</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<a href="<?= route_to('employees/create') ?>" class="fab-btn">
    <i class="bi bi-plus-lg"></i>
</a>

