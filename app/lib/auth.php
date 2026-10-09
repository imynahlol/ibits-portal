<?php
declare(strict_types=1);

/* ---- Session user ---- */
function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}
function is_logged_in(): bool {
    return isset($_SESSION['user']);
}
function is_admin(): bool {
    return (current_user()['account_type'] ?? '') === 'admin';
}

/** Where each account type lands after login. */
function home_for(array $user): string {
    return $user['account_type'] === 'admin' ? 'admin/index.php' : 'member/index.php';
}

/* ---- Permissions (position based) ---- */
function user_features(?array $user = null): array {
    $user ??= current_user();
    if (!$user || $user['account_type'] !== 'admin') return [];
    return POSITIONS[$user['position'] ?? ''] ?? [];
}
function can_access(string $feature): bool {
    return isset(user_features()[$feature]);
}
function can_manage(string $feature): bool {
    return (user_features()[$feature] ?? '') === 'manage';
}

/* ---- Guards (call at the top of a page) ---- */
function require_member(): array {
    $u = current_user();
    if (!$u) { flash('error', 'Please sign in first.'); redirect('login.php'); }
    if ($u['account_type'] !== 'member') redirect(home_for($u));
    return $u;
}
function require_admin(): array {
    $u = current_user();
    if (!$u) { flash('error', 'Please sign in first.'); redirect('login.php?as=admin'); }
    if ($u['account_type'] !== 'admin') redirect(home_for($u));
    return $u;
}
/** Admin page that needs a specific feature for the logged-in position. */
function require_feature(string $feature): array {
    $u = require_admin();
    if (!can_access($feature)) {
        http_response_code(403);
        flash('error', 'Your position (' . $u['position'] . ') does not have access to ' . (FEATURES[$feature][0] ?? $feature) . '.');
        redirect('admin/index.php');
    }
    return $u;
}
/** Block writes for read-only positions. */
function require_manage(string $feature): void {
    if (!can_manage($feature)) {
        http_response_code(403);
        exit('Your position can view this page but not change it.');
    }
}

/* ---- Login / logout ---- */
const MAX_ATTEMPTS = 5;
const LOCK_SECONDS = 60;

function login_locked_for(): int {
    $a = $_SESSION['_attempts'] ?? ['n' => 0, 'until' => 0];
    return max(0, (int)$a['until'] - time());
}

/**
 * Check credentials. For admins the chosen position must match the account's
 * position. Returns the user row or null; the error is deliberately generic.
 */
function attempt_login(string $as, string $idNumber, string $password, string $position = ''): ?array {
    if (login_locked_for() > 0) return null;

    $u = q_one('SELECT * FROM users WHERE id_number = ? AND status = ?', [$idNumber, 'active']);
    $ok = $u
        && password_verify($password, $u['password_hash'])
        && $u['account_type'] === $as
        && ($as === 'member' || ($position !== '' && $u['position'] === $position));

    if (!$ok) {
        // Burn the same time whether or not the account exists.
        if (!$u) password_verify($password, password_hash('dummy', PASSWORD_DEFAULT));
        $a = $_SESSION['_attempts'] ?? ['n' => 0, 'until' => 0];
        $a['n']++;
        if ($a['n'] >= MAX_ATTEMPTS) { $a = ['n' => 0, 'until' => time() + LOCK_SECONDS]; }
        $_SESSION['_attempts'] = $a;
        return null;
    }

    unset($_SESSION['_attempts']);
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id'           => (int)$u['id'],
        'full_name'    => $u['full_name'],
        'id_number'    => $u['id_number'],
        'account_type' => $u['account_type'],
        'position'     => $u['position'],
        'program'      => $u['program'],
        'year_level'   => $u['year_level'],
    ];
    return $_SESSION['user'];
}

function do_logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}
