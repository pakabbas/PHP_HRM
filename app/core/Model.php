<?php

abstract class Model
{
    protected Database $db;
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    public function all(array $filters = [], array $options = []): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        foreach ($filters as $column => $value) {
            if ($value === '' || $value === null) {
                continue;
            }
            $sql .= " AND {$column} = :{$column}";
            $params[":{$column}"] = $value;
        }

        if (!empty($options['search']) && !empty($options['search_columns'])) {
            $searchSql = [];
            foreach ($options['search_columns'] as $idx => $column) {
                $placeholder = ":search{$idx}";
                $searchSql[] = "{$column} LIKE {$placeholder}";
                $params[$placeholder] = '%' . $options['search'] . '%';
            }
            if ($searchSql) {
                $sql .= ' AND (' . implode(' OR ', $searchSql) . ')';
            }
        }

        $order = $options['order'] ?? "{$this->primaryKey} DESC";
        $sql .= " ORDER BY {$order}";

        if (!empty($options['limit'])) {
            $sql .= " LIMIT " . (int) $options['limit'];
            if (!empty($options['offset'])) {
                $sql .= " OFFSET " . (int) $options['offset'];
            }
        }

        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->query($sql, [':id' => $id]);
        $record = $stmt->fetch();
        return $record ?: null;
    }

    public function create(array $data): int
    {
        $data = $this->filterFillable($data);
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        return $this->db->insert($sql, $this->prefixParams($data));
    }

    public function update(int $id, array $data): void
    {
        $data = $this->filterFillable($data);
        $setParts = [];
        foreach ($data as $column => $value) {
            $setParts[] = "{$column} = :{$column}";
        }
        $sql = "UPDATE {$this->table} SET " . implode(',', $setParts) . " WHERE {$this->primaryKey} = :id";

        $params = $this->prefixParams($data);
        $params[':id'] = $id;
        $this->db->query($sql, $params);
    }

    public function delete(int $id): void
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $this->db->query($sql, [':id' => $id]);
    }

    protected function prefixParams(array $data): array
    {
        $params = [];
        foreach ($data as $key => $value) {
            $params[":{$key}"] = $value;
        }
        return $params;
    }
}

