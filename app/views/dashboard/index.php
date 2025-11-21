<?php
$renderCard = function ($title, $value, $icon, $subtext = null) {
    $title = $title;
    $value = $value;
    $icon = $icon;
    $subtext = $subtext;
    include base_path('app/views/components/stat-card.php');
};
?>
<div class="row g-3">
    <div class="col-md-3">
        <?php $renderCard('Total Employees', number_format($stats['employees']), 'bi-people', 'Active workforce'); ?>
    </div>
    <div class="col-md-3">
        <?php $renderCard('Present Today', number_format($stats['attendance_today']), 'bi-check-circle', 'Marked attendance'); ?>
    </div>
    <div class="col-md-3">
        <?php $renderCard('Pending Leaves', number_format($stats['pending_leaves']), 'bi-hourglass-split', 'Awaiting action'); ?>
    </div>
    <div class="col-md-3">
        <?php $renderCard('Payroll Runs', number_format($stats['payroll_processed']), 'bi-wallet2', 'Recent months'); ?>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-lg-7">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Recent Leave Requests</h5>
                    <small class="text-muted">Last 5 submissions</small>
                </div>
                <a href="<?= route_to('leaves/index') ?>" class="btn btn-sm btn-outline-primary">View all</a>
            </div>
            <div class="card-body">
                <?php if (empty($recentLeaves)): ?>
                    <p class="text-muted mb-0">No leave requests yet.</p>
                <?php else: ?>
                    <div class="timeline">
                        <?php foreach ($recentLeaves as $leave): ?>
                            <div class="timeline-item">
                                <div>
                                    <p class="fw-semibold mb-0"><?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?></p>
                                    <small class="text-muted"><?= htmlspecialchars($leave['type_name']) ?> · <?= htmlspecialchars($leave['start_date']) ?> → <?= htmlspecialchars($leave['end_date']) ?></small>
                                </div>
                                <span class="badge rounded-pill bg-<?= $leave['status'] === 'approved' ? 'success' : ($leave['status'] === 'pending' ? 'warning text-dark' : 'danger') ?>">
                                    <?= strtoupper($leave['status']) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Attendance Snapshot</h5>
                <small class="text-muted"><?= date('l, d M Y') ?></small>
            </div>
            <div class="card-body">
                <div class="attendance-progress">
                    <?php foreach ($attendanceSummary as $status => $total): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-capitalize"><?= str_replace('_', ' ', $status) ?></span>
                            <span class="fw-semibold"><?= $total ?></span>
                        </div>
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar bg-<?= $status === 'present' ? 'success' : ($status === 'absent' ? 'danger' : 'info') ?>"
                                 role="progressbar" style="width: <?= $stats['employees'] ? min(100, ($total / max(1,$stats['employees'])) * 100) : 0 ?>%"></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Payroll History</h5>
                    <small class="text-muted">Latest processed payroll</small>
                </div>
                <a href="<?= route_to('payroll/index') ?>" class="btn btn-sm btn-outline-primary">Manage</a>
            </div>
            <div class="card-body table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Period</th>
                            <th>Net Salary</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($payrollHistory)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No payroll records.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($payrollHistory as $payroll): ?>
                                <tr>
                                    <td><?= htmlspecialchars($payroll['first_name'] . ' ' . $payroll['last_name']) ?></td>
                                    <td><?= sprintf('%02d/%d', $payroll['month'], $payroll['year']) ?></td>
                                    <td>$<?= number_format($payroll['net_salary'], 2) ?></td>
                                    <td><span class="badge bg-primary"><?= strtoupper($payroll['status']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="<?= route_to('employees/create') ?>" class="btn btn-primary btn-lg">
                    <i class="bi bi-person-plus me-2"></i> Add Employee
                </a>
                <a href="<?= route_to('attendance/create') ?>" class="btn btn-outline-primary">
                    <i class="bi bi-calendar2-check me-2"></i> Mark Attendance
                </a>
                <a href="<?= route_to('leaves/create') ?>" class="btn btn-outline-primary">
                    <i class="bi bi-umbrella me-2"></i> Apply Leave
                </a>
            </div>
        </div>
    </div>
</div>

<a href="<?= route_to('employees/create') ?>" class="fab-btn" title="Quick add">
    <i class="bi bi-plus-lg"></i>
</a>

