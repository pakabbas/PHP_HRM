<?php

class EmployeeModel extends Model
{
    protected string $table = 'employees';
    protected array $fillable = [
        'emp_code',
        'first_name',
        'last_name',
        'gender',
        'dob',
        'cnic',
        'phone',
        'email',
        'address',
        'city_id',
        'department_id',
        'designation_id',
        'joining_date',
        'leaving_date',
        'status',
    ];

    public function listing(array $filters = [], array $options = []): array
    {
        $sql = "SELECT e.*, d.department_name, des.designation_name, c.city_name
                FROM employees e
                LEFT JOIN departments d ON d.id = e.department_id
                LEFT JOIN designations des ON des.id = e.designation_id
                LEFT JOIN cities c ON c.id = e.city_id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['department_id'])) {
            $sql .= " AND e.department_id = :department_id";
            $params[':department_id'] = $filters['department_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND e.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($options['search'])) {
            $sql .= " AND (e.first_name LIKE :search OR e.last_name LIKE :search OR e.emp_code LIKE :search)";
            $params[':search'] = '%' . $options['search'] . '%';
        }

        $sql .= " ORDER BY e.id DESC";

        if (!empty($options['limit'])) {
            $sql .= " LIMIT " . (int) $options['limit'];
            if (!empty($options['offset'])) {
                $sql .= " OFFSET " . (int) $options['offset'];
            }
        }

        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function count(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) FROM employees WHERE 1=1";
        $params = [];
        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }
        $stmt = $this->db->query($sql, $params);
        return (int) $stmt->fetchColumn();
    }

    public function countFiltered(array $filters = [], ?string $search = null): int
    {
        $sql = "SELECT COUNT(*) FROM employees e WHERE 1=1";
        $params = [];
        if (!empty($filters['department_id'])) {
            $sql .= " AND e.department_id = :department_id";
            $params[':department_id'] = $filters['department_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND e.status = :status";
            $params[':status'] = $filters['status'];
        }
        if ($search) {
            $sql .= " AND (e.first_name LIKE :search OR e.last_name LIKE :search OR e.emp_code LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        $stmt = $this->db->query($sql, $params);
        return (int) $stmt->fetchColumn();
    }

    public function stats(): array
    {
        $pdo = $this->db->getConnection();
        $stats = [];
        $stats['total'] = (int) $pdo->query("SELECT COUNT(*) FROM employees")->fetchColumn();
        $stats['active'] = (int) $pdo->query("SELECT COUNT(*) FROM employees WHERE status = 'active'")->fetchColumn();
        $stats['departments'] = (int) $pdo->query("SELECT COUNT(*) FROM departments")->fetchColumn();
        $stats['pending_leaves'] = (int) $pdo->query("SELECT COUNT(*) FROM leave_requests WHERE status = 'pending'")->fetchColumn();
        return $stats;
    }
}

