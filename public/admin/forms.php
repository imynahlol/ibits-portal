<?php
declare(strict_types=1);

/**
 * File attachments for Forms (PDF, Word, Excel).
 * Files are stored in storage/form-files (outside public/) and served only to
 * signed-in users through public/form-file.php.
 */

const FORM_FILE_MAX = 10 * 1024 * 1024; // 10 MB, also capped by the server's PHP limits

/** extension => [label, mime type, colour key] */
function form_file_types(): array {
    return [
        'pdf'  => ['PDF',   'application/pdf', 'pdf'],
        'doc'  => ['Word',  'application/msword', 'word'],
        'docx' => ['Word',  'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'word'],
        'xls'  => ['Excel', 'application/vnd.ms-excel', 'excel'],
        'xlsx' => ['Excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'excel'],
    ];
}

function form_file_dir(): string {
    return ROOT_DIR . '/storage/form-files';
}

/** Add the file columns to an existing forms table (no-op once they exist). */
function form_files_migrate(): void {
    static $done = false;
    if ($done) return;
    $done = true;
    $pdo = db();
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    $have = [];
    if ($driver === 'sqlite') {
        foreach ($pdo->query('PRAGMA table_info(forms)')->fetchAll() as $c) $have[] = $c['name'];
    } else {
        foreach ($pdo->query('SHOW COLUMNS FROM forms')->fetchAll() as $c) $have[] = $c['Field'];
    }
    $cols = ['file_name' => $driver === 'sqlite' ? 'TEXT' : 'VARCHAR(255) NULL',
             'file_path' => $driver === 'sqlite' ? 'TEXT' : 'VARCHAR(255) NULL',
             'file_type' => $driver === 'sqlite' ? 'TEXT' : 'VARCHAR(10) NULL',
             'file_size' => $driver === 'sqlite' ? 'INTEGER' : 'INT NULL'];
    foreach ($cols as $name => $def) {
        if (!in_array($name, $have, true)) $pdo->exec('ALTER TABLE forms ADD COLUMN ' . $name . ' ' . $def);
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
    if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
    if ($bytes >= 1024)    return max(1, (int)round($bytes / 1024)) . ' KB';
    return $bytes . ' B';
}

/**
 * Validate and store an uploaded file.
 * Returns ['error' => msg] on failure, otherwise the columns to save.
 */
function form_file_save(array $file): array {
    $err = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
        return ['error' => 'That file is too large (maximum ' . form_size_label(form_file_limit()) . ').'];
    }
    if ($err !== UPLOAD_ERR_OK || !is_uploaded_file((string)$file['tmp_name'])) {
        return ['error' => 'The file could not be uploaded. Please try again.'];
    }
    $size = (int)$file['size'];
    if ($size <= 0 || $size > form_file_limit()) {
        return ['error' => 'That file is too large (maximum ' . form_size_label(form_file_limit()) . ').'];
    }
    $orig = basename(str_replace('\\', '/', (string)$file['name']));
    $ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    if (!isset(form_file_types()[$ext])) {
        return ['error' => 'Only PDF, Word (.doc, .docx) or Excel (.xls, .xlsx) files are allowed.'];
    }
    // Check the real file signature, not only the extension.
    $head = (string)file_get_contents((string)$file['tmp_name'], false, null, 0, 8);
    $ok = match ($ext) {
        'pdf'          => str_starts_with($head, '%PDF'),
        'docx', 'xlsx' => str_starts_with($head, "PK\x03\x04"),
        default        => str_starts_with($head, "\xD0\xCF\x11\xE0"),
    };
    if (!$ok) return ['error' => 'That file does not look like a real ' . form_file_types()[$ext][0] . ' document.'];

    $dir = form_file_dir();
    if (!is_dir($dir) && !@mkdir($dir, 0750, true) && !is_dir($dir)) {
        return ['error' => 'The server could not create the upload folder.'];
    }
    $stored = bin2hex(random_bytes(16)) . '.' . $ext;
    if (!move_uploaded_file((string)$file['tmp_name'], $dir . '/' . $stored)) {
        return ['error' => 'The file could not be saved. Please try again.'];
    }
    $clean = trim((string)preg_replace('/[^\p{L}\p{N}\s._()\-]/u', '_', $orig));
    return ['file_name' => mb_substr($clean, 0, 200), 'file_path' => $stored, 'file_type' => $ext, 'file_size' => $size];
}

function form_file_delete(?string $stored): void {
    if (!$stored) return;
    $p = form_file_dir() . '/' . basename($stored);
    if (is_file($p)) @unlink($p);
}
