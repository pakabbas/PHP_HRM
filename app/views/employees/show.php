<?php
$deptMap = array_column($departments, 'department_name', 'id');
$desgMap = array_column($designations, 'designation_name', 'id');
$cityMap = array_column($cities, 'city_name', 'id');
$breadcrumbs = [
    ['label' => 'Employees', 'route' => 'employees/index'],
    ['label' => htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']), 'route' => '']
];
?>
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0"><?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></h5>
            <small class="text-muted">Employee Code · <?= htmlspecialchars($employee['emp_code']) ?></small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= route_to('employees/edit/' . $employee['id']) ?>" class="btn btn-outline-primary btn-sm">Edit</a>
            <a href="<?= route_to('employees/index') ?>" class="btn btn-outline-secondary btn-sm">Back</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-6">
                <h6 class="text-uppercase text-muted">Personal Info</h6>
                <ul class="list-unstyled mb-0">
                    <li><strong>Gender:</strong> <?= htmlspecialchars($employee['gender']) ?></li>
                    <li><strong>Date of Birth:</strong> <?= htmlspecialchars($employee['dob']) ?></li>
                    <li><strong>Phone:</strong> <?= htmlspecialchars($employee['phone']) ?></li>
                    <li><strong>Email:</strong> <?= htmlspecialchars($employee['email']) ?></li>
                    <li><strong>Address:</strong> <?= htmlspecialchars($employee['address']) ?></li>
                    <li><strong>City:</strong> <?= htmlspecialchars($cityMap[$employee['city_id']] ?? '-') ?></li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6 class="text-uppercase text-muted">Job Info</h6>
                <ul class="list-unstyled mb-0">
                    <li><strong>Department:</strong> <?= htmlspecialchars($deptMap[$employee['department_id']] ?? '-') ?></li>
                    <li><strong>Designation:</strong> <?= htmlspecialchars($desgMap[$employee['designation_id']] ?? '-') ?></li>
                    <li><strong>Joining Date:</strong> <?= htmlspecialchars($employee['joining_date']) ?></li>
                    <li><strong>Status:</strong> <span class="badge bg-<?= $employee['status'] === 'active' ? 'success' : 'secondary' ?>"><?= ucfirst($employee['status']) ?></span></li>
                    <li><strong>Leaving Date:</strong> <?= htmlspecialchars($employee['leaving_date'] ?? '-') ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>

