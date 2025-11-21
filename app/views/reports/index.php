<?php
$breadcrumbs = [
    ['label' => 'Reports', 'route' => 'reports/index']
];
?>
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form class="row g-3 align-items-end" method="get">
            <input type="hidden" name="route" value="reports/index">
            <div class="col-md-3">
                <label class="form-label">Report Type</label>
                <select name="type" class="form-select">
                    <option value="employees" <?= $type === 'employees' ? 'selected' : '' ?>>Employees</option>
                    <option value="attendance" <?= $type === 'attendance' ? 'selected' : '' ?>>Attendance</option>
                    <option value="leaves" <?= $type === 'leaves' ? 'selected' : '' ?>>Leaves</option>
                    <option value="payroll" <?= $type === 'payroll' ? 'selected' : '' ?>>Payroll</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <input type="text" name="status" class="form-control" value="<?= htmlspecialchars($filters['status'] ?? '') ?>" placeholder="e.g. active">
            </div>
            <div class="col-md-2">
                <label class="form-label">From</label>
                <input type="date" name="start" class="form-control" value="<?= htmlspecialchars($filters['start'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">To</label>
                <input type="date" name="end" class="form-control" value="<?= htmlspecialchars($filters['end'] ?? '') ?>">
            </div>
            <div class="col-md-3 text-end">
                <button class="btn btn-primary"><i class="bi bi-funnel me-1"></i> Apply Filters</button>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0 text-uppercase"><?= htmlspecialchars($type) ?> Report</h5>
    <div class="btn-group">
        <a class="btn btn-outline-primary" href="<?= route_to('reports/export', array_merge($_GET, ['format' => 'excel'])) ?>"><i class="bi bi-file-earmark-spreadsheet me-1"></i> Excel</a>
        <a class="btn btn-primary" href="<?= route_to('reports/export', array_merge($_GET, ['format' => 'pdf'])) ?>"><i class="bi bi-file-pdf me-1"></i> PDF</a>
    </div>
</div>

<?php
$headers = $dataset['headers'] ?? [];
$rows = $dataset['rows'] ?? [];
include base_path('app/views/components/table.php');
?>

