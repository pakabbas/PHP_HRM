<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form class="row g-3" method="get">
            <input type="hidden" name="route" value="attendance/index">
            <div class="col-md-3">
                <label class="form-label">From</label>
                <input type="date" name="start" class="form-control" value="<?= htmlspecialchars($start) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">To</label>
                <input type="date" name="end" class="form-control" value="<?= htmlspecialchars($end) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <?php foreach (['present','absent','leave','half_day','holiday'] as $status): ?>
                        <option value="<?= $status ?>" <?= ($filters['status'] ?? null) === $status ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$status)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button class="btn btn-primary flex-grow-1">Apply</button>
                <a href="<?= route_to('attendance/index') ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Attendance Records</h5>
                <?php if (in_array($currentUser['role'], ['admin','hr','manager'], true)): ?>
                    <a href="<?= route_to('attendance/create') ?>" class="btn btn-outline-primary btn-sm">Mark Attendance</a>
                <?php endif; ?>
            </div>
            <div class="card-body table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Employee</th>
                            <th>Status</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">No attendance found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($records as $record): ?>
                                <tr>
                                    <td><?= htmlspecialchars($record['attendance_date']) ?></td>
                                    <td><?= htmlspecialchars($record['first_name'] . ' ' . $record['last_name']) ?></td>
                                    <td><span class="badge bg-<?= $record['status'] === 'present' ? 'success' : 'secondary' ?>"><?= strtoupper(str_replace('_',' ',$record['status'])) ?></span></td>
                                    <td><?= htmlspecialchars($record['check_in']) ?></td>
                                    <td><?= htmlspecialchars($record['check_out']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0">Today Summary</h6>
            </div>
            <div class="card-body">
                <?php foreach ($summary as $status => $count): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-capitalize"><?= str_replace('_',' ',$status) ?></span>
                        <strong><?= $count ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (in_array($currentUser['role'], ['admin','hr'], true)): ?>
                <div class="card-footer bg-light">
                    <form action="<?= route_to('attendance/import') ?>" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <label class="form-label">Import CSV</label>
                        <input type="file" name="import_file" class="form-control mb-2" accept=".csv" required>
                        <button class="btn btn-primary w-100 btn-sm">Upload</button>
                    </form>
                    <small class="text-muted d-block mt-2">Columns: employee_id, date(YYYY-MM-DD), status, check_in, check_out</small>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

