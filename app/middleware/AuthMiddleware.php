<?php
class AuthMiddleware {
    public static function requireLogin(): void {
        if (!isset($_SESSION['user_id'])) {
            redirect($_ENV['APP_URL'] . '/login');
        }
    }

    public static function requireRole(array|string $roles): void {
        self::requireLogin();
        $roles = (array)$roles;
        if (!in_array($_SESSION['role'] ?? '', $roles, true)) {
            http_response_code(403);
            die('403 - Forbidden: You do not have permission to access this page.');
        }
    }
}