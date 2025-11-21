<?php
$configuredEmployees = count($settings);
$totalEmployees = count($employees);
$historyCount = count($history);
$latestRun = $historyCount ? $history[array_key_first($history)] : null;
$latestPeriod = $latestRun ? date('M Y', mktime(0, 0, 0, (int) $latestRun['month'], 1, (int) $latestRun['year'])) : null;
?>

<section class="payroll-hero card border-0 shadow-sm mb-4">
    <div class="payroll-hero__body">
        <p class="eyebrow text-uppercase text-muted mb-2">Payroll Workspace</p>
        <h2 class="mb-3">Build compensation with clarity and control</h2>
        <p class="text-muted mb-0">
            Move through each module – from configuring base salaries to running payroll – without the clutter.
            Your settings, adjustments, loans, and history each live in their own focused workspace.
        </p>
    </div>
    <div class="payroll-hero__stats">
        <div class="payroll-stat">
            <span class="label">Employees configured</span>
            <strong><?= $configuredEmployees ?> / <?= $totalEmployees ?></strong>
            <small><?= $totalEmployees ? round(($configuredEmployees / max($totalEmployees, 1)) * 100) : 0 ?>% coverage</small>
        </div>
        <div class="payroll-stat">
            <span class="label">Recent payroll runs</span>
            <strong><?= $historyCount ?></strong>
            <small><?= $latestPeriod ? 'Latest · ' . $latestPeriod : 'No runs yet' ?></small>
        </div>
        <div class="payroll-stat">
            <span class="label">Ready to process</span>
            <strong><?= $totalEmployees - $configuredEmployees ?> employees</strong>
            <small>Configure salary to unlock payroll</small>
        </div>
    </div>
</section>

<?php if (!empty($flash)): ?>
    <div class="alert alert-<?= htmlspecialchars($flash['type'] ?? 'info') ?> payroll-alert shadow-sm mb-4">
        <?= htmlspecialchars($flash['message'] ?? '') ?>
    </div>
<?php endif; ?>

