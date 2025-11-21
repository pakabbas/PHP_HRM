<?php

class AttendanceModel extends Model
{
    protected string $table = 'attendance';
    protected array $fillable = [
        'employee_id',
        'attendance_date',
        'check_in',
        'check_out',
        'status',
    ];

    public function getByDateRange(string $start, string $end, array $filters = []): array
    {
        $sql = "SELECT a.*, e.first_name, e.last_name, e.emp_code, d.department_name
                FROM attendance a
                JOIN employees e ON e.id = a.employee_id
                LEFT JOIN departments d ON d.id = e.department_id
                WHERE a.attendance_date BETWEEN :start AND :end";
        $params = [
            ':start' => $start,
            ':end' => $end,
        ];
        if (!empty($filters['department_id'])) {
            $sql .= " AND e.department_id = :department_id";
            $params[':department_id'] = $filters['department_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND a.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['employee_id'])) {
            $sql .= " AND a.employee_id = :employee_id";
            $params[':employee_id'] = $filters['employee_id'];
        }
        $sql .= " ORDER BY a.attendance_date DESC";
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function summaryForDate(string $date): array
    {
        $sql = "SELECT status, COUNT(*) as total FROM attendance WHERE attendance_date = :date GROUP BY status";
        $stmt = $this->db->query($sql, [':date' => $date]);
        $summary = array_fill_keys(['present', 'absent', 'leave', 'half_day', 'holiday'], 0);
        foreach ($stmt->fetchAll() as $row) {
            $summary[$row['status']] = (int) $row['total'];
        }
        return $summary;
    }

    public function markBulk(array $rows): void
    {
        $pdo = $this->db->getConnection();
        $sql = "INSERT INTO attendance (employee_id, attendance_date, check_in, check_out, status)
                VALUES (:employee_id, :attendance_date, :check_in, :check_out, :status)
                ON DUPLICATE KEY UPDATE
                    check_in = VALUES(check_in),
                    check_out = VALUES(check_out),
                    status = VALUES(status)";
        $stmt = $pdo->prepare($sql);
        foreach ($rows as $row) {
            $stmt->execute([
                ':employee_id' => $row['employee_id'],
                ':attendance_date' => $row['attendance_date'],
                ':check_in' => $row['check_in'] ?? null,
                ':check_out' => $row['check_out'] ?? null,
                ':status' => $row['status'] ?? 'present',
            ]);
        }
    }
}

