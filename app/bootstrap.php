<?php
declare(strict_types=1);

define('ROOT_DIR', dirname(__DIR__));
define('PUBLIC_DIR', ROOT_DIR . '/public');

$envFile = ROOT_DIR . '/.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
            continue;
        }

        [$k, $v] = explode('=', $line, 2);
        if (getenv(trim($k)) === false) {
            putenv(trim($k) . '=' . trim($v));
        }
    }
}

function env(string $key, string $default = ''): string
{
    $v = getenv($key);
    return $v === false ? $default : $v;
}

if (env('APP_DEBUG', 'false') === 'true') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

$docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
$pub     = realpath(PUBLIC_DIR) ?: PUBLIC_DIR;
$base    = $docRoot !== '' && str_starts_with($pub, $docRoot)
    ? substr($pub, strlen($docRoot))
    : '';

define('BASE_URL', rtrim(str_replace('\\', '/', $base), '/'));

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/lib/helpers.php';
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/data.php';
require_once __DIR__ . '/lib/layout.php';