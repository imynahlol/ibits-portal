<?php
declare(strict_types=1);

final class AuthMiddleware {
    //block authenticated users from a guest-only route.
    public static function requireLogin(): void {
        if (empty($_SESSION['user'])) {
            if (self::wantsJson()) {
                json_out(['ok' => false, 'error' => 'Unauthenticated'], 401);
            }
            redirect('/?route=login');
        }
    }

    public static function requireGuest(): void {
        if (!empty($_SESSION['user'])) {
            redirect('/?route=dashboard');
        }
    }

    public static function user(): ?array {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?string {
        return $_SESSION['user']['id'] ?? null;
    }

    private static function wantsJson(): bool {
        return str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
            || str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api');
    }
}