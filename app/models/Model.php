<?php
declare(strict_types=1);

abstract class Model {
    protected string $table = '';
    protected string $primaryKey = 'id';

    protected function pdo(): PDO {
        return db();
    }

    public function all(string $orderBy = 'id DESC'): array {
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy}";
        return $this->pdo()->query($sql)->fetchAll();
    }

    public function find(int|string $id): ?array {
        $stmt = $this->pdo()->prepare(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function findBy(string $column, mixed $value): ?array {
        $stmt = $this->pdo()->prepare(
            "SELECT * FROM {$this->table} WHERE {$column} = :v LIMIT 1"
        );
        $stmt->execute(['v' => $value]);
        return $stmt->fetch() ?: null;
    }

    public function where(string $column, mixed $value, string $orderBy = 'id DESC'): array {
        $stmt = $this->pdo()->prepare(
            "SELECT * FROM {$this->table} WHERE {$column} = :v ORDER BY {$orderBy}"
        );
        $stmt->execute(['v' => $value]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int {
        $cols = array_keys($data);
        $place = array_map(fn($c) => ':' . $c, $cols);
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(',', $cols),
            implode(',', $place)
        );
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute($data);
        return (int) $this->pdo()->lastInsertId();
    }

    public function update(int|string $id, array $data): bool {
        $sets = implode(', ', array_map(fn($c) => "{$c} = :{$c}", array_keys($data)));
        $data['__id'] = $id;
        $sql = "UPDATE {$this->table} SET {$sets} WHERE {$this->primaryKey} = :__id";
        return $this->pdo()->prepare($sql)->execute($data);
    }

    public function delete(int|string $id): bool {
        $stmt = $this->pdo()->prepare(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id"
        );
        return $stmt->execute(['id' => $id]);
    }

    public function count(?string $column = null, mixed $value = null): int {
        if ($column === null) {
            return (int) $this->pdo()->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
        }
        $stmt = $this->pdo()->prepare(
            "SELECT COUNT(*) FROM {$this->table} WHERE {$column} = :v"
        );
        $stmt->execute(['v' => $value]);
        return (int) $stmt->fetchColumn();
    }
}