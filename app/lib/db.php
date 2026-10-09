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
            $pdo = new PDO($dsn, env('DB_USERNAME', 'root'), env('DB_PASSWORD', ''),
                $opts + [PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_TIMEOUT => 2]);
            migrate_user_password_flag($pdo);
            return $pdo;
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
    migrate_user_password_flag($pdo);
    return $pdo;
}

/** Upgrade existing installations once; newly upgraded officers receive the required default password. */
function migrate_user_password_flag(PDO $pdo): void {
    static $done = false;
    if ($done) return;
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver === 'sqlite') {
        $columns = $pdo->query('PRAGMA table_info(users)')->fetchAll();
        $hasColumn = false;
        foreach ($columns as $column) if (($column['name'] ?? '') === 'must_change_password') $hasColumn = true;
    } else {
        $st = $pdo->query("SHOW COLUMNS FROM users LIKE 'must_change_password'");
        $hasColumn = (bool)$st->fetch();
    }
    if (!$hasColumn) {
        $pdo->exec($driver === 'sqlite'
            ? 'ALTER TABLE users ADD COLUMN must_change_password INTEGER NOT NULL DEFAULT 0'
            : 'ALTER TABLE users ADD COLUMN must_change_password TINYINT(1) NOT NULL DEFAULT 0');
        $hash = '$2y$12$2bWkB.8FydBaORJzl13nP.A0qQrViA985uMTHBXJIikvY465Ocqye';
        $st = $pdo->prepare("UPDATE users SET password_hash = ?, must_change_password = 1 WHERE account_type = 'admin'");
        $st->execute([$hash]);
    }
    $done = true;
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
