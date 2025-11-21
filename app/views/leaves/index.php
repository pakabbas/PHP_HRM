<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Leave Requests</h5>
    <div class="d-flex gap-2">
        <a href="<?= route_to('leaves/create') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Apply Leave</a>
        <?php if (in_array($currentUser['role'], ['admin','hr'], true)): ?>
            <a href="<?= route_to('leaves/types') ?>" class="btn btn-outline-primary">Manage Types</a>
        <?php endif; ?>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form class="row g-3" method="get">
            <input type="hidden" name="route" value="leaves/index">
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <?php foreach (['pending','approved','rejected'] as $status): ?>
                        <option value="<?= $status ?>" <?= ($filters['status'] ?? null) === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">From</label>
                <input type="date" name="start" class="form-control" value="<?= htmlspecialchars($filters['start_date'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">To</label>
                <input type="date" name="end" class="form-control" value="<?= htmlspecialchars($filters['end_date'] ?? '') ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button class="btn btn-primary flex-grow-1">Filter</button>
                <a href="<?= route_to('leaves/index') ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th>Employee</th>
                    <th>Type</th>
                    <th>Dates</th>
                    <th>Days</th>
                    <th>Status</th>
                    <th>Remarks</th>
                    <?php if (in_array($currentUser['role'], ['admin','hr','manager'], true)): ?>
                        <th>Action</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($requests)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No leave requests.</td></tr>
                <?php else: ?>
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td><?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?></td>
                            <td><?= htmlspecialchars($request['type_name']) ?></td>
                            <td><?= htmlspecialchars($request['start_date']) ?> → <?= htmlspecialchars($request['end_date']) ?></td>
                            <td><?= htmlspecialchars($request['total_days']) ?></td>
                            <td><span class="badge bg-<?= $request['status'] === 'approved' ? 'success' : ($request['status'] === 'pending' ? 'warning text-dark' : 'secondary') ?>"><?= strtoupper($request['status']) ?></span></td>
                            <td><?= htmlspecialchars($request['remarks']) ?></td>
                            <?php if (in_array($currentUser['role'], ['admin','hr','manager'], true)): ?>
                                <td>
                                    <?php if ($request['status'] === 'pending'): ?>
                                        <div class="d-flex gap-2">
                                            <form method="post" action="<?= route_to('leaves/approve/' . $request['id']) ?>">
                                                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                                <button class="btn btn-success btn-sm">Approve</button>
                                            </form>
                                            <form method="post" action="<?= route_to('leaves/reject/' . $request['id']) ?>">
                                                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                                <input type="hidden" name="remarks" value="Rejected by manager">
                                                <button class="btn btn-outline-danger btn-sm">Reject</button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

