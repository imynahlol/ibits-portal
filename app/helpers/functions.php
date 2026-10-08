<?php
declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = '/'): string
{
    $base = rtrim(env('APP_URL', ''), '/');
    return $base . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    $path = ltrim($path, '/');
    $file = dirname(__DIR__, 2) . '/public/' . $path;
    $ver  = is_readable($file) ? filemtime($file) : time();
    return url($path) . '?v=' . $ver;
}

function view(string $name, array $data = []): void
{
    extract($data, EXTR_SKIP);
    $file = dirname(__DIR__) . '/views/' . str_replace('.', '/', $name) . '.php';
    if (!is_readable($file)) {
        throw new RuntimeException("View not found: {$name}");
    }
    require $file;
}

function json_input(): array
{
    $raw = file_get_contents('php://input') ?: '';
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function json_out(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function log_line(string $channel, string $message): void
{
    $dir = dirname(__DIR__, 2) . '/storage/logs';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    $line = sprintf("[%s] [%s] %s\n", date('Y-m-d H:i:s'), $channel, $message);
    @file_put_contents($dir . '/' . date('Y-m-d') . '.log', $line, FILE_APPEND);
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function peso(float|int $amount): string
{
    return '₱' . number_format((float) $amount, 2);
}