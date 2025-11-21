<?php

class ReportModel extends Model
{
    public function employeeReport(array $filters = []): array
    {
        $sql = "SELECT e.emp_code, CONCAT(e.first_name, ' ', e.last_name) AS employee_name,
                       d.department_name, des.designation_name, e.status, e.joining_date
                FROM employees e
                LEFT JOIN departments d ON d.id = e.department_id
                LEFT JOIN designations des ON des.id = e.designation_id
                WHERE 1=1";
        $params = [];
        if (!empty($filters['status'])) {
            $sql .= " AND e.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['department_id'])) {
            $sql .= " AND e.department_id = :department_id";
            $params[':department_id'] = $filters['department_id'];
        }
        $sql .= " ORDER BY e.emp_code";
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function attendanceReport(string $start, string $end, array $filters = []): array
    {
        $attendance = new AttendanceModel();
        return $attendance->getByDateRange($start, $end, $filters);
    }

    public function leaveReport(array $filters = []): array
    {
        $leave = new LeaveModel();
        return $leave->requests($filters);
    }

    public function payrollReport(int $month, int $year): array
    {
        $sql = "SELECT pr.*, e.emp_code, e.first_name, e.last_name
                FROM payroll_runs pr
                JOIN employees e ON e.id = pr.employee_id
                WHERE pr.month = :month AND pr.year = :year
                ORDER BY e.emp_code";
        $stmt = $this->db->query($sql, [
            ':month' => $month,
            ':year' => $year,
        ]);
        return $stmt->fetchAll();
    }
}

