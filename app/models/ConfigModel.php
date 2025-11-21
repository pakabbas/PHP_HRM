<?php

class ConfigModel extends Model
{
    protected array $entities = [
        'departments' => ['table' => 'departments', 'label' => 'Departments', 'columns' => ['department_name', 'status']],
        'designations' => ['table' => 'designations', 'label' => 'Designations', 'columns' => ['designation_name', 'status']],
        'cities' => ['table' => 'cities', 'label' => 'Cities', 'columns' => ['city_name', 'status']],
        'configurations' => ['table' => 'configurations', 'label' => 'Dictionary', 'columns' => ['config_type', 'config_key', 'config_value', 'status']],
        'holidays' => ['table' => 'holidays', 'label' => 'Holidays', 'columns' => ['holiday_date', 'description']],
    ];

    public function getAll(string $entity): array
    {
        $table = $this->entities[$entity]['table'] ?? null;
        if (!$table) {
            return [];
        }
        $stmt = $this->db->query("SELECT * FROM {$table} ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function save(string $entity, array $data, ?int $id = null): void
    {
        $config = $this->entities[$entity] ?? null;
        $table = $config['table'] ?? null;
        if (!$table) {
            return;
        }

        $columns = $config['columns'];
        $filtered = array_intersect_key($data, array_flip($columns));

        if ($id) {
            $setParts = [];
            $params = [];
            foreach ($filtered as $column => $value) {
                $setParts[] = "{$column} = :{$column}";
                $params[":{$column}"] = $value;
            }
            $params[':id'] = $id;
            $sql = "UPDATE {$table} SET " . implode(',', $setParts) . " WHERE id = :id";
            $this->db->query($sql, $params);
        } else {
            if (in_array('status', $columns, true) && !isset($filtered['status'])) {
                $filtered['status'] = 1;
            }
            $columnsSql = implode(',', array_keys($filtered));
            $placeholders = ':' . implode(', :', array_keys($filtered));
            $sql = "INSERT INTO {$table} ({$columnsSql}) VALUES ({$placeholders})";
            $this->db->insert($sql, $this->prefix($filtered));
        }
    }

    public function deleteEntity(string $entity, int $id): void
    {
        $table = $this->entities[$entity]['table'] ?? null;
        if (!$table) {
            return;
        }
        $this->db->query("DELETE FROM {$table} WHERE id = :id", [':id' => $id]);
    }

    protected function prefix(array $data): array
    {
        $params = [];
        foreach ($data as $key => $value) {
            $params[":{$key}"] = $value;
        }
        return $params;
    }
}