<div class="payroll-layout">
    <aside class="payroll-nav card shadow-sm">
        <p class="nav-label text-muted">Workspace modules</p>
        <button type="button"
                class="payroll-nav-btn active"
                data-module-target="salary"
                id="module-tab-salary"
                aria-controls="module-salary"
                aria-selected="true">
            <span class="nav-step">1</span>
            <div>
                <strong>Salary Settings</strong>
                <small>Baseline pay + OT</small>
            </div>
        </button>
        <button type="button"
                class="payroll-nav-btn"
                data-module-target="allowances"
                id="module-tab-allowances"
                aria-controls="module-allowances"
                aria-selected="false">
            <span class="nav-step">2</span>
            <div>
                <strong>Allowances & Deductions</strong>
                <small>Recurring adjustments</small>
            </div>
        </button>
        <button type="button"
                class="payroll-nav-btn"
                data-module-target="loans"
                id="module-tab-loans"
                aria-controls="module-loans"
                aria-selected="false">
            <span class="nav-step">3</span>
            <div>
                <strong>Loans</strong>
                <small>Track repayments</small>
            </div>
        </button>
        <button type="button"
                class="payroll-nav-btn"
                data-module-target="run"
                id="module-tab-run"
                aria-controls="module-run"
                aria-selected="false">
            <span class="nav-step">4</span>
            <div>
                <strong>Run Payroll</strong>
                <small>Process pay cycles</small>
            </div>
        </button>
        <button type="button"
                class="payroll-nav-btn"
                data-module-target="overview"
                id="module-tab-overview"
                aria-controls="module-overview"
                aria-selected="false">
            <span class="nav-step">5</span>
            <div>
                <strong>Salary Overview</strong>
                <small>Live configuration</small>
            </div>
        </button>
        <button type="button"
                class="payroll-nav-btn"
                data-module-target="history"
                id="module-tab-history"
                aria-controls="module-history"
                aria-selected="false">
            <span class="nav-step">6</span>
            <div>
                <strong>Recent Payroll Runs</strong>
                <small>Audit trail</small>
            </div>
        </button>
    </aside>

    <div class="payroll-panels">
        <section class="payroll-module active" id="module-salary" role="tabpanel" aria-labelledby="module-tab-salary">
            <div class="module-header">
                <div>
                    <p class="eyebrow text-uppercase text-muted mb-1">Base Compensation</p>
                    <h4 class="mb-2">Salary Settings</h4>
                    <p class="text-muted mb-0">Define each employee's baseline salary and overtime rate. Updating here immediately impacts future payroll runs.</p>
                </div>
            </div>
            <div class="card shadow-sm module-card">
                <div class="card-body">
                    <form class="row g-3" method="post" action="<?= route_to('payroll/saveSetting') ?>">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Employee</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="" disabled selected>Select employee</option>
                                <?php foreach ($employees as $employee): ?>
                                    <option value="<?= $employee['id'] ?>"><?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Basic Salary</label>
                            <input type="number" name="basic_salary" class="form-control" min="0" step="0.01" placeholder="0.00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Overtime Rate</label>
                            <input type="number" name="overtime_rate" class="form-control" min="0" step="0.01" value="0">
                        </div>
                        <div class="col-12 d-flex justify-content-between align-items-center pt-2">
                            <small class="text-muted">Tip: Re-run payroll for the employee if you adjust salary mid-cycle.</small>
                            <button class="btn btn-primary px-4">Save salary settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section class="payroll-module" id="module-allowances" role="tabpanel" aria-labelledby="module-tab-allowances">
            <div class="module-header">
                <div>
                    <p class="eyebrow text-uppercase text-muted mb-1">Flexible Components</p>
                    <h4 class="mb-2">Allowances & Deductions</h4>
                    <p class="text-muted mb-0">Record recurring allowances (e.g., transport, bonus) or deductions (e.g., tax, benefits). These are automatically factored in during the payroll run.</p>
                </div>
            </div>
            <div class="card shadow-sm module-card">
                <div class="card-body">
                    <form class="row g-3" method="post" action="<?= route_to('payroll/saveComponent') ?>">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Employee</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="" disabled selected>Select employee</option>
                                <?php foreach ($employees as $employee): ?>
                                    <option value="<?= $employee['id'] ?>"><?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Component</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Housing" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Amount</label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label fw-semibold">Type</label>
                            <select name="component_type" class="form-select">
                                <option value="allowance">Allowance (+)</option>
                                <option value="deduction">Deduction (−)</option>
                            </select>
                        </div>
                        <div class="col-12 d-flex justify-content-between align-items-center pt-2">
                            <small class="text-muted">Need one-off adjustments? Apply them directly when running payroll.</small>
                            <button class="btn btn-primary px-4">Save component</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section class="payroll-module" id="module-loans" role="tabpanel" aria-labelledby="module-tab-loans">
            <div class="module-header">
                <div>
                    <p class="eyebrow text-uppercase text-muted mb-1">Employee Financing</p>
                    <h4 class="mb-2">Loans</h4>
                    <p class="text-muted mb-0">Capture loans and monthly installments. Active balances automatically deduct from the employee's net pay.</p>
                </div>
            </div>
            <div class="card shadow-sm module-card">
                <div class="card-body">
                    <form class="row g-3" method="post" action="<?= route_to('payroll/saveLoan') ?>">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Employee</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="" disabled selected>Select employee</option>
                                <?php foreach ($employees as $employee): ?>
                                    <option value="<?= $employee['id'] ?>"><?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Loan amount</label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Monthly installment</label>
                            <input type="number" name="installment" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                        </div>
                        <div class="col-12 d-flex justify-content-between align-items-center pt-2">
                            <small class="text-muted">Installments post automatically until the loan status is cleared.</small>
                            <button class="btn btn-primary px-4">Record loan</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section class="payroll-module" id="module-run" role="tabpanel" aria-labelledby="module-tab-run">
            <div class="module-header">
                <div>
                    <p class="eyebrow text-uppercase text-muted mb-1">Processing</p>
                    <h4 class="mb-2">Run Payroll</h4>
                    <p class="text-muted mb-0">Generate a payroll run for a single employee. The system aggregates salary, allowances, deductions, loans, overtime, and unpaid leaves.</p>
                </div>
                <div class="module-insight">
                    <span class="badge bg-light text-dark">Reminder</span>
                    <small class="text-muted">Ensure salary and components are set before running.</small>
                </div>
            </div>
            <div class="card shadow-sm module-card">
                <div class="card-body">
                    <form class="row g-3" method="post" action="<?= route_to('payroll/run') ?>">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <div class="col-lg-4">
                            <label class="form-label fw-semibold">Employee</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="" disabled selected>Select employee</option>
                                <?php foreach ($employees as $employee): ?>
                                    <option value="<?= $employee['id'] ?>"><?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 col-lg-3">
                            <label class="form-label fw-semibold">Month</label>
                            <select name="month" class="form-select">
                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                    <option value="<?= $m ?>" <?= $m == date('n') ? 'selected' : '' ?>>
                                        <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4 col-lg-3">
                            <label class="form-label fw-semibold">Year</label>
                            <input type="number" name="year" class="form-control" value="<?= date('Y') ?>">
                        </div>
                        <div class="col-lg-2 d-flex align-items-end">
                            <button class="btn btn-success w-100">Run payroll</button>
                        </div>
                        <div class="col-12">
                            <div class="callout callout-success">
                                <strong>Heads-up:</strong> Need bulk processing? Run per employee and export results from the history tab while batch automation is being finalized.
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section class="payroll-module" id="module-overview" role="tabpanel" aria-labelledby="module-tab-overview">
            <div class="module-header">
                <div>
                    <p class="eyebrow text-uppercase text-muted mb-1">Audit</p>
                    <h4 class="mb-2">Salary Overview</h4>
                    <p class="text-muted mb-0">Every employee with configured salary details appears here. Use it to verify coverage before running payroll.</p>
                </div>
            </div>
            <div class="card shadow-sm module-card">
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
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">No salary settings saved.</td>
                                </tr>
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
        </section>

        <section class="payroll-module" id="module-history" role="tabpanel" aria-labelledby="module-tab-history">
            <div class="module-header">
                <div>
                    <p class="eyebrow text-uppercase text-muted mb-1">Traceability</p>
                    <h4 class="mb-2">Recent Payroll Runs</h4>
                    <p class="text-muted mb-0">Latest payroll events including gross, allowances, deductions, loans, and final net pay. Use it as your audit trail.</p>
                </div>
            </div>
            <div class="card shadow-sm module-card">
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
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No payroll history.</td>
                                </tr>
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
        </section>
    </div>
</div>

