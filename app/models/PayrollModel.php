<?php

class PayrollModel extends Model
{
    protected string $table = 'employee_salary_settings';
    protected array $fillable = [
        'employee_id',
        'basic_salary',
        'overtime_rate',
    ];

    public function salarySettings(array $filters = []): array
    {
        $sql = "SELECT ess.*, e.first_name, e.last_name, e.emp_code
                FROM employee_salary_settings ess
                JOIN employees e ON e.id = ess.employee_id
                WHERE 1=1";
        $params = [];
        if (!empty($filters['employee_id'])) {
            $sql .= " AND ess.employee_id = :employee_id";
            $params[':employee_id'] = $filters['employee_id'];
        }
        $sql .= " ORDER BY e.first_name";
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function allowances(int $employeeId): array
    {
        $stmt = $this->db->query("SELECT * FROM salary_allowances WHERE employee_id = :employee_id", [':employee_id' => $employeeId]);
        return $stmt->fetchAll();
    }

    public function deductions(int $employeeId): array
    {
        $stmt = $this->db->query("SELECT * FROM salary_deductions WHERE employee_id = :employee_id", [':employee_id' => $employeeId]);
        return $stmt->fetchAll();
    }

    public function loans(int $employeeId): array
    {
        $stmt = $this->db->query("SELECT * FROM loans WHERE employee_id = :employee_id AND status = 'active'", [':employee_id' => $employeeId]);
        return $stmt->fetchAll();
    }

    public function overtimeTotal(int $employeeId, int $month, int $year): float
    {
        $sql = "SELECT SUM(amount) FROM overtime WHERE employee_id = :employee_id AND MONTH(created_at) = :month AND YEAR(created_at) = :year";
        $stmt = $this->db->query($sql, [
            ':employee_id' => $employeeId,
            ':month' => $month,
            ':year' => $year,
        ]);
        return (float) $stmt->fetchColumn();
    }

    public function unpaidLeaveDeduction(int $employeeId, int $month, int $year, float $dailyRate): float
    {
        $sql = "SELECT SUM(total_days) FROM leave_requests
                WHERE employee_id = :employee_id
                  AND status = 'approved'
                  AND MONTH(start_date) = :month
                  AND YEAR(start_date) = :year
                  AND remarks LIKE '%UNPAID%'";
        $stmt = $this->db->query($sql, [
            ':employee_id' => $employeeId,
            ':month' => $month,
            ':year' => $year,
        ]);
        $unpaidDays = (float) $stmt->fetchColumn();
        return $unpaidDays * $dailyRate;
    }

    public function runPayroll(int $employeeId, int $month, int $year): int
    {
        $pdo = $this->db->getConnection();
        $pdo->beginTransaction();
        try {
            $setting = $pdo->prepare("SELECT * FROM employee_salary_settings WHERE employee_id = :employee_id");
            $setting->execute([':employee_id' => $employeeId]);
            $setting = $setting->fetch();
            if (!$setting) {
                throw new RuntimeException('Salary settings not found');
            }

            $allowances = $this->allowances($employeeId);
            $deductions = $this->deductions($employeeId);
            $loans = $this->loans($employeeId);

            $allowanceTotal = array_sum(array_column($allowances, 'amount'));
            $deductionTotal = array_sum(array_column($deductions, 'amount'));
            $loanInstallment = array_sum(array_column($loans, 'monthly_installment'));
            $overtimeAmount = $this->overtimeTotal($employeeId, $month, $year);

            $dailyRate = $setting['basic_salary'] / 30;
            $unpaidDeduction = $this->unpaidLeaveDeduction($employeeId, $month, $year, $dailyRate);

            $gross = $setting['basic_salary'] + $allowanceTotal + $overtimeAmount;
            $net = $gross - $deductionTotal - $loanInstallment - $unpaidDeduction;

            $sql = "INSERT INTO payroll_runs
                    (employee_id, month, year, gross_salary, total_allowances, total_deductions,
                     overtime_amount, unpaid_leave_deductions, loan_deduction, net_salary, status)
                     VALUES (:employee_id, :month, :year, :gross, :allow, :deduct, :ot, :unpaid, :loan, :net, 'processed')";
            $id = $this->db->insert($sql, [
                ':employee_id' => $employeeId,
                ':month' => $month,
                ':year' => $year,
                ':gross' => $gross,
                ':allow' => $allowanceTotal,
                ':deduct' => $deductionTotal,
                ':ot' => $overtimeAmount,
                ':unpaid' => $unpaidDeduction,
                ':loan' => $loanInstallment,
                ':net' => $net,
            ]);

            $pdo->commit();
            return $id;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function history(int $limit = 20): array
    {
        $sql = "SELECT pr.*, e.first_name, e.last_name, e.emp_code
                FROM payroll_runs pr
                JOIN employees e ON e.id = pr.employee_id
                ORDER BY pr.created_at DESC
                LIMIT :limit";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

