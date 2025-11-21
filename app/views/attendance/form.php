<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Mark Attendance</h5>
    </div>
    <form action="<?= route_to('attendance/store') ?>" method="post" class="card-body">
        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Employee</label>
                <select name="employee_id" class="form-select" required>
                    <option value="">Select employee</option>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Date</label>
                <input type="date" name="attendance_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <?php foreach (['present','absent','leave','half_day','holiday'] as $status): ?>
                        <option value="<?= $status ?>"><?= ucfirst(str_replace('_',' ',$status)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Check In</label>
                <input type="time" name="check_in" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Check Out</label>
                <input type="time" name="check_out" class="form-control">
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="<?= route_to('attendance/index') ?>" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary">Save</button>
        </div>
    </form>
</div>

