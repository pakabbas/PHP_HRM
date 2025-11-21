<?php

class UserModel extends Model
{
    protected string $table = 'users';
    protected array $fillable = [
        'employee_id',
        'username',
        'password',
        'role',
        'status',
    ];

    public function getByUsername(string $username): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username LIMIT 1";
        $stmt = $this->db->query($sql, [':username' => $username]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function allWithEmployees(array $options = []): array
    {
        $sql = "SELECT u.*, e.first_name, e.last_name, e.emp_code FROM users u
                LEFT JOIN employees e ON e.id = u.employee_id
                WHERE 1=1";
        $params = [];
        if (!empty($options['search'])) {
            $sql .= " AND (u.username LIKE :search OR e.first_name LIKE :search OR e.last_name LIKE :search)";
            $params[':search'] = '%' . $options['search'] . '%';
        }
        $sql .= " ORDER BY u.id DESC";
        if (!empty($options['limit'])) {
            $sql .= " LIMIT " . (int) $options['limit'];
            if (!empty($options['offset'])) {
                $sql .= " OFFSET " . (int) $options['offset'];
            }
        }
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function count(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->table}");
        return (int) $stmt->fetchColumn();
    }
}

