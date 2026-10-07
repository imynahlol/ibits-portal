<?php

class User {
    private PDO $db;

    public function __construct() {
        $this->db = db();
    }

    public function findByStudentNumber(string $studentNumber): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE student_number = ? LIMIT 1");
        $stmt->execute([$studentNumber]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare("
            INSERT INTO users
                (full_name, student_number, sex, program, year_level, password_hash, role)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['full_name'],
            $data['student_number'],
            $data['sex'],
            $data['program'],
            $data['year_level'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'] ?? 'member',
        ]);
    }
}