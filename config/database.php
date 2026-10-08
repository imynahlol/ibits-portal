<?php
declare(strict_types=1);
function env(string $key, mixed $default = null): mixed
{
    static $vars = null;

    if ($vars === null) {
        $vars = [];
        $file = dirname(__DIR__) . '/.env';
        if (is_readable($file)) {
            foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                    continue;
                }
                [$k, $v] = explode('=', $line, 2);
                $v = trim($v, " \t\n\r\0\x0B\"'");
                $vars[trim($k)] = $v;
            }
        }
        foreach ($_ENV as $k => $v) {
            $vars[$k] = $v;
        }
    }

    return $vars[$key] ?? $default;
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $driver = env('DB_DRIVER', 'mysql');
    $host   = env('DB_HOST', '127.0.0.1');
    $port   = env('DB_PORT', '3306');
    $name   = env('DB_NAME', 'ibits_portal');
    $user   = env('DB_USER', 'root');
    $pass   = env('DB_PASS', '');

    $dsn = match ($driver) {
        'sqlite' => 'sqlite:' . dirname(__DIR__) . '/database/ibits.sqlite',
        'pgsql'  => "pgsql:host={$host};port={$port};dbname={$name}",
        default  => "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
    };

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        error_log('[DB] ' . $e->getMessage());
        if (env('APP_DEBUG') === 'true') {
            throw $e;
        }
        http_response_code(500);
        exit('Database unavailable.');
    }
    
    return $pdo;
}