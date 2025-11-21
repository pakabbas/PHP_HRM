<?php

class LeaveModel extends Model
{
    protected string $table = 'leave_requests';
    protected array $fillable = [
        'employee_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'total_days',
        'status',
        'remarks',
    ];

    public function requests(array $filters = [], array $options = []): array
    {
        $sql = "SELECT lr.*, e.first_name, e.last_name, lt.type_name
                FROM leave_requests lr
                JOIN employees e ON e.id = lr.employee_id
                JOIN leave_types lt ON lt.id = lr.leave_type_id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND lr.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['employee_id'])) {
            $sql .= " AND lr.employee_id = :employee_id";
            $params[':employee_id'] = $filters['employee_id'];
        }
        if (!empty($options['search'])) {
            $sql .= " AND (e.first_name LIKE :search OR e.last_name LIKE :search)";
            $params[':search'] = '%' . $options['search'] . '%';
        }
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $sql .= " AND lr.start_date BETWEEN :start_date AND :end_date";
            $params[':start_date'] = $filters['start_date'];
            $params[':end_date'] = $filters['end_date'];
        }

        $sql .= " ORDER BY lr.created_at DESC";

        if (!empty($options['limit'])) {
            $sql .= " LIMIT " . (int) $options['limit'];
            if (!empty($options['offset'])) {
                $sql .= " OFFSET " . (int) $options['offset'];
            }
        }

        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function pendingCount(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM leave_requests WHERE status = 'pending'");
        return (int) $stmt->fetchColumn();
    }

    public function leaveTypes(): array
    {
        $stmt = $this->db->query("SELECT * FROM leave_types WHERE status = 1 ORDER BY type_name ASC");
        return $stmt->fetchAll();
    }

    public function balancesByEmployee(int $employeeId, int $year): array
    {
        $sql = "SELECT lb.*, lt.type_name
                FROM leave_balances lb
                JOIN leave_types lt ON lt.id = lb.leave_type_id
                WHERE lb.employee_id = :employee_id AND lb.year = :year";
        $stmt = $this->db->query($sql, [
            ':employee_id' => $employeeId,
            ':year' => $year,
        ]);
        return $stmt->fetchAll();
    }

    public function consumeBalance(int $employeeId, int $leaveTypeId, int $year, int $days): array
    {
        $pdo = $this->db->getConnection();
        $pdo->beginTransaction();
        try {
            $typeStmt = $pdo->prepare("SELECT * FROM leave_types WHERE id = :id");
            $typeStmt->execute([':id' => $leaveTypeId]);
            $type = $typeStmt->fetch();
            $defaultAllocation = (int) ($type['default_days'] ?? 0);

            $balanceStmt = $pdo->prepare("SELECT * FROM leave_balances WHERE employee_id = :employee_id AND leave_type_id = :leave_type_id AND year = :year");
            $balanceStmt->execute([
                ':employee_id' => $employeeId,
                ':leave_type_id' => $leaveTypeId,
                ':year' => $year,
            ]);
            $balance = $balanceStmt->fetch();

            if (!$balance) {
                $pdo->prepare("INSERT INTO leave_balances (employee_id, leave_type_id, allocated, used, year)
                               VALUES (:employee_id, :leave_type_id, :allocated, 0, :year)")
                    ->execute([
                        ':employee_id' => $employeeId,
                        ':leave_type_id' => $leaveTypeId,
                        ':allocated' => $defaultAllocation,
                        ':year' => $year,
                    ]);
                $balance = [
                    'allocated' => $defaultAllocation,
                    'used' => 0,
                ];
            }

            $available = max(0, (int) $balance['allocated'] - (int) $balance['used']);
            $paid = min($available, $days);
            $unpaid = max(0, $days - $paid);

            if ($paid > 0) {
                $pdo->prepare("UPDATE leave_balances SET used = used + :used WHERE employee_id = :employee_id AND leave_type_id = :leave_type_id AND year = :year")
                    ->execute([
                        ':used' => $paid,
                        ':employee_id' => $employeeId,
                        ':leave_type_id' => $leaveTypeId,
                        ':year' => $year,
                    ]);
            }

            $pdo->commit();
            return ['paid' => $paid, 'unpaid' => $unpaid];
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function approve(array $request, string $remarks = null): void
    {
        $sql = "UPDATE leave_requests SET status = 'approved', remarks = :remarks WHERE id = :id";
        $this->db->query($sql, [
            ':remarks' => $remarks,
            ':id' => $request['id'],
        ]);
    }

    public function reject(int $id, string $remarks = null): void
    {
        $sql = "UPDATE leave_requests SET status = 'rejected', remarks = :remarks WHERE id = :id";
        $this->db->query($sql, [
            ':remarks' => $remarks,
            ':id' => $id,
        ]);
    }
}

