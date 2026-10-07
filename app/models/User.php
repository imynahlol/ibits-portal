<?php
// app/models/User.php

class User {
    private PDO $db;

    public function __construct() {
        $this->db = db();
    }

    /**
     * Find a user by their id_number (student or adviser format).
     */
    public function findByIdNumber(string $idNumber): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id_number = ? LIMIT 1");
        $stmt->execute([$idNumber]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Find a user by primary key.
     */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Create a new user. Expects:
     *   full_name, id_number, sex, program, year_level, password, role
     *
     * For advisers: program and year_level may be null.
     */
    public function create(array $data): bool {
        $stmt = $this->db->prepare("
            INSERT INTO users
                (full_name, id_number, sex, program, year_level, password_hash, role)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data['full_name'],
            $data['id_number'],
            $data['sex'],
            $data['program']    ?? null,
            $data['year_level'] ?? null,
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'] ?? 'member',
        ]);
    }
}