<?php
declare(strict_types=1);

function currentUser(): ?array {
    return $_SESSION['user'] ?? null;
}

function isLoggedIn(): bool {
    return isset($_SESSION['user']);
}

function hasPermission(string $permission): bool {
    $permissions = $_SESSION['user']['permissions'] ?? [];
    return in_array($permission, $permissions, true);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /');
        exit;
    }
}
