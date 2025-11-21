<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Apply for Leave</h5>
            <small class="text-muted">Submit leave request for approval</small>
        </div>
        <a href="<?= route_to('leaves/index') ?>" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
    <form action="<?= route_to('leaves/store') ?>" method="post" class="card-body">
        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
        <?php if (in_array($currentUser['role'], ['admin','hr','manager'], true)): ?>
            <div class="mb-3">
                <label class="form-label">Employee</label>
                <select name="employee_id" class="form-select" required>
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?= $employee['id'] ?>"><?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Leave Type</label>
                <select name="leave_type_id" class="form-select" required>
                    <?php foreach ($leaveTypes as $type): ?>
                        <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['type_name']) ?> (<?= $type['default_days'] ?> days)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
        </div>
        <div class="mt-3">
            <label class="form-label">Remarks</label>
            <textarea name="remarks" class="form-control" rows="3" placeholder="Add context for approver"></textarea>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="<?= route_to('leaves/index') ?>" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary">Submit Request</button>
        </div>
    </form>
</div>

