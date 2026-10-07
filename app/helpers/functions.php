<?php
// small utility functions used across the app

function redirect(string $url): void {
    header("Location: $url");
    exit;
}


function view(string $view, array $data = []): void {
    extract($data);
    $path = __DIR__ . "/../views/$view.php";
    if (!file_exists($path)) {
        die("View not found: $view");
    }
    require $path;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool {
    return !empty($_SESSION['csrf_token'])
        && is_string($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}


function old(string $key, $default = '') {
    return $_SESSION['old'][$key] ?? $default;
}


function flash(string $key, ?string $message = null) {
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    $msg = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $msg;
}


function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Validate a student ID number: 20XX-XXXXX-LQ-0
 * Example: 2025-00122-LQ-0
 */
function is_valid_student_id(string $id): bool {
    return (bool)preg_match('/^[0-9]{4}-[0-9]{5}-LQ-[0-9]$/', $id);
}

/**
 * Validate an adviser ID number: XXXXX
 * Example: 00812
 */
function is_valid_adviser_id(string $id): bool {
    return (bool)preg_match('/^[0-9]{5}$/', $id);
}

/**
 * Validate any id_number (student OR adviser).
 */
function is_valid_id_number(string $id): bool {
    return is_valid_student_id($id) || is_valid_adviser_id($id);
}