<?php
$isEdit = !empty($employee);
$action = $isEdit ? route_to('employees/update/' . $employee['id']) : route_to('employees/store');
?>
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0"><?= $isEdit ? 'Edit Employee' : 'Add Employee' ?></h5>
            <small class="text-muted">Capture personal and job information</small>
        </div>
        <a href="<?= route_to('employees/index') ?>" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
    <form action="<?= $action ?>" method="post" class="card-body">
        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Employee Code</label>
                <input type="text" name="emp_code" class="form-control" value="<?= htmlspecialchars($employee['emp_code'] ?? '') ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($employee['first_name'] ?? '') ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($employee['last_name'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select">
                    <option value="">Select</option>
                    <?php foreach (['male','female','other'] as $gender): ?>
                        <option value="<?= $gender ?>" <?= ($employee['gender'] ?? '') === $gender ? 'selected' : '' ?>><?= ucfirst($gender) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">DOB</label>
                <input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($employee['dob'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">CNIC</label>
                <input type="text" name="cnic" class="form-control" value="<?= htmlspecialchars($employee['cnic'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($employee['phone'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($employee['email'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($employee['address'] ?? '') ?></textarea>
            </div>
            <div class="col-md-3">
                <label class="form-label">City</label>
                <select name="city_id" class="form-select">
                    <option value="">Select</option>
                    <?php foreach ($cities as $city): ?>
                        <option value="<?= $city['id'] ?>" <?= ($employee['city_id'] ?? null) == $city['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($city['city_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-select" required>
                    <option value="">Select</option>
                    <?php foreach ($departments as $department): ?>
                        <option value="<?= $department['id'] ?>" <?= ($employee['department_id'] ?? null) == $department['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($department['department_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Designation</label>
                <select name="designation_id" class="form-select">
                    <option value="">Select</option>
                    <?php foreach ($designations as $designation): ?>
                        <option value="<?= $designation['id'] ?>" <?= ($employee['designation_id'] ?? null) == $designation['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($designation['designation_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Joining Date</label>
                <input type="date" name="joining_date" class="form-control" value="<?= htmlspecialchars($employee['joining_date'] ?? '') ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Leaving Date</label>
                <input type="date" name="leaving_date" class="form-control" value="<?= htmlspecialchars($employee['leaving_date'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <?php foreach (['active','inactive','terminated','resigned'] as $status): ?>
                        <option value="<?= $status ?>" <?= ($employee['status'] ?? 'active') === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-3 mt-4">
            <a href="<?= route_to('employees/index') ?>" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary"><?= $isEdit ? 'Update Employee' : 'Create Employee' ?></button>
        </div>
    </form>
</div>

