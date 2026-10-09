<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', 1); // Show errors temporarily to debug

/**
 * File attachments for Forms (PDF, Word, Excel + Link type)
 * Files stored outside public/, served only to signed-in users
 */

// Make sure ROOT_DIR exists — define if missing
if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', dirname(__DIR__));
}

const FORM_FILE_MAX = 10 * 1024 * 1024; // 10 MB

/** extension => [label, mime type, colour key] */
function form_file_types(): array {
    return [
        'pdf'  => ['PDF',   'application/pdf', 'pdf'],
        'doc'  => ['Word',  'application/msword', 'word'],
        'docx' => ['Word',  'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'word'],
        'xls'  => ['Excel', 'application/vnd.ms-excel', 'excel'],
        'xlsx' => ['Excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'excel'],
        'link' => ['Link',   '', 'link'],
    ];
}

function form_file_dir(): string {
    return ROOT_DIR . '/storage/form-files';
}

/** Get DB connection safely */
function form_get_db(): PDO {
    if (!function_exists('db')) {
        throw new RuntimeException('db() function not found — include your database connection file first');
    }
    return db();
}

/** Add file columns to forms table */
function form_files_migrate(): void {
    static $done = false;
    if ($done) return;
    $done = true;

    try {
        $pdo = form_get_db();
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        $have = [];

        if ($driver === 'sqlite') {
            $rows = $pdo->query('PRAGMA table_info(forms)')->fetchAll();
            foreach ($rows as $c) $have[] = $c['name'];
        } else {
            $rows = $pdo->query('SHOW COLUMNS FROM forms')->fetchAll();
            foreach ($rows as $c) $have[] = $c['Field'];
        }

        $cols = [
            'file_name' => $driver === 'sqlite' ? 'TEXT' : 'VARCHAR(255) NULL',
            'file_path' => $driver === 'sqlite' ? 'TEXT' : 'VARCHAR(255) NULL',
            'file_type' => $driver === 'sqlite' ? 'TEXT' : 'VARCHAR(10) NULL',
            'file_size' => $driver === 'sqlite' ? 'INTEGER' : 'INT NULL',
            'form_link' => $driver === 'sqlite' ? 'TEXT' : 'VARCHAR(2048) NULL',
        ];

        foreach ($cols as $name => $def) {
            if (!in_array($name, $have, true)) {
                $pdo->exec("ALTER TABLE forms ADD COLUMN $name $def");
            }
        }
    } catch (Throwable $e) {
        error_log('Migration error: ' . $e->getMessage());
    }
}

function form_ini_bytes(string $key): int {
    $v = trim((string)ini_get($key));
    if ($v === '' || $v === '0') return PHP_INT_MAX;
    $n = (int)$v;
    switch (strtolower(substr($v, -1))) {
        case 'g': $n *= 1024;
        case 'm': $n *= 1024;
        case 'k': $n *= 1024;
    }
    return $n > 0 ? $n : PHP_INT_MAX;
}

function form_file_limit(): int {
    return min(FORM_FILE_MAX, form_ini_bytes('upload_max_filesize'), form_ini_bytes('post_max_size'));
}

function form_size_label(int $bytes): string {
    if ($bytes === 0) return '';
    if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
    if ($bytes >= 1024)    return max(1, (int)round($bytes / 1024)) . ' KB';
    return $bytes . ' B';
}

/** Validate & store uploaded file */
function form_file_save(array $file): array {
    $err = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
    
    if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
        return ['error' => 'File too large (max ' . form_size_label(form_file_limit()) . ').'];
    }
    if ($err !== UPLOAD_ERR_OK || !is_uploaded_file((string)($file['tmp_name'] ?? ''))) {
        return ['error' => 'Upload failed. Please try again.'];
    }

    $size = (int)$file['size'];
    if ($size <= 0 || $size > form_file_limit()) {
        return ['error' => 'File too large (max ' . form_size_label(form_file_limit()) . ').'];
    }

    $orig = basename(str_replace('\\', '/', (string)$file['name']));
    $ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    
    if (!isset(form_file_types()[$ext])) {
        return ['error' => 'Only PDF, DOC, DOCX, XLS, XLSX allowed.'];
    }

    // Verify real file signature
    $head = (string)file_get_contents((string)$file['tmp_name'], false, null, 0, 8);
    $ok = match ($ext) {
        'pdf'                  => str_starts_with($head, '%PDF'),
        'docx', 'xlsx'         => str_starts_with($head, "PK\x03\x04"),
        'doc', 'xls'           => str_starts_with($head, "\xD0\xCF\x11\xE0\xF0\xA4\xB0\xB6"),
        default                => false,
    };

    if (!$ok) {
        return ['error' => 'Not a valid ' . form_file_types()[$ext][0] . ' file.'];
    }

    $dir = form_file_dir();
    if (!is_dir($dir) && !@mkdir($dir, 0750, true) && !is_dir($dir)) {
        return ['error' => 'Server error: cannot create storage folder.'];
    }

    $stored = bin2hex(random_bytes(16)) . '.' . $ext;
    if (!move_uploaded_file((string)$file['tmp_name'], "$dir/$stored")) {
        return ['error' => 'File could not be saved.'];
    }

    $clean = preg_replace('/[^\p{L}\p{N}\s._()\-]/u', '_', $orig) ?: $orig;
    
    return [
        'file_name' => mb_substr($clean, 0, 200),
        'file_path' => $stored,
        'file_type' => $ext,
        'file_size' => $size,
        'form_link' => null,
    ];
}

/** Save Online Link */
function form_link_save(string $title, string $link): array {
    if (!filter_var($link, FILTER_VALIDATE_URL)) {
        return ['error' => 'Enter a valid URL (http/https).'];
    }
    return [
        'file_name' => mb_substr(trim($title) ?: 'Link', 0, 200),
        'form_link' => $link,
        'file_type' => 'link',
        'file_size' => 0,
        'file_path' => null,
    ];
}

/** Permission check — match your actual session structure */
function form_can_edit(): bool {
    // Adjust this to match YOUR auth system exactly
    return !empty($_SESSION['user_role']) 
        && in_array($_SESSION['user_role'], ['Secretary', 'Admin']);
}

/** Delete stored file */
function form_file_delete(?string $stored): void {
    if (!$stored) return;
    $p = form_file_dir() . '/' . basename($stored);
    if (is_file($p)) @unlink($p);
}

// Run migration on load
form_files_migrate();