<?php
// loads .env and provides a reusable PDO connection via db()

/**
 * Load key=value pairs from a .env file into $_ENV
 */

function loadEnv(string $path): void {
    if (!file_exists($path)) {
        die("Environment file not found: $path");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (!str_contains($line, '=')) {
            continue;
        }
        [$name, $value] = explode('=', $line, 2);
        $name  = trim($name);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        $_ENV[$name] = $value;
    }
}

loadEnv(__DIR__ . '/../.env');

/**
 * Returns a singleton PDO connection.
 */
function db(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $host    = $_ENV['DB_HOST']    ?? 'localhost';
        $name    = $_ENV['DB_NAME']    ?? 'ibits_portal';
        $user    = $_ENV['DB_USER']    ?? 'root';
        $pass    = $_ENV['DB_PASS']    ?? '';
        $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$name;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            // In production, log this instead of displaying
            die("Database connection failed: " . $e->getMessage());
        }
    }

    return $pdo;
}