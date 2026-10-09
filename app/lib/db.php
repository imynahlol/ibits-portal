<?php
declare(strict_types=1);

/**
 * Database connection.
 *
 * Uses MySQL (see database/schema.sql). If MySQL cannot be reached and
 * APP_ENV=local, it falls back to a throw-away SQLite file in storage/ that is
 * created and filled with DEMO data, so the site can be tried without MySQL.
 * In any other environment a failed connection is a hard error.
 */
function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;

    $opts = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    if (env('DB_DRIVER', 'mysql') === 'mysql') {
        try {
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                env('DB_HOST', '127.0.0.1'), env('DB_PORT', '3306'), env('DB_DATABASE', 'ibits_portal'));
            return $pdo = new PDO($dsn, env('DB_USERNAME', 'root'), env('DB_PASSWORD', ''),
                $opts + [PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_TIMEOUT => 2]);
        } catch (PDOException $e) {
            error_log('[iBITS] MySQL unavailable: ' . $e->getMessage());
            if (env('APP_ENV', 'local') !== 'local') {
                http_response_code(500);
                exit('Database unavailable.');
            }
        }
    }

    // ---- Local demo fallback (SQLite) ----
    $file = ROOT_DIR . '/storage/demo.sqlite';
    $fresh = !is_file($file);
    $pdo = new PDO('sqlite:' . $file, null, null, $opts);
    $pdo->exec('PRAGMA foreign_keys = ON');
    if ($fresh) {
        $pdo->exec(file_get_contents(ROOT_DIR . '/database/schema.sqlite.sql'));
        require_once __DIR__ . '/demo_seed.php';
        demo_seed($pdo);
    }
    return $pdo;
}

function db_is_demo(): bool {
    return db()->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite';
}

function q(string $sql, array $params = []): PDOStatement {
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st;
}
function q_all(string $sql, array $params = []): array {
    return q($sql, $params)->fetchAll();
}
function q_one(string $sql, array $params = []): ?array {
    $r = q($sql, $params)->fetch();
    return $r === false ? null : $r;
}
function q_val(string $sql, array $params = []): mixed {
    $r = q($sql, $params)->fetchColumn();
    return $r === false ? null : $r;
}
