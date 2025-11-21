<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Salary Settings</h5>
            </div>
            <div class="card-body">
                <form class="row g-3" method="post" action="<?= route_to('payroll/saveSetting') ?>">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    <div class="col-12">
                        <label class="form-label">Employee</label>
                        <select name="employee_id" class="form-select" required>
                            <?php foreach ($employees as $employee): ?>
                                <option value="<?= $employee['id'] ?>"><?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Basic Salary</label>
                        <input type="number" name="basic_salary" class="form-control" min="0" step="0.01" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Overtime Rate</label>
                        <input type="number" name="overtime_rate" class="form-control" min="0" step="0.01" value="0">
                    </div>
                    <div class="col-12 text-end">
                        <button class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white"><h5 class="mb-0">Allowances & Deductions</h5></div>
            <div class="card-body">
                <form class="row g-3" method="post" action="<?= route_to('payroll/saveComponent') ?>">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    <div class="col-12">
                        <label class="form-label">Employee</label>
                        <select name="employee_id" class="form-select" required>
                            <?php foreach ($employees as $employee): ?>
                                <option value="<?= $employee['id'] ?>"><?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Amount</label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Type</label>
                        <select name="component_type" class="form-select">
                            <option value="allowance">Allowance</option>
                            <option value="deduction">Deduction</option>
                        </select>
                    </div>
                    <div class="col-12 text-end">
                        <button class="btn btn-primary">Save Component</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white"><h5 class="mb-0">Loans</h5></div>
            <div class="card-body">
                <form class="row g-3" method="post" action="<?= route_to('payroll/saveLoan') ?>">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    <div class="col-md-6">
                        <label class="form-label">Employee</label>
                        <select name="employee_id" class="form-select" required>
                            <?php foreach ($employees as $employee): ?>
                                <option value="<?= $employee['id'] ?>"><?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Amount</label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Installment</label>
                        <input type="number" name="installment" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="col-12 text-end">
                        <button class="btn btn-primary">Save Loan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white"><h5 class="mb-0">Run Payroll</h5></div>
            <div class="card-body">
                <form class="row g-3" method="post" action="<?= route_to('payroll/run') ?>">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    <div class="col-md-5">
                        <label class="form-label">Employee</label>
                        <select name="employee_id" class="form-select" required>
                            <?php foreach ($employees as $employee): ?>
                                <option value="<?= $employee['id'] ?>"><?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Month</label>
                        <select name="month" class="form-select">
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= $m == date('n') ? 'selected' : '' ?>><?= date('F', mktime(0,0,0,$m,1)) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Year</label>
                        <input type="number" name="year" class="form-control" value="<?= date('Y') ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-success w-100">Run</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Salary Overview</h5>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Employee</th>
                    <th>Basic Salary</th>
                    <th>OT Rate</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($settings)): ?>
                    <tr><td colspan="3" class="text-center text-muted py-4">No salary settings saved.</td></tr>
                <?php else: ?>
                    <?php foreach ($settings as $setting): ?>
                        <tr>
                            <td><?= htmlspecialchars($setting['first_name'] . ' ' . $setting['last_name']) ?></td>
                            <td>$<?= number_format($setting['basic_salary'], 2) ?></td>
                            <td>$<?= number_format($setting['overtime_rate'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Recent Payroll Runs</h5>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Employee</th>
                    <th>Period</th>
                    <th>Gross</th>
                    <th>Allowances</th>
                    <th>Deductions</th>
                    <th>Net</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($history)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No payroll history.</td></tr>
                <?php else: ?>
                    <?php foreach ($history as $run): ?>
                        <tr>
                            <td><?= htmlspecialchars($run['first_name'] . ' ' . $run['last_name']) ?></td>
                            <td><?= sprintf('%02d/%d', $run['month'], $run['year']) ?></td>
                            <td>$<?= number_format($run['gross_salary'], 2) ?></td>
                            <td>$<?= number_format($run['total_allowances'], 2) ?></td>
                            <td>$<?= number_format($run['total_deductions'] + $run['loan_deduction'] + $run['unpaid_leave_deductions'], 2) ?></td>
                            <td class="fw-semibold">$<?= number_format($run['net_salary'], 2) ?></td>
                            <td><span class="badge bg-primary"><?= strtoupper($run['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

