<?php
declare(strict_types=1);

final class User {
    public function __construct(private ?PDO $db) {}

    public function findByStudentId(string $studentId): ?array {
        if (!$this->db) return null;
        $stmt = $this->db->prepare('SELECT * FROM users WHERE student_id = ? LIMIT 1');
        $stmt->execute([$studentId]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $name, string $studentId, string $program, string $password): int {
        if (!$this->db) throw new RuntimeException('Database unavailable.');
        $stmt = $this->db->prepare('INSERT INTO users (full_name, student_id, program, role, password_hash) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$name, $studentId, $program, 'Member', password_hash($password, PASSWORD_DEFAULT)]);
        return (int)$this->db->lastInsertId();
    }
}
