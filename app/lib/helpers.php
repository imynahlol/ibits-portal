<?php
declare(strict_types=1);

function e(mixed $v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** URL for a page inside public/, e.g. url('admin/events.php') */
function url(string $path = ''): string {
    return BASE_URL . '/' . ltrim($path, '/');
}
function asset(string $path): string {
    return url('assets/' . ltrim($path, '/'));
}

function redirect(string $path): never {
    header('Location: ' . (str_starts_with($path, 'http') ? $path : url($path)));
    exit;
}

/* ---- Flash messages ---- */
function flash(string $type, string $msg): void {
    $_SESSION['_flash'][] = ['type' => $type, 'msg' => $msg];
}
function flash_render(): string {
    $out = '';
    foreach ($_SESSION['_flash'] ?? [] as $f) {
        $out .= '<div class="alert alert-' . e($f['type']) . '" role="' . ($f['type'] === 'error' ? 'alert' : 'status') . '">' . e($f['msg']) . '</div>';
    }
    unset($_SESSION['_flash']);
    return $out;
}

/* ---- CSRF ---- */
function csrf_token(): string {
    if (empty($_SESSION['_csrf'])) $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['_csrf'];
}
function csrf_field(): string {
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}
/** Call at the top of any POST handler. */
function csrf_check(): void {
    $sent = $_POST['_csrf'] ?? '';
    if (!is_string($sent) || !hash_equals($_SESSION['_csrf'] ?? '', $sent)) {
        http_response_code(419);
        exit('Your session expired. Go back, refresh the page and try again.');
    }
}
function is_post(): bool {
    return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
}
function post(string $key): string {
    return trim((string)($_POST[$key] ?? ''));
}

function fmt_date(?string $dt, string $fmt = 'M j, Y'): string {
    return $dt ? date($fmt, strtotime($dt)) : '';
}
function initials(string $name): string {
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    $s = '';
    foreach (array_slice($parts, 0, 2) as $p) $s .= mb_strtoupper(mb_substr($p, 0, 1));
    return $s ?: '?';
}
