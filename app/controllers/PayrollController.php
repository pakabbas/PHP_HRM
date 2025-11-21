<?php

class PayrollController extends Controller
{
    protected PayrollModel $payroll;
    protected EmployeeModel $employees;

    public function __construct()
    {
        parent::__construct();
        $this->payroll = new PayrollModel();
        $this->employees = new EmployeeModel();
    }

    public function index(): void
    {
        $this->requireAuth(['admin', 'hr']);
        $this->view('payroll/index', [
            'pageTitle' => 'Payroll Center',
            'settings' => $this->payroll->salarySettings(),
            'history' => $this->payroll->history(10),
            'employees' => $this->employees->listing([], ['limit' => 500]),
            'flash' => flash_get('payroll'),
        ]);
    }

    public function saveSetting(): void
    {
        $this->requireAuth(['admin', 'hr']);
        verify_csrf();
        $db = Database::getInstance();
        $sql = "INSERT INTO employee_salary_settings (employee_id, basic_salary, overtime_rate)
                VALUES (:employee_id, :basic_salary, :overtime_rate)
                ON DUPLICATE KEY UPDATE basic_salary = VALUES(basic_salary), overtime_rate = VALUES(overtime_rate)";
        $db->insert($sql, [
            ':employee_id' => (int) $_POST['employee_id'],
            ':basic_salary' => (float) $_POST['basic_salary'],
            ':overtime_rate' => (float) $_POST['overtime_rate'],
        ]);
        flash_set('payroll', 'Salary settings saved.', 'success');
        redirect('payroll/index');
    }

    public function saveComponent(): void
    {
        $this->requireAuth(['admin', 'hr']);
        verify_csrf();
        $type = $_POST['component_type']; // allowance|deduction
        $table = $type === 'deduction' ? 'salary_deductions' : 'salary_allowances';
        $db = Database::getInstance();
        $db->insert("INSERT INTO {$table} (employee_id, title, amount) VALUES (:employee_id, :title, :amount)", [
            ':employee_id' => (int) $_POST['employee_id'],
            ':title' => $_POST['title'],
            ':amount' => (float) $_POST['amount'],
        ]);
        flash_set('payroll', ucfirst($type) . ' saved.', 'success');
        redirect('payroll/index');
    }

    public function saveLoan(): void
    {
        $this->requireAuth(['admin', 'hr']);
        verify_csrf();
        $db = Database::getInstance();
        $db->insert("INSERT INTO loans (employee_id, amount, remaining, monthly_installment, status) VALUES (:employee_id, :amount, :remaining, :installment, 'active')", [
            ':employee_id' => (int) $_POST['employee_id'],
            ':amount' => (float) $_POST['amount'],
            ':remaining' => (float) $_POST['amount'],
            ':installment' => (float) $_POST['installment'],
        ]);
        flash_set('payroll', 'Loan recorded.', 'success');
        redirect('payroll/index');
    }

    public function run(): void
    {
        $this->requireAuth(['admin', 'hr']);
        verify_csrf();
        $employeeId = (int) $_POST['employee_id'];
        $month = (int) $_POST['month'];
        $year = (int) $_POST['year'];

        try {
            $this->payroll->runPayroll($employeeId, $month, $year);
            flash_set('payroll', 'Payroll processed.', 'success');
        } catch (Throwable $e) {
            flash_set('payroll', 'Payroll failed: ' . $e->getMessage(), 'danger');
        }
        redirect('payroll/index');
    }
}

