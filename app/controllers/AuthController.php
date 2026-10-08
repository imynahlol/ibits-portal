<?php
declare(strict_types=1);

final class AuthController {
    public function login(string $studentId, string $password): bool {
        global $pdo;
        if (!$pdo) return false;
        $user = (new User($pdo))->findByStudentId($studentId);
        if (!$user || empty($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
            return false;
        }
        $_SESSION['user'] = $user;
        return true;
    }

    public function logout(): void {
        $_SESSION = [];
        session_destroy();
    }
}
