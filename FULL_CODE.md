# iBITS Portal — Full Split Source Code

Generated from the uploaded monolithic portal while preserving its frontend behavior.

## `.env`

```text
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost/ibits-portal/public

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ibits_portal
DB_USERNAME=root
DB_PASSWORD=

```

## `.env.example`

```example
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost/ibits-portal/public

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ibits_portal
DB_USERNAME=root
DB_PASSWORD=

```

## `.gitignore`

```text
/.env
/public/uploads/*
!/public/uploads/.gitkeep
/storage/logs/*
!/storage/logs/.gitkeep

```

## `README.md`

```md
# iBITS Portal

A structured PHP/HTML split of the original iBITS Portal single-page prototype.

## Structure

- `app/` — application logic, controllers, models, helpers, and views
- `config/` — database connection
- `database/` — MySQL schema and seed data
- `public/` — web root, assets, uploads, and entry point
- `storage/logs/` — application logs
- `.env` — local configuration (do not commit)
- `.env.example` — configuration template

## Run locally

1. Create a MySQL database and import:
   - `database/schema.sql`
   - `database/seeds.sql`
2. Copy `.env.example` to `.env` and set the database credentials.
3. Point Apache/Nginx to the `public/` directory, or run:
   ```bash
   php -S localhost:8000 -t public
   ```
4. Open `http://localhost:8000`.

## Important

The original frontend behavior is preserved as a JavaScript prototype. Login, registration, event editing, fines, tickets, and population data still use the mock client-side data in `public/assets/js/app.js`.

The PHP models/controllers and database schema are separated and ready for replacing the mock data with real CRUD/API operations. No real authentication is claimed until those endpoints are wired to the frontend.

## Notes

- The original embedded iBITS logo was extracted into `public/assets/images/ibits-logo.png`.
- Uploaded event images are intended for `public/uploads/`.
- Keep `.env` out of version control.

```

## `app/bootstrap.php`

```php
<?php
declare(strict_types=1);

session_start();

$envFile = dirname(__DIR__) . '/.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        if (getenv($key) === false) putenv(trim($key) . '=' . trim($value));
    }
}

require_once dirname(__DIR__) . '/config/database.php';
require_once __DIR__ . '/helpers/auth.php';
require_once __DIR__ . '/helpers/view.php';

```

## `app/controllers/AuthController.php`

```php
<?php
declare(strict_types=1);

final class AuthController {
    public function login(string $studentId, string $password): bool {
        global $pdo;
        if (!$pdo) return false;
        $user = (new User($pdo))->findByStudentId($studentId);
        if (!$user || empty($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
            return false;
        }
        $_SESSION['user'] = $user;
        return true;
    }

    public function logout(): void {
        $_SESSION = [];
        session_destroy();
    }
}

```

## `app/controllers/PortalController.php`

```php
<?php
declare(strict_types=1);

final class PortalController {
    public function index(): void {
        require __DIR__ . '/../../public/index.php';
    }
}

```

## `app/helpers/auth.php`

```php
<?php
declare(strict_types=1);

function currentUser(): ?array {
    return $_SESSION['user'] ?? null;
}

function isLoggedIn(): bool {
    return isset($_SESSION['user']);
}

function hasPermission(string $permission): bool {
    $permissions = $_SESSION['user']['permissions'] ?? [];
    return in_array($permission, $permissions, true);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /');
        exit;
    }
}

```

## `app/helpers/view.php`

```php
<?php
declare(strict_types=1);

function view(string $path, array $data = []): void {
    extract($data, EXTR_SKIP);
    require __DIR__ . '/../views/' . $path . '.php';
}

```

## `app/models/Event.php`

```php
<?php
declare(strict_types=1);

final class Event {
    public function __construct(private ?PDO $db) {}

    public function all(): array {
        if (!$this->db) return [];
        return $this->db->query('SELECT * FROM events ORDER BY start_date, start_time')->fetchAll();
    }
}

```

## `app/models/Fine.php`

```php
<?php
declare(strict_types=1);

final class Fine {
    public function __construct(private ?PDO $db) {}

    public function forUser(int $userId): array {
        if (!$this->db) return [];
        $stmt = $this->db->prepare('SELECT * FROM fines WHERE user_id = ? ORDER BY fine_date DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}

```

## `app/models/Ticket.php`

```php
<?php
declare(strict_types=1);

final class Ticket {
    public function __construct(private ?PDO $db) {}

    public function forUser(int $userId): array {
        if (!$this->db) return [];
        $stmt = $this->db->prepare('SELECT * FROM tickets WHERE owner_id = ? ORDER BY id DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}

```

## `app/models/User.php`

```php
<?php
declare(strict_types=1);

final class User {
    public function __construct(private ?PDO $db) {}

    public function findByStudentId(string $studentId): ?array {
        if (!$this->db) return null;
        $stmt = $this->db->prepare('SELECT * FROM users WHERE student_id = ? LIMIT 1');
        $stmt->execute([$studentId]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $name, string $studentId, string $program, string $password): int {
        if (!$this->db) throw new RuntimeException('Database unavailable.');
        $stmt = $this->db->prepare('INSERT INTO users (full_name, student_id, program, role, password_hash) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$name, $studentId, $program, 'Member', password_hash($password, PASSWORD_DEFAULT)]);
        return (int)$this->db->lastInsertId();
    }
}

```

## `app/views/admin/cbl.php`

```php
<div class="view-section w-full p-6 lg:p-10" id="view-admin-cbl">
<div class="mb-8">
<h1 class="text-2xl font-bold text-primary dark:text-white">CBL Workflow &amp; Verification</h1>
<p class="text-textMuted text-sm">Review, approve, or reject proposed changes to the Constitution and By-Laws.</p>
</div>
<div class="bg-surface rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
<h3 class="font-bold text-lg text-primary mb-6 border-b border-gray-100 pb-4">Pending Amendments</h3>
<!-- Workflow Tracker UI -->
<div class="mb-8 p-6 bg-gray-50 rounded-xl border border-gray-200">
<div class="flex justify-between mb-2">
<span class="text-sm font-bold text-primary">Amendment #2026-04: Attendance Fines</span>
<span class="text-xs font-semibold px-2 py-1 bg-yellow-100 text-yellow-700 rounded">Needs Your Approval</span>
</div>
<p class="text-sm text-gray-600 mb-6">Proposed change to Section 4.1 regarding unexcused absences during general assemblies.</p>
<!-- Progress Bar -->
<div class="relative w-full h-2 bg-gray-200 rounded-full mb-8">
<div class="absolute top-0 left-0 h-2 bg-green-500 rounded-full w-2/3"></div>
<!-- Nodes -->
<div class="absolute -top-3 left-0 w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center shadow-md border-2 border-white"><i class="fa-solid fa-check text-xs"></i></div>
<div class="absolute -top-3 left-1/3 w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center shadow-md border-2 border-white transform -translate-x-1/2"><i class="fa-solid fa-check text-xs"></i></div>
<div class="absolute -top-3 left-2/3 w-8 h-8 rounded-full bg-secondary text-primary flex items-center justify-center shadow-md border-2 border-white transform -translate-x-1/2"><i class="fa-solid fa-spinner animate-spin text-xs"></i></div>
<div class="absolute -top-3 right-0 w-8 h-8 rounded-full bg-gray-300 text-gray-500 flex items-center justify-center border-2 border-white"><i class="fa-solid fa-lock text-xs"></i></div>
</div>
<!-- Labels -->
<div class="flex justify-between text-xs font-medium text-gray-500">
<div class="text-center">Initiated<br/><span class="text-[10px]">Sec. Smith</span></div>
<div class="text-center transform -translate-x-4">Adviser<br/><span class="text-[10px] text-green-600">Approved</span></div>
<div class="text-center transform -translate-x-4">President<br/><span class="text-[10px] text-green-600">Approved</span></div>
<div class="text-center">Vice President<br/><span class="text-[10px] text-yellow-600">Pending</span></div>
</div>
</div>
<div class="flex justify-end gap-3 border-t border-gray-100 pt-4">
<button class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors text-textDark">View Document</button>
<button class="px-4 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors" onclick="showModal('Reject Amendment', 'Are you sure you want to reject this amendment?')">Reject</button>
<button class="px-4 py-2 btn-primary rounded-lg text-sm font-medium" onclick="showModal('Approve Amendment', 'Approve and publish this amendment to the official CBL?', true)">Approve &amp; Publish</button>
</div>
</div>
</div>

```

## `app/views/admin/org.php`

```php
<div class="view-section w-full p-6 lg:p-10" id="view-admin-org">
<div class="flex justify-between items-center mb-8">
<div>
<h1 class="text-2xl font-bold text-primary dark:text-white">Organization Management</h1>
<p class="text-textMuted text-sm">Edit organizational structure, members, and roles.</p>
</div>
<button class="btn-primary px-4 py-2 rounded-lg text-sm font-medium shadow-sm flex items-center gap-2">
<i class="fa-solid fa-plus"></i> Add Officer
                        </button>
</div>
<div class="bg-surface rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-gray-50 border-b border-gray-100 text-sm text-gray-500 uppercase tracking-wider">
<th class="p-4 font-medium">Member</th>
<th class="p-4 font-medium">Role</th>
<th class="p-4 font-medium">Program</th>
<th class="p-4 font-medium text-right">Actions</th>
</tr>
</thead>
<tbody class="text-sm">
<tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
<td class="p-4 flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-secondary flex items-center justify-center text-primary font-bold text-xs">AL</div>
<div>
<p class="font-bold text-primary">Ada Lovelace</p>
<p class="text-xs text-textMuted">2023-0012-LQ-0</p>
</div>
</td>
<td class="p-4"><span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-semibold">President</span></td>
<td class="p-4 text-textDark">BSIT - 4th Yr</td>
<td class="p-4 text-right">
<button class="text-gray-400 hover:text-primary transition-colors p-1"><i class="fa-solid fa-pen"></i></button>
</td>
</tr>
<tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
<td class="p-4 flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-primary text-secondary flex items-center justify-center font-bold text-xs">JG</div>
<div>
<p class="font-bold text-primary">Johahnie Gilbuena</p>
<p class="text-xs text-textMuted">2024-0001-LQ-0</p>
</div>
</td>
<td class="p-4"><span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-semibold">Vice President</span></td>
<td class="p-4 text-textDark">BSIT - 3rd Yr</td>
<td class="p-4 text-right">
<button class="text-gray-400 hover:text-primary transition-colors p-1"><i class="fa-solid fa-pen"></i></button>
</td>
</tr>
<!-- More mock rows -->
</tbody>
</table>
</div>
</div>

```

## `app/views/auth/login.php`

```php
<div class="view-section w-full min-h-screen relative overflow-hidden zd pt-24 pb-10 px-4" id="view-login">
<canvas class="rain absolute inset-0 w-full h-full"></canvas>
<div class="relative z-10 max-w-6xl mx-auto grid md:grid-cols-2 gap-10 items-center min-h-[70vh]">
<div class="text-center login-in" style="--d:.05s">
<div class="coin-wrap"><div class="coin"><img alt="iBITS logo" class="logo-img"/></div></div>
<h1 class="text-5xl md:text-6xl font-bold gt">iBITS Portal</h1><p class="text-textMuted mt-2">We Code as One</p>
</div>
<div class="olive p-8 max-w-md w-full mx-auto login-in" style="--d:.15s">
<div class="border-l-4 border-secondary pl-4 mb-6"><h2 class="text-2xl font-bold text-primary" id="lg-title">SECURE ACCESS</h2><p class="text-xs tracking-widest text-textMuted" id="lg-sub">IDENTIFY YOURSELF TO PROCEED.</p></div>
<div class="space-y-4" data-panel="choice">
<button class="tilt w-full flex items-center gap-4 p-5 rounded-2xl border border-gray-200 bg-white text-left hover:border-primary glass-btn" onclick="loginMode('member')"><span class="w-12 h-12 rounded-xl bg-primary text-secondary flex items-center justify-center text-xl"><i class="fa-solid fa-user"></i></span><span><b class="text-primary block">Member Access</b><span class="text-xs text-textMuted">Fines, events, forms and help desk</span></span></button>
<button class="tilt w-full flex items-center gap-4 p-5 rounded-2xl border border-gray-200 bg-white text-left hover:border-primary glass-btn" onclick="loginMode('admin')"><span class="w-12 h-12 rounded-xl bg-secondary text-primary flex items-center justify-center text-xl"><i class="fa-solid fa-shield-halved"></i></span><span><b class="text-primary block">Admin Console</b><span class="text-xs text-textMuted">Officers and advisers</span></span></button>
</div>
<form class="hidden space-y-4" data-panel="member admin" onsubmit="doLogin(event)">
<div class="hidden" data-only="admin"><label class="block text-sm font-medium mb-1">Designation</label><select class="w-full px-3 py-3 border border-gray-200 rounded-xl text-sm bg-white" id="lg-role"><option>President</option><option selected="">Vice President</option><option>Secretary</option><option>Treasurer</option><option>Assistant Treasurer</option><option>Auditor</option><option>Assistant Auditor</option><option>Institutional</option><option>Adviser</option></select></div>
<div><label class="block text-sm font-medium mb-1">Student / Adviser Number</label><input autocomplete="username" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary" id="lg-id" placeholder="Student ID / ID number" required=""/></div>
<div><label class="block text-sm font-medium mb-1">Password</label><div class="relative"><input autocomplete="current-password" class="w-full px-4 py-3 pr-12 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary" id="lg-pw" placeholder="Password" required="" type="password"/><button aria-label="Show password" class="absolute right-3 top-3 text-textMuted" onclick="togglePw('lg-pw',this)" type="button"><i class="fa-solid fa-eye"></i></button></div></div>
<div class="flex justify-between text-xs"><label class="flex items-center gap-2 text-textMuted"><input class="rounded" type="checkbox"/> Keep Session Active</label><a class="text-primary font-medium hover:underline" href="#" onclick="showModal('Forgot password','Password reset needs the backend. For now, file an Account Concern ticket after contacting an officer.');return false">Forgot password?</a></div>
<button class="w-full btn-secondary py-3 rounded-xl text-base" id="lg-btn" type="submit">Sign In</button>
<div class="text-center text-sm"><a class="text-textMuted hover:text-primary" href="#" onclick="loginMode('choice');return false"><i class="fa-solid fa-arrow-left"></i> Return to dashboard</a></div>
<p class="text-center text-sm text-textMuted" data-only="member">Don't have an account yet? <a class="text-primary font-semibold hover:underline" href="#" onclick="loginMode('register');return false">Join iBITS</a></p>
</form>
<form class="hidden space-y-3" data-panel="register" onsubmit="doRegister(event)">
<input class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm" id="rg-name" placeholder="Full Name" required=""/>
<input class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm" id="rg-id" placeholder="Student ID / ID number" required=""/>
<input class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm" id="rg-prog" placeholder="Program &amp; Year (e.g. BSIT 2)" required=""/>
<div class="relative"><input class="w-full px-4 py-3 pr-12 border border-gray-200 rounded-xl text-sm" id="rg-pw" oninput="checkPw()" placeholder="Password" required="" type="password"/><button aria-label="Show password" class="absolute right-3 top-3 text-textMuted" onclick="togglePw('rg-pw',this)" type="button"><i class="fa-solid fa-eye"></i></button></div>
<ul class="text-xs space-y-1 text-textMuted" id="pw-rules"></ul>
<button class="w-full btn-secondary py-3 rounded-xl" type="submit">Register</button>
<div class="text-center text-sm"><a class="text-textMuted hover:text-primary" href="#" onclick="loginMode('member');return false"><i class="fa-solid fa-arrow-left"></i> Back to sign in</a></div>
</form>
</div>
</div>
</div>

```

## `app/views/layouts/chat-fab.php`

```php
<button aria-label="Open AI Help Desk" class="hidden fixed bottom-5 right-5 z-40 w-14 h-14 rounded-full bg-primary text-secondary text-xl shadow-xl flex items-center justify-center hover:scale-105 transition-transform" id="chat-fab" onclick="toggleChat()"><i class="fa-solid fa-headset"></i></button>

```

## `app/views/layouts/chat-panel.php`

```php
<div class="hidden fixed bottom-24 right-4 z-40 w-[min(94vw,380px)] h-[min(72vh,540px)] bg-white rounded-2xl shadow-2xl border border-gray-200 flex-col overflow-hidden" id="chat-panel">
<div class="bg-primary text-white px-4 py-3 flex items-center justify-between"><div><b class="text-sm">iBITS HELP DESK</b><div class="text-xs text-secondary">AI ASSISTANT</div></div><div class="flex gap-3"><button aria-label="Minimize" onclick="toggleChat(false)"><i class="fa-solid fa-minus"></i></button><button aria-label="Close" onclick="toggleChat(false)"><i class="fa-solid fa-xmark"></i></button></div></div>
<div class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50" id="chat-msgs"></div>
<form class="p-3 border-t border-gray-200 flex gap-2 bg-white" onsubmit="sendChat(event)"><input autocomplete="off" class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm" id="chat-in" placeholder="Type your question..."/><button aria-label="Send" class="btn-secondary px-4 rounded-lg text-sm" id="chat-send"><i class="fa-solid fa-paper-plane"></i></button></form>
</div>

```

## `app/views/layouts/custom-modal.php`

```php
<div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300" id="custom-modal">
<div class="bg-surface rounded-2xl p-6 shadow-2xl max-w-sm w-full transform scale-95 transition-transform duration-300" id="modal-content">
<h3 class="text-lg font-bold text-primary mb-2" id="modal-title">Title</h3>
<p class="text-sm text-textMuted mb-6" id="modal-body">Message body goes here.</p>
<div class="flex justify-end gap-3">
<button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50" onclick="closeModal()">Cancel</button>
<button class="px-4 py-2 btn-primary rounded-lg text-sm font-medium" id="modal-confirm-btn" onclick="confirmModal()">Confirm</button>
</div>
</div>
</div>

```

## `app/views/layouts/footer.php`

```php
<footer class="bg-slate-50 text-textMuted py-8 border-t border-gray-200 mt-auto transition-all" id="app-footer">
<div class="container mx-auto px-6 text-center text-sm">
<div class="flex justify-center gap-4 mb-4">
<a class="hover:text-secondary transition-colors text-xl" href="#"><i class="fa-brands fa-facebook"></i></a>
<a class="hover:text-secondary transition-colors text-xl" href="#"><i class="fa-brands fa-github"></i></a>
<a class="hover:text-secondary transition-colors text-xl" href="#"><i class="fa-brands fa-discord"></i></a>
</div>
<p>© 2026 iBITS Organization. "We Code as One". All rights reserved.</p>
</div>
</footer>

```

## `app/views/layouts/m-event.php`

```php
<div class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" id="m-event" onclick="if(event.target===this)closeM('m-event')"><div class="modal-in bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto" id="m-event-c"></div></div>

```

## `app/views/layouts/m-form.php`

```php
<div class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" id="m-form" onclick="if(event.target===this)closeM('m-form')"><form class="modal-in bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto p-6 space-y-3" id="m-form-c" onsubmit="saveEvent(event)"></form></div>

```

## `app/views/layouts/m-ticket.php`

```php
<div class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" id="m-ticket" onclick="if(event.target===this)closeM('m-ticket')"><form class="modal-in bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-3" onsubmit="saveTicket(event)"><h3 class="text-lg font-bold text-primary">Create New Ticket</h3><input class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" id="t-sub" placeholder="Subject" required=""/><select class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white" id="t-cat"><option>Internet / Portal Issue</option><option>Account Concern</option><option>Form Request</option><option>Fines Inquiry</option><option>Other</option></select><textarea class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" id="t-desc" placeholder="Describe your concern" required="" rows="4"></textarea><div class="flex justify-end gap-2"><button class="px-4 py-2 border border-gray-200 rounded-lg text-sm" onclick="closeM('m-ticket')" type="button">Cancel</button><button class="btn-primary px-4 py-2 rounded-lg text-sm">Submit</button></div></form></div>

```

## `app/views/layouts/mobile-header.php`

```php
<header class="hidden md:hidden bg-surface shadow-sm px-4 py-3 flex justify-between items-center z-10" id="auth-mobile-header">
<div class="flex items-center gap-2">
<div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-secondary font-bold">
<i class="fa-solid fa-code"></i>
</div>
<span class="font-bold text-primary">iBITS</span>
</div>
<button class="text-primary p-2" onclick="document.getElementById('sidebar').classList.toggle('hidden'); document.getElementById('sidebar').classList.toggle('flex');">
<i class="fa-solid fa-bars text-xl"></i>
</button>
</header>

```

## `app/views/layouts/public-nav.php`

```php
<header class="absolute w-full z-30 zd" id="public-nav">
<div class="mx-auto max-w-6xl px-4 pt-3"><div class="glass-pill flex items-center justify-between rounded-full px-5 py-2.5">
<div class="flex items-center gap-3 cursor-pointer" onclick="navigate('public-home')"><img alt="iBITS logo" class="logo-img w-9 h-9 rounded-full"/><span class="font-bold text-white">iBITS PUPLC</span></div>
<nav class="hidden md:flex items-center gap-7 text-sm text-white">
<div class="dd relative"><button class="flex items-center gap-1">About <i class="fa-solid fa-chevron-down text-xs"></i></button><div class="dd-m"><a href="#" onclick="navigate('public-about')">Who We Are</a><a href="#" onclick="navigate('public-about')">Mission &amp; Vision</a><a href="#" onclick="navigate('public-org')">Org Structure</a></div></div>
<div class="dd relative"><button class="flex items-center gap-1">Community <i class="fa-solid fa-chevron-down text-xs"></i></button><div class="dd-m"><a href="#" onclick="navigate('public-org')">Members</a><a href="#" onclick="navigate('public-faqs')">FAQs</a><a href="#" onclick="navigate('login')">Help Desk</a></div></div>
<a href="#" onclick="navigate('public-events')">Events</a>
<button class="tilt px-4 py-1.5 rounded-xl font-bold text-white" onclick="navigate('register')" style="background:#06215c;box-shadow:0 4px 0 #031236">Get ID</button>
<button aria-label="Log in" class="w-9 h-9 rounded-full bg-white text-primary flex items-center justify-center" onclick="navigate('login')"><i class="fa-solid fa-user"></i></button>
</nav>
<div class="md:hidden flex gap-2"><button class="text-sm font-semibold text-white" onclick="navigate('login')">Login</button><button class="px-3 py-1 rounded-lg text-sm font-bold text-white" onclick="navigate('register')" style="background:#06215c">Get ID</button></div>
</div></div>
</header>

```

## `app/views/layouts/sidebar.php`

```php
<aside class="hidden w-64 bg-white text-primary flex-col border-r border-gray-200 shadow-sm z-20 transition-all duration-300" id="sidebar">
<div class="p-6 flex items-center gap-3 border-b border-gray-200">
<div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-secondary font-bold text-xl">
<i class="fa-solid fa-code"></i>
</div>
<div>
<h2 class="text-xl font-bold tracking-wider">iBITS</h2>
<p class="text-xs text-secondaryHover font-medium">We Code as One</p>
</div>
</div>
<div class="p-4 flex items-center gap-3 bg-slate-50">
<img alt="Profile" class="w-10 h-10 rounded-full border-2 border-secondary" src="https://placehold.co/100x100/fad517/06215c?text=VP"/>
<div class="overflow-hidden">
<p class="text-sm font-semibold truncate" id="auth-user-name">Johahnie Gilbuena</p>
<p class="text-xs text-secondaryHover font-semibold truncate" id="auth-user-role">Vice President</p>
</div>
</div>
<nav class="flex-1 overflow-y-auto py-4">
<p class="px-6 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">General</p>
<a class="nav-link block px-6 py-3 text-sm hover:bg-slate-100 hover:text-primary transition-colors border-l-4 border-transparent active-nav" href="#" onclick="navigate('dashboard')"><i class="fa-solid fa-house w-6"></i> Dashboard</a>
<a class="nav-link block px-6 py-3 text-sm hover:bg-slate-100 hover:text-primary transition-colors border-l-4 border-transparent" href="#" onclick="navigate('fines')"><i class="fa-solid fa-file-invoice-dollar w-6"></i> My Fines</a>
<a class="nav-link block px-6 py-3 text-sm hover:bg-slate-100 hover:text-primary transition-colors border-l-4 border-transparent" href="#" onclick="navigate('events')"><i class="fa-solid fa-calendar-alt w-6"></i> Events / GPOA</a>
<a class="nav-link block px-6 py-3 text-sm hover:bg-slate-100 hover:text-primary transition-colors border-l-4 border-transparent" href="#" onclick="navigate('forms')"><i class="fa-solid fa-file-alt w-6"></i> Forms Hub</a>
<a class="nav-link block px-6 py-3 text-sm hover:bg-slate-100 hover:text-primary transition-colors border-l-4 border-transparent" href="#" onclick="navigate('helpdesk')"><i class="fa-solid fa-headset w-6"></i> Help Desk</a>
<div class="mt-6" id="admin-menu">
<p class="px-6 text-xs font-semibold text-secondaryHover uppercase tracking-wider mb-2">Admin Tools</p>
<a class="nav-link block px-6 py-3 text-sm hover:bg-slate-100 hover:text-primary transition-colors border-l-4 border-transparent" href="#" onclick="navigate('admin-org')"><i class="fa-solid fa-sitemap w-6"></i> Manage Org</a>
<a class="nav-link block px-6 py-3 text-sm hover:bg-slate-100 hover:text-primary transition-colors border-l-4 border-transparent" href="#" onclick="navigate('admin-cbl')"><i class="fa-solid fa-scale-balanced w-6"></i> CBL Workflow</a>
</div>
<p class="px-6 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 mt-6">Settings</p>
<a class="nav-link block px-6 py-3 text-sm hover:bg-slate-100 hover:text-primary transition-colors border-l-4 border-transparent" href="#" onclick="navigate('settings')"><i class="fa-solid fa-cog w-6"></i> Preferences</a>
</nav>
<div class="p-4 border-t border-gray-200">
<button class="w-full py-2 px-4 bg-slate-100 text-primary hover:bg-red-600 hover:text-white rounded text-sm transition-colors flex items-center justify-center gap-2" onclick="logout()">
<i class="fa-solid fa-sign-out-alt"></i> Logout
                </button>
</div>
</aside>

```

## `app/views/member/dashboard.php`

```php
<div class="view-section w-full p-6 lg:p-10" id="view-dashboard">
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 animate-fade-in">
<div>
<h1 class="text-2xl font-bold text-primary dark:text-white">Dashboard Overview</h1>
<p class="text-textMuted text-sm">Welcome back, <span class="font-medium text-primary dark:text-gray-300" id="dash-greeting-name">Johahnie</span>!</p>
</div>
<div class="mt-4 md:mt-0 flex gap-3">
<button class="bg-surface border border-gray-200 p-2 rounded-lg text-textMuted hover:text-primary transition-colors shadow-sm">
<i class="fa-regular fa-bell"></i>
</button>
</div>
</div>
<!-- Widgets Grid -->
<div class="grid md:grid-cols-3 gap-6 mb-8">
<!-- Widget 1: Fines -->
<div class="bg-surface rounded-2xl p-6 shadow-sm border border-gray-100 hover-card tilt animate-slide-up" style="animation-delay: 0.1s;">
<div class="flex justify-between items-start mb-4">
<div class="w-12 h-12 rounded-xl bg-red-50 text-red-500 flex items-center justify-center text-xl">
<i class="fa-solid fa-file-invoice-dollar"></i>
</div>
<span class="px-2 py-1 bg-red-100 text-red-600 text-xs font-bold rounded-md">Pending</span>
</div>
<h3 class="text-gray-500 text-sm font-medium">My Total Fines</h3>
<div class="text-3xl font-bold text-primary mt-1" id="dash-fines">₱0.00</div>
<button class="text-xs text-primary font-medium mt-4 hover:underline flex items-center gap-1" onclick="navigate('fines')">View Details <i class="fa-solid fa-arrow-right"></i></button>
</div>
<!-- Widget 2: Events -->
<div class="bg-surface rounded-2xl p-6 shadow-sm border border-gray-100 hover-card tilt animate-slide-up" style="animation-delay: 0.2s;">
<div class="flex justify-between items-start mb-4">
<div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center text-xl">
<i class="fa-solid fa-calendar-check"></i>
</div>
<span class="px-2 py-1 bg-blue-100 text-blue-600 text-xs font-bold rounded-md" id="dash-event-badge">Upcoming</span>
</div>
<h3 class="text-gray-500 text-sm font-medium">Upcoming Events</h3>
<div class="text-xl font-bold text-primary mt-1 truncate" id="dash-event">No upcoming events</div><div class="text-xs text-textMuted" id="dash-event-date"></div>
<button class="text-xs text-primary font-medium mt-4 hover:underline flex items-center gap-1" onclick="navigate('events')">View Calendar <i class="fa-solid fa-arrow-right"></i></button>
</div>
<!-- Widget 3: Help Desk -->
<div class="bg-surface rounded-2xl p-6 shadow-sm border border-gray-100 hover-card tilt animate-slide-up" style="animation-delay: 0.3s;">
<div class="flex justify-between items-start mb-4">
<div class="w-12 h-12 rounded-xl bg-green-50 text-green-500 flex items-center justify-center text-xl">
<i class="fa-solid fa-ticket-alt"></i>
</div>
<span class="px-2 py-1 bg-green-100 text-green-600 text-xs font-bold rounded-md">Active</span>
</div>
<h3 class="text-gray-500 text-sm font-medium">Open Tickets</h3>
<div class="text-3xl font-bold text-primary mt-1" id="dash-tickets">0</div>
<button class="text-xs text-primary font-medium mt-4 hover:underline flex items-center gap-1" onclick="navigate('helpdesk')">Go to Help Desk <i class="fa-solid fa-arrow-right"></i></button>
</div>
</div>
<!-- Role Specific Banner (VP Simulation) -->
<div class="bg-gradient-to-r from-primary to-primaryLight rounded-2xl p-6 text-white shadow-lg mb-8 animate-slide-up flex flex-col md:flex-row justify-between items-center" id="admin-banner" style="animation-delay: 0.4s;">
<div class="flex items-center gap-4 mb-4 md:mb-0">
<div class="w-12 h-12 rounded-full bg-secondary flex items-center justify-center text-primary text-xl">
<i class="fa-solid fa-shield-halved"></i>
</div>
<div>
<h3 class="font-bold text-lg">Admin Access Active</h3>
<p class="text-sm text-blue-200">You have Vice President privileges. You can manage Org structure and verify CBL updates.</p>
</div>
</div>
<button class="bg-secondary text-primary px-5 py-2 rounded-lg text-sm font-bold shadow hover:bg-white transition-colors" onclick="navigate('admin-cbl')">Review Pending CBL</button>
</div>
</div>

```

## `app/views/member/events.php`

```php
<div class="view-section w-full p-6 lg:p-10" id="view-events">
<div class="flex flex-wrap justify-between items-center gap-3 mb-5"><div><h1 class="text-2xl font-bold text-primary">Events / GPOA</h1><p class="text-textMuted text-sm">The organization calendar.</p></div><button class="hidden btn-secondary px-5 py-2 rounded-xl text-sm" id="add-event-btn" onclick="openEventForm()"><i class="fa-solid fa-plus"></i> Add Event</button></div>
<div class="flex gap-2 flex-wrap mb-4" id="year-tabs"></div>
<div class="flex flex-wrap gap-2 mb-4" id="cal-filters"></div>
<div class="grid xl:grid-cols-3 gap-6">
<div class="xl:col-span-2 bg-surface rounded-2xl border border-gray-100 shadow-sm p-4">
<div class="flex items-center justify-between mb-3"><button aria-label="Previous month" class="px-3 py-2 rounded-lg border border-gray-200 hover:bg-slate-50" onclick="calMove(-1)"><i class="fa-solid fa-chevron-left"></i></button><div class="text-center"><h2 class="text-lg font-bold text-primary" id="cal-title"></h2><button class="text-xs text-primary font-semibold hover:underline" onclick="calToday()">Today</button></div><button aria-label="Next month" class="px-3 py-2 rounded-lg border border-gray-200 hover:bg-slate-50" onclick="calMove(1)"><i class="fa-solid fa-chevron-right"></i></button></div>
<div class="grid grid-cols-7 text-center text-xs font-semibold text-textMuted mb-1"><div>SUN</div><div>MON</div><div>TUE</div><div>WED</div><div>THU</div><div>FRI</div><div>SAT</div></div>
<div class="grid grid-cols-7" id="cal-grid"></div>
<p class="text-xs text-textMuted mt-3" id="cal-count"></p>
</div>
<div class="bg-surface rounded-2xl border border-gray-100 shadow-sm p-5"><h3 class="font-bold text-primary mb-3" id="day-title">Selected day</h3><div class="space-y-2" id="day-list"></div></div>
</div>
<h2 class="text-xl font-bold text-primary mt-10 mb-4">Year Highlights <span class="text-secondaryHover" id="hl-year"></span></h2>
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5" id="year-hl"></div>
</div>

```

## `app/views/member/fines.php`

```php
<div class="view-section w-full p-6 lg:p-10" id="view-fines">
<div class="mb-8">
<h1 class="text-2xl font-bold text-primary dark:text-white">My Fines</h1>
<p class="text-textMuted text-sm">Track and manage your organizational penalties.</p>
</div>
<div class="bg-surface rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
<div class="p-6 border-b border-gray-100 bg-red-50 flex justify-between items-center">
<div>
<h3 class="text-red-800 font-bold">Unpaid Balance</h3>
<p class="text-sm text-red-600">Please settle your fines before the end of the semester.</p>
</div>
<div class="text-2xl font-bold text-red-600">₱150.00</div>
</div>
<div class="p-0">
<table class="w-full text-left text-sm">
<thead class="bg-gray-50">
<tr>
<th class="p-4 font-medium text-gray-500">Date</th>
<th class="p-4 font-medium text-gray-500">Reason</th>
<th class="p-4 font-medium text-gray-500">Amount</th>
<th class="p-4 font-medium text-gray-500">Status</th>
</tr>
</thead>
<tbody>
<tr class="border-t border-gray-100">
<td class="p-4 text-textDark">Oct 5, 2026</td>
<td class="p-4 font-medium text-primary">Late to Gen Assembly</td>
<td class="p-4 text-textDark">₱50.00</td>
<td class="p-4"><span class="px-2 py-1 bg-red-100 text-red-600 rounded text-xs font-bold">Unpaid</span></td>
</tr>
<tr class="border-t border-gray-100">
<td class="p-4 text-textDark">Sep 20, 2026</td>
<td class="p-4 font-medium text-primary">Absent - IT Week Day 1</td>
<td class="p-4 text-textDark">₱100.00</td>
<td class="p-4"><span class="px-2 py-1 bg-red-100 text-red-600 rounded text-xs font-bold">Unpaid</span></td>
</tr>
<tr class="border-t border-gray-100 bg-gray-50 opacity-60">
<td class="p-4 text-textDark">Aug 15, 2026</td>
<td class="p-4 font-medium text-primary">No ID Lanyard</td>
<td class="p-4 text-textDark">₱20.00</td>
<td class="p-4"><span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">Paid</span></td>
</tr>
</tbody>
</table>
</div>
</div>
</div>

```

## `app/views/member/forms.php`

```php
<div class="view-section w-full p-6 lg:p-10" id="view-forms">
<div class="mb-8">
<h1 class="text-2xl font-bold text-primary dark:text-white">Forms Hub</h1>
<p class="text-textMuted text-sm">Download necessary templates and official documents.</p>
</div>
<div class="grid md:grid-cols-2 gap-6">
<div class="bg-surface p-6 rounded-2xl shadow-sm border border-gray-100 hover-card animate-slide-up">
<div class="flex items-start gap-4">
<div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-2xl flex-shrink-0">
<i class="fa-solid fa-file-word"></i>
</div>
<div>
<h3 class="font-bold text-primary text-lg">Excuse Letter Template</h3>
<p class="text-sm text-textMuted mb-4">Official fillable template for missing organizational events or assemblies.</p>
<button class="text-sm btn-primary px-4 py-2 rounded shadow-sm"><i class="fa-solid fa-download mr-1"></i> Download Word</button>
</div>
</div>
</div>
<div class="bg-surface p-6 rounded-2xl shadow-sm border border-gray-100 hover-card animate-slide-up" style="animation-delay: 0.1s;">
<div class="flex items-start gap-4">
<div class="w-12 h-12 bg-red-50 text-red-600 rounded-lg flex items-center justify-center text-2xl flex-shrink-0">
<i class="fa-solid fa-file-pdf"></i>
</div>
<div>
<h3 class="font-bold text-primary text-lg">Constitution and By-Laws</h3>
<p class="text-sm text-textMuted mb-4">The current official and verified CBL of the iBITS organization. (View Only)</p>
<button class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded transition-colors"><i class="fa-solid fa-eye mr-1"></i> View PDF</button>
</div>
</div>
</div>
</div>
</div>

```

## `app/views/member/helpdesk.php`

```php
<div class="view-section w-full p-6 lg:p-10" id="view-helpdesk">
<div class="flex flex-wrap justify-between items-center gap-3 mb-6"><div><h1 class="text-2xl font-bold text-primary">STRAW Support / Help Desk</h1><p class="text-textMuted text-sm">Concerns, inquiries and assistance requests.</p></div><div class="flex gap-3"><button class="btn-primary px-5 py-2 rounded-xl text-sm" onclick="toggleChat(true)"><i class="fa-solid fa-robot"></i> AI Assistant</button><button class="btn-secondary px-5 py-2 rounded-xl text-sm" onclick="openTicketForm()"><i class="fa-solid fa-plus"></i> Create New Ticket</button></div></div>
<div class="grid grid-cols-3 gap-3 mb-5" id="tk-counts"></div>
<p class="text-xs text-textMuted mb-3" id="tk-scope"></p>
<div class="space-y-3" id="tk-list"></div>
</div>

```

## `app/views/member/settings.php`

```php
<div class="view-section w-full p-6 lg:p-10" id="view-settings">
<h1 class="text-2xl font-bold text-primary mb-4">Settings &amp; Profile</h1>
<div class="bg-surface p-6 rounded-2xl border border-gray-100 max-w-2xl">
<div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
<img alt="Profile" class="w-20 h-20 rounded-full border-4 border-secondary" src="https://placehold.co/100x100/fad517/06215c?text=VP"/>
<div>
<h2 class="text-xl font-bold text-primary">Johahnie Ynah Marise Capistrano Gilbuena</h2>
<p class="text-textMuted text-sm">2024-0001-LQ-0 • BSIT - 3rd Yr</p>
</div>
</div>
<div class="mb-6">
<h3 class="font-semibold text-primary mb-3">Appearance</h3>
<button class="px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg text-sm font-medium flex items-center gap-2" onclick="toggleDarkMode()">
<i class="fa-solid fa-moon"></i> Toggle Dark Mode
                            </button>
</div>
<button class="btn-primary px-6 py-2 rounded-lg text-sm font-medium shadow-sm">Save Changes</button>
</div>
</div>

```

## `app/views/public/about.php`

```php
<div class="view-section w-full min-h-full pt-28 pb-20" id="view-public-about">
<div class="container mx-auto px-6 max-w-5xl">
<h2 class="text-3xl md:text-4xl font-bold text-primary mb-12 text-center animate-slide-up">About iBITS</h2>
<div class="grid md:grid-cols-2 gap-12">
<div class="bg-surface p-8 rounded-3xl shadow-sm border border-gray-100 hover-card animate-slide-up" style="animation-delay: 0.1s;">
<div class="w-14 h-14 bg-primary bg-opacity-10 text-primary rounded-xl flex items-center justify-center text-2xl mb-6">
<i class="fa-solid fa-bullseye"></i>
</div>
<h3 class="text-2xl font-bold text-primary mb-4">Our Mission</h3>
<p class="text-textMuted leading-relaxed">
                                    To foster a collaborative environment where IT students can develop technical excellence, leadership skills, and ethical professionalism through innovative projects and community engagement.
                                </p>
</div>
<div class="bg-surface p-8 rounded-3xl shadow-sm border border-gray-100 hover-card animate-slide-up" style="animation-delay: 0.2s;">
<div class="w-14 h-14 bg-secondary bg-opacity-20 text-secondaryHover rounded-xl flex items-center justify-center text-2xl mb-6">
<i class="fa-solid fa-eye"></i>
</div>
<h3 class="text-2xl font-bold text-primary mb-4">Our Vision</h3>
<p class="text-textMuted leading-relaxed">
                                    To be the leading student organization that produces globally competitive technology professionals who drive digital transformation and social progress.
                                </p>
</div>
</div>
</div>
</div>

```

## `app/views/public/events.php`

```php
<div class="view-section w-full min-h-full pt-28 pb-20" id="view-public-events"><div class="container mx-auto px-6 max-w-6xl"><h2 class="text-3xl font-bold text-primary mb-2 text-center">Events</h2><p class="text-textMuted text-center mb-10">Highlights of past events and upcoming public events. Hover a card for details.</p><div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6" id="pub-events"></div></div></div>

```

## `app/views/public/faqs.php`

```php
<div class="view-section w-full min-h-full pt-28 pb-20" id="view-public-faqs"><div class="container mx-auto px-6 max-w-3xl"><h2 class="text-3xl font-bold text-primary mb-8 text-center">Frequently Asked Questions</h2>
<details class="card3d p-5 mb-3"><summary class="font-semibold text-primary cursor-pointer">Who can join iBITS?</summary><p class="text-textMuted text-sm mt-2">Any registered IT or DIT student. Create an account using your student number.</p></details>
<details class="card3d p-5 mb-3"><summary class="font-semibold text-primary cursor-pointer">How do I check my fines?</summary><p class="text-textMuted text-sm mt-2">Log in and open My Fines. You can only see your own.</p></details>
<details class="card3d p-5 mb-3"><summary class="font-semibold text-primary cursor-pointer">Where is the excuse letter?</summary><p class="text-textMuted text-sm mt-2">Open Forms Hub after logging in. Fill the template, then save or print.</p></details></div></div>

```

## `app/views/public/home.php`

```php
<div class="view-section active w-full min-h-full" id="view-public-home">
<!-- Hero Section -->
<div class="zd relative overflow-hidden text-center pt-40 pb-24" style="background:#020a1c">
<canvas class="rain absolute inset-0 w-full h-full"></canvas>
<div class="relative z-10 px-6 max-w-3xl mx-auto">
<h1 class="text-5xl md:text-7xl font-light text-white mb-5 animate-slide-up">We <b class="font-bold">CODE</b> as <b class="font-bold">ONE.</b></h1>
<p class="text-sm md:text-base text-slate-300 mb-9 max-w-xl mx-auto animate-slide-up">At iBITS, we unite brilliant minds and passionate builders under a shared passion for technology to forge innovation together as a single, unified force.</p>
<button class="olive-btn tilt animate-slide-up" onclick="navigate('register')">Code as One</button>
</div>
</div>
<!-- Statistics / Counters -->
<div class="zd py-14 border-t border-white/10" style="background:#030c24"><div class="container mx-auto px-6"><h2 class="text-2xl font-bold text-white text-center mb-8">iBITS Population</h2><div class="space-y-6" id="pop-stats"></div></div></div>
<div class="wp wp-sec relative overflow-hidden py-16">
<canvas class="rain absolute inset-0 w-full h-full"></canvas>
<div class="relative z-10 container mx-auto px-6 max-w-5xl">
<h2 class="text-4xl font-bold text-center mb-10 text-primary">Who We Are</h2>
<div class="grid md:grid-cols-2 gap-10 items-center">
<div class="tilt navy-card p-8"><h3 class="text-2xl font-bold mb-1">We Are More Than an Academic Organization</h3><p class="text-secondary font-semibold mb-4">A Unified Force for Tech Innovators.</p><p class="text-sm leading-relaxed text-blue-100">At the <b>Institute of Brilliant Information Technology Students</b>, we unite brilliant minds and passionate builders under a shared love for technology to forge innovation together as a single, unified force.</p><p class="text-sm mt-3 text-blue-100">We create spaces where students don't just study technology, <b>we code as one.</b></p></div>
<div class="coin-wrap"><div class="coin"><img alt="iBITS logo" class="logo-img"/></div></div>
</div>
<div class="grid md:grid-cols-2 gap-10 mt-20">
<div><h3 class="text-4xl font-bold text-primary mb-4">Mission</h3><div class="tilt navy-card p-8 min-h-[190px]"><p class="text-sm leading-relaxed text-blue-100">To foster a collaborative environment where IT students can develop technical excellence, leadership skills, and ethical professionalism through innovative projects and community engagement.</p></div></div>
<div class="md:mt-16"><h3 class="text-4xl font-bold text-primary mb-4 text-center">Vision</h3><div class="tilt yellow-card p-8 min-h-[190px]"><p class="text-sm leading-relaxed">To be the leading student organization that produces globally competitive technology professionals who drive digital transformation and social progress.</p></div></div>
</div>
<h2 class="text-4xl font-bold text-center mt-20 mb-8 text-primary">Events</h2>
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5" id="home-events"></div>
</div>
</div>
</div>

```

## `app/views/public/org.php`

```php
<div class="view-section w-full min-h-full pt-28 pb-20" id="view-public-org">
<div class="container mx-auto px-6 max-w-6xl">
<div class="text-center mb-16 animate-fade-in">
<h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">Organizational Structure</h2>
<p class="text-textMuted">Meet the dedicated leaders of iBITS</p>
</div>
<!-- Level 1: Adviser -->
<div class="flex justify-center mb-12 animate-slide-up" style="animation-delay: 0.1s;">
<div class="text-center hover-card">
<div class="relative w-24 h-24 mx-auto mb-4 rounded-full overflow-hidden border-4 border-primary shadow-lg">
<img alt="Adviser" class="w-full h-full object-cover" src="https://placehold.co/100x100/06215c/fff?text=AD"/>
</div>
<h4 class="font-bold text-primary">Prof. Alan Turing</h4>
<p class="text-xs text-textMuted uppercase tracking-wider font-semibold">Organization Adviser</p>
</div>
</div>
<div class="w-full flex justify-center mb-12"><div class="h-8 w-px bg-gray-300"></div></div>
<!-- Level 2: Execs -->
<div class="grid grid-cols-2 gap-8 max-w-2xl mx-auto mb-12 animate-slide-up" style="animation-delay: 0.2s;">
<div class="text-center hover-card">
<div class="relative w-20 h-20 mx-auto mb-4 rounded-full overflow-hidden border-4 border-secondary shadow-md">
<img alt="President" class="w-full h-full object-cover" src="https://placehold.co/100x100/fad517/06215c?text=PR"/>
</div>
<h4 class="font-bold text-primary text-sm">Ada Lovelace</h4>
<p class="text-xs text-secondaryHover uppercase tracking-wider font-bold">President</p>
</div>
<div class="text-center hover-card">
<div class="relative w-20 h-20 mx-auto mb-4 rounded-full overflow-hidden border-4 border-secondary shadow-md">
<img alt="VP" class="w-full h-full object-cover" src="https://placehold.co/100x100/fad517/06215c?text=VP"/>
</div>
<h4 class="font-bold text-primary text-sm">Johahnie Gilbuena</h4>
<p class="text-xs text-secondaryHover uppercase tracking-wider font-bold">Vice President</p>
</div>
</div>
</div>
</div>

```

## `config/database.php`

```php
<?php
declare(strict_types=1);

$host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: '3306';
$db   = $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE') ?: 'ibits_portal';
$user = $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME') ?: 'root';
$pass = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: '';

$dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    error_log('[iBITS] Database connection failed: ' . $e->getMessage());
    $pdo = null;
}

```

## `database/schema.sql`

```sql
CREATE DATABASE IF NOT EXISTS ibits_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ibits_portal;

CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  student_id VARCHAR(50) NOT NULL UNIQUE,
  program VARCHAR(100) NOT NULL,
  role VARCHAR(50) NOT NULL DEFAULT 'Member',
  password_hash VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE fines (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  reason VARCHAR(255) NOT NULL,
  paid TINYINT(1) NOT NULL DEFAULT 0,
  fine_date DATE NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE events (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(180) NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NULL,
  start_time TIME NULL,
  end_time TIME NULL,
  location VARCHAR(180) NULL,
  category VARCHAR(80) NOT NULL,
  description TEXT NULL,
  is_public TINYINT(1) NOT NULL DEFAULT 1,
  is_highlight TINYINT(1) NOT NULL DEFAULT 0,
  image_path VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tickets (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  owner_id BIGINT UNSIGNED NOT NULL,
  subject VARCHAR(180) NOT NULL,
  category VARCHAR(100) NOT NULL,
  status ENUM('Open','In Progress','Resolved') NOT NULL DEFAULT 'Open',
  description TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE cbl_amendments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  amendment_no VARCHAR(50) NOT NULL,
  title VARCHAR(180) NOT NULL,
  description TEXT,
  status VARCHAR(50) NOT NULL DEFAULT 'Pending',
  document_path VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

```

## `database/seeds.sql`

```sql
USE ibits_portal;

INSERT INTO users (full_name, student_id, program, role, password_hash) VALUES
('Johahnie Ynah Marise Capistrano Gilbuena','2024-0001-LQ-0','BSIT - 3rd Yr','Vice President',NULL),
('Ada Lovelace','2023-0012-LQ-0','BSIT - 4th Yr','President',NULL);

INSERT INTO fines (user_id, amount, reason, paid, fine_date) VALUES
(1, 50.00, 'Late to Gen Assembly', 0, '2026-10-05'),
(1, 100.00, 'Absent - IT Week Day 1', 0, '2026-09-20'),
(1, 20.00, 'No ID Lanyard', 1, '2026-08-15');

INSERT INTO events (title,start_date,end_date,start_time,end_time,location,category,description,is_public,is_highlight) VALUES
('Orientation 2024','2024-08-14','2024-08-14','08:00','12:00','PUP Lopez Gym','Academic','Welcome program for new members.',1,1),
('IT Week 2024','2024-10-14','2024-10-18','08:00','17:00','PUP Lopez Campus','Technology','Week of contests and talks.',1,1),
('IT Week 2025','2025-10-13','2025-10-17','08:00','17:00','PUP Lopez Campus','Technology','Seminars, tournaments and exhibits.',1,1),
('Foundation Day 2025','2025-11-20','2025-11-20','09:00','16:00','Covered Court','Social','Celebrating the org founding.',1,1),
('Orientation 2026','2026-08-12','2026-08-12','08:00','12:00','PUP Lopez Gym','Academic','Welcome program for new members.',1,1),
('Intramurals Code Cup','2026-10-05','2026-10-10','09:00','17:00','Computer Lab','Competition','Coding contest running all week.',1,0),
('Tech Talk Thursday','2026-10-08','2026-10-08','13:00','15:00','Room 204','Technology','Guest talk on web careers.',1,0),
('IT Week 2026','2026-10-12','2026-10-16','08:00','12:00','PUP Lopez Campus','Technology','Technology week with contests, seminars and exhibits.',1,1),
('General Assembly','2026-11-14','2026-11-14','09:00','12:00','Auditorium','Org Meeting','Members only: updates and elections.',0,1),
('Hackathon: Build as One','2026-12-05','2026-12-06','08:00','08:00','Computer Lab','Competition','24 hours of building together.',1,1),
('Orientation 2027','2027-08-11','2027-08-11','08:00','12:00','PUP Lopez Gym','Academic','Welcome program for new members.',1,1);

INSERT INTO tickets (owner_id,subject,category,status,description,created_at) VALUES
(1,'Cannot open the portal','Internet / Portal Issue','Open','The page stays blank on mobile.','2026-10-06 00:00:00'),
(2,'Wrong name on my profile','Account Concern','In Progress','Please fix my middle name.','2026-10-05 00:00:00'),
(1,'Need the excuse letter template','Form Request','Resolved','Where can I get it?','2026-09-28 00:00:00');

INSERT INTO cbl_amendments (amendment_no,title,description,status) VALUES
('2026-04','Attendance Fines','Proposed change to Section 4.1 regarding unexcused absences during general assemblies.','Pending');

```

## `public/assets/css/app.css`

```css
.nav-link.bg-primary:hover{background:#06215c!important;color:#fff!important}
#app-footer .text-white,#app-footer h2,#app-footer h3,#app-footer a{color:#06215c}
.dark #sidebar{background:#0b1220;color:#f8fafc}.dark #sidebar .text-primary{color:#f8fafc}
.btn-primary{box-shadow:0 4px 0 #031236}.btn-primary:hover{box-shadow:0 6px 0 #031236,0 10px 20px rgba(6,33,92,.2)}.btn-secondary{box-shadow:0 4px 0 #bda000}.btn-secondary:hover{box-shadow:0 6px 0 #bda000,0 10px 20px rgba(250,213,23,.35)}
.btn-primary:active,.btn-secondary:active{transform:translateY(3px)!important;box-shadow:0 1px 0 #031236!important}
.tilt{transition:transform .2s ease,box-shadow .2s;transform-style:preserve-3d;will-change:transform}
.card3d{background:#fff;border:1px solid #e5e7eb;border-radius:1.5rem;box-shadow:0 1px 0 #fff inset,0 24px 50px -18px rgba(6,33,92,.35),0 3px 0 #e2e8f0}
.dark .card3d{background:#1e293b;border-color:#334155}
.coin-wrap{perspective:900px;width:min(250px,58vw);margin:0 auto 26px;position:relative}
.coin-wrap::after{content:"";position:absolute;left:12%;right:12%;bottom:-16px;height:18px;background:radial-gradient(ellipse,rgba(6,33,92,.28),transparent 70%)}
.coin{aspect-ratio:1;transform-style:preserve-3d;animation:coin 9s ease-in-out infinite}
.coin img{width:100%;height:100%;border-radius:50%;box-shadow:0 22px 40px -8px rgba(6,33,92,.45),0 0 0 3px #fff}
@keyframes coin{0%,100%{transform:rotateY(-20deg) rotateX(6deg)}50%{transform:rotateY(20deg) rotateX(-4deg)}}
.gt{background:linear-gradient(90deg,#06215c 0,#06215c 45%,#d4af00 100%);-webkit-background-clip:text;background-clip:text;color:transparent}
@keyframes up{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:none}}
.login-in{opacity:0;animation:up .5s ease-out forwards;animation-delay:var(--d,0s)}
.msg-in{animation:up .3s ease-out}
.ico{width:1em;height:1em;display:inline-block;vertical-align:-.125em;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.cal-cell{min-height:92px;border:1px solid #eef2f7;padding:6px;cursor:pointer;transition:background .15s}.cal-cell:hover{background:#f8fafc}
.cal-cell.today{background:#fffbe0;box-shadow:inset 0 0 0 2px #fad517}.cal-cell.sel{box-shadow:inset 0 0 0 2px #06215c}.cal-cell.off{opacity:.4}
.chip{display:block;width:100%;text-align:left;font-size:11px;padding:1px 6px;margin-top:2px;border-radius:6px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.chip.Past{background:#f1f5f9;color:#64748b}.chip.Current,.chip.Ongoing{background:#fef3b0;color:#06215c}.chip.Future{background:#06215c;color:#fff}
.flip{perspective:900px;height:250px}.flip-in{position:relative;width:100%;height:100%;transform-style:preserve-3d;transition:transform .6s}.flip:hover .flip-in,.flip:focus-within .flip-in{transform:rotateY(180deg)}
.flip-f,.flip-b{position:absolute;inset:0;backface-visibility:hidden;border-radius:1rem;overflow:hidden}.flip-b{transform:rotateY(180deg);background:#06215c;color:#fff;padding:20px}
.modal-in{animation:up .25s ease-out}
@media(max-width:640px){.cal-cell{min-height:56px;padding:3px}}
@media(prefers-reduced-motion:reduce){*{animation-duration:.01ms!important;transition-duration:.01ms!important}.coin{animation:none}.login-in{opacity:1}}

html.dark body{background:#020a1c;color:#f1f5ff}.dark #content-viewport,.dark .bg-background{background:#020a1c}.zd{color:#f1f5ff}
:is(.dark,.zd) .text-primary{color:#fff}:is(.dark,.zd) .text-textDark{color:#f1f5ff}:is(.dark,.zd) .text-textMuted,.dark .text-gray-500,.dark .text-gray-400,.dark .text-gray-600{color:#a9b8d6}
:is(.dark,.zd) .bg-surface{background:#081a45}.dark .bg-white{background:rgba(255,255,255,.07)}.dark .bg-soft,.dark .bg-slate-50,.dark .bg-gray-50{background:rgba(255,255,255,.05)}.dark .bg-slate-100{background:rgba(255,255,255,.1)}
.dark .hover\:bg-slate-50:hover,.dark .hover\:bg-slate-100:hover,.dark .hover\:bg-gray-50:hover{background:rgba(255,255,255,.08)}
.dark .border-gray-100,.dark .border-gray-200,.dark .border-gray-300,.dark .border-gray-50{border-color:rgba(255,255,255,.12)}
.dark #sidebar{background:#010816;color:#f1f5ff;border-color:rgba(255,255,255,.1)}.dark #app-footer{background:#010611;border-color:rgba(255,255,255,.08)}.dark #app-footer :is(.text-white,h2,h3,a){color:#fff}
.dark ::-webkit-scrollbar-track{background:#020a1c}.dark ::-webkit-scrollbar-thumb{background:#1c3366}
:is(.dark,.zd) .card3d{background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.12);box-shadow:0 24px 50px -18px rgba(0,0,0,.7),0 3px 0 rgba(250,213,23,.25)}
:is(.dark,.zd) input:not([type=checkbox]):not([type=file]),:is(.dark,.zd) select,:is(.dark,.zd) textarea{background:rgba(255,255,255,.1);color:#fff;border-color:rgba(255,255,255,.18)}
:is(.dark,.zd) ::placeholder{color:#aab6cf}:is(.dark,.zd) select option{background:#0a1a3d;color:#fff}
.wp{background:#fff;color:#1f2937}:is(.dark,.zd) .wp{background:#fff;color:#1f2937}:is(.dark,.zd) .wp .text-primary{color:#06215c}:is(.dark,.zd) .wp .text-textMuted{color:#6b7280}:is(.dark,.zd) .wp .text-textDark{color:#1f2937}
:is(.dark,.zd) .wp :is(input:not([type=checkbox]):not([type=file]),select,textarea){background:#fff;color:#1f2937;border-color:#e5e7eb}:is(.dark,.zd) .wp ::placeholder{color:#9ca3af}:is(.dark,.zd) .wp select option{background:#fff;color:#1f2937}
.dark .wp .border-gray-100,.dark .wp .border-gray-200{border-color:#e5e7eb}.dark .wp .bg-slate-50{background:#f8fafc}.dark .wp .bg-white{background:#fff}
:is(.dark,.zd) .bg-secondary.text-primary,.dark .bg-secondary .text-primary{color:#06215c}
.dark #year-tabs .bg-primary{background:#fad517;color:#06215c;border-color:#fad517}
.dark .chip.Past{background:rgba(255,255,255,.1);color:#cbd5e1}.dark .chip.Future{background:#0a328c;color:#fff}.dark .cal-cell{border-color:rgba(255,255,255,.08)}.dark .cal-cell:hover{background:rgba(255,255,255,.05)}.dark .cal-cell.today{background:rgba(250,213,23,.16)}.dark .cal-cell.sel{box-shadow:inset 0 0 0 2px #fff}
.glass-pill{background:rgba(255,255,255,.13);backdrop-filter:blur(14px);border:1px solid rgba(255,255,255,.08);box-shadow:0 10px 30px rgba(0,0,0,.4)}
.dd:hover .dd-m,.dd:focus-within .dd-m{display:block}.dd-m{display:none;position:absolute;top:100%;left:-8px;margin-top:12px;min-width:190px;background:#1a2132;border-radius:14px;padding:8px;box-shadow:0 14px 30px rgba(0,0,0,.5)}.dd-m a{display:block;padding:8px 12px;border-radius:8px;color:#fff;font-size:14px}.dd-m a:hover{background:rgba(255,255,255,.1)}
.olive{background:rgba(250,213,23,.3);border:1px solid rgba(250,213,23,.25);border-radius:1.5rem;backdrop-filter:blur(6px);box-shadow:0 24px 50px -18px rgba(0,0,0,.7),0 4px 0 rgba(250,213,23,.35)}
.olive-btn{background:rgba(250,213,23,.3);border:1px solid rgba(250,213,23,.6);color:#fff;padding:10px 28px;border-radius:12px;font-weight:500;box-shadow:0 4px 0 rgba(0,0,0,.4)}
.glass-btn{background:#3b3b3b;border:1px solid #2c2c2c;border-radius:1rem;box-shadow:0 10px 24px rgba(0,0,0,.45),0 3px 0 #222}.glass-btn:hover{border-color:#fad517}
#view-login .glass-btn span.w-12{background:rgba(255,255,255,.1)!important;color:#fad517!important}
#view-login :is(label,.font-medium){color:#fff}#view-login .olive h2{color:#fff}#view-login .olive p,#view-login .olive a{color:#e8edf8}
.gt{background:linear-gradient(90deg,#c9a800,#fff 50%,#fad517);-webkit-background-clip:text;background-clip:text;color:transparent}
.navy-card{background:#0a1f5c;color:#fff;border-radius:1.5rem;box-shadow:10px 12px 0 rgba(6,33,92,.2),0 24px 40px -16px rgba(6,33,92,.55)}
.yellow-card{background:#fad517;color:#06215c;border-radius:1.5rem;box-shadow:10px 12px 0 rgba(160,130,0,.28),0 24px 40px -16px rgba(120,100,0,.5)}
.zd .stat-num{color:#fad517}.flip-f{background:#fad517!important;border:0!important}.flip-f :is(b,.text-primary){color:#06215c!important}.wp-sec .flip-f{box-shadow:6px 8px 0 rgba(160,130,0,.25)}
.coin img{box-shadow:0 22px 40px -8px rgba(0,0,0,.55),0 0 0 3px #fff}

```

## `public/assets/images/ibits-logo.png`

Binary asset: `94,606` bytes.

## `public/assets/js/app.js`

```js
// --- State Management ---
        const state = {
            isLoggedIn: true, // Start logged in to show RBAC
            currentUser: {
                name: 'Johahnie Gilbuena',
                fullName: 'Johahnie Ynah Marise Capistrano Gilbuena',
                id: '2024-0001-LQ-0',
                role: 'Vice President',
                program: 'BSIT - 3rd Yr',
                permissions: ['admin_org', 'admin_events', 'admin_forms', 'admin_cbl']
            },
            currentView: 'dashboard'
        };

        // --- DOM Elements ---
        const sidebar = document.getElementById('sidebar');
        const publicNav = document.getElementById('public-nav');
        const authMobileHeader = document.getElementById('auth-mobile-header');
        const footer = document.getElementById('app-footer');
        const adminMenu = document.getElementById('admin-menu');
        const views = document.querySelectorAll('.view-section');
        const navLinks = document.querySelectorAll('.nav-link');

        // --- Core Functions ---
        function initApp() {
            updateUIState();
            navigate(state.isLoggedIn ? 'dashboard' : 'public-home');
        }

        function updateUIState() {
            if (state.isLoggedIn) {
                // Show Auth Shell
                sidebar.classList.remove('hidden');
                sidebar.classList.add('flex');
                publicNav.classList.add('hidden');
                authMobileHeader.classList.remove('hidden');
                footer.classList.add('hidden');

                // RBAC Logic: Show/Hide Admin menu
                if (state.currentUser.role === 'Vice President' || state.currentUser.role === 'President') {
                    adminMenu.style.display = 'block';
                } else {
                    adminMenu.style.display = 'none';
                }
            } else {
                // Show Public Shell
                sidebar.classList.add('hidden');
                sidebar.classList.remove('flex');
                publicNav.classList.remove('hidden');
                authMobileHeader.classList.add('hidden');
                footer.classList.remove('hidden');
            }
        }

        function navigate(viewId) {
            // Hide all views
            views.forEach(v => {
                v.classList.remove('active');
                // Optional: reset animations by forcing reflow
                const animates = v.querySelectorAll('.animate-slide-up, .animate-fade-in');
                animates.forEach(el => {
                    el.style.animation = 'none';
                    el.offsetHeight; /* trigger reflow */
                    el.style.animation = null; 
                });
            });

            // Show target view
            const target = document.getElementById(`view-${viewId}`);
            if (target) {
                target.classList.add('active');
                state.currentView = viewId;
                
                // Scroll to top
                document.getElementById('content-viewport').scrollTop = 0;
            }

            // Update active state on sidebar links
            navLinks.forEach(link => {
                link.classList.remove('bg-primary', 'text-white', 'border-secondary');
                link.classList.add('border-transparent');
                
                // Check if onclick contains the viewId
                if (link.getAttribute('onclick').includes(`'${viewId}'`)) {
                    link.classList.add('bg-primary', 'text-white', 'border-secondary');
                    link.classList.remove('border-transparent');
                }
            });

            // Close mobile sidebar if open
            if (window.innerWidth < 768 && state.isLoggedIn) {
                sidebar.classList.add('hidden');
                sidebar.classList.remove('flex');
            }
        }

        function handleLogin(e) {
            e.preventDefault();
            // Simulate login process with delay for realism
            const btn = e.target.querySelector('button');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Authenticating...';
            
            setTimeout(() => {
                state.isLoggedIn = true;
                updateUIState();
                navigate('dashboard');
                btn.innerHTML = originalText;
            }, 800);
        }

        function logout() {
            state.isLoggedIn = false;
            updateUIState();
            navigate('public-home');
        }

        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
        }

        // --- Custom Modal Logic (Replacing alert/confirm) ---
        const modal = document.getElementById('custom-modal');
        const modalContent = document.getElementById('modal-content');
        let confirmCallback = null;

        function showModal(title, body, isPrimary = true, onConfirm = null) {
            document.getElementById('modal-title').innerText = title;
            document.getElementById('modal-body').innerText = body;
            
            const btn = document.getElementById('modal-confirm-btn');
            if(isPrimary) {
                btn.className = "px-4 py-2 btn-primary rounded-lg text-sm font-medium";
            } else {
                btn.className = "px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700";
            }

            confirmCallback = onConfirm || (() => closeModal());

            modal.classList.remove('opacity-0', 'pointer-events-none');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }

        function closeModal() {
            modal.classList.add('opacity-0', 'pointer-events-none');
            modalContent.classList.add('scale-95');
            modalContent.classList.remove('scale-100');
            confirmCallback = null;
        }

        function confirmModal() {
            if (confirmCallback) confirmCallback();
            closeModal();
            // Show a mock toast/success indicator via console for now
            console.log("Action confirmed.");
        }

        // Initialize application
        window.onload = initApp;

/* ===== iBITS v4 additions (patched onto the existing portal) ===== */
const LOGO='assets/images/ibits-logo.png';
const RM=matchMedia('(prefers-reduced-motion:reduce)').matches;
const $=s=>document.querySelector(s), $$=s=>[...document.querySelectorAll(s)];
const esc=s=>String(s??'').replace(/[&<>"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
/* AI backend: set endpoint to your server route (POST {messages:[{role,content}]} -> {reply}). Never put API keys here. */
const AI_CONFIG={endpoint:''};
/* Data layer: replace loadPortalData() with a real API call when the backend exists. */
const DATA={
 population:{program:{BSIT:850,DIT:390},year:{'1st Year':380,'2nd Year':330,'3rd Year':300,'4th Year':230},section:{'Section A':320,'Section B':310,'Section C':300,'Section D':310},officers:15},
 fines:[{amt:100,paid:false},{amt:50,paid:false}],
 events:[
  {id:1,title:'Orientation 2024',start:'2024-08-14',end:'2024-08-14',st:'08:00',et:'12:00',loc:'PUP Lopez Gym',cat:'Academic',desc:'Welcome program for new members.',pub:true,hl:true},
  {id:2,title:'IT Week 2024',start:'2024-10-14',end:'2024-10-18',st:'08:00',et:'17:00',loc:'PUP Lopez Campus',cat:'Technology',desc:'Week of contests and talks.',pub:true,hl:true},
  {id:3,title:'IT Week 2025',start:'2025-10-13',end:'2025-10-17',st:'08:00',et:'17:00',loc:'PUP Lopez Campus',cat:'Technology',desc:'Seminars, tournaments and exhibits.',pub:true,hl:true},
  {id:4,title:'Foundation Day 2025',start:'2025-11-20',end:'2025-11-20',st:'09:00',et:'16:00',loc:'Covered Court',cat:'Social',desc:'Celebrating the org founding.',pub:true,hl:true},
  {id:5,title:'Orientation 2026',start:'2026-08-12',end:'2026-08-12',st:'08:00',et:'12:00',loc:'PUP Lopez Gym',cat:'Academic',desc:'Welcome program for new members.',pub:true,hl:true},
  {id:6,title:'Intramurals Code Cup',start:'2026-10-05',end:'2026-10-10',st:'09:00',et:'17:00',loc:'Computer Lab',cat:'Competition',desc:'Coding contest running all week.',pub:true,hl:false},
  {id:7,title:'Tech Talk Thursday',start:'2026-10-08',end:'2026-10-08',st:'13:00',et:'15:00',loc:'Room 204',cat:'Technology',desc:'Guest talk on web careers.',pub:true,hl:false},
  {id:8,title:'IT Week 2026',start:'2026-10-12',end:'2026-10-16',st:'08:00',et:'12:00',loc:'PUP Lopez Campus',cat:'Technology',desc:'Technology week with contests, seminars and exhibits.',pub:true,hl:true},
  {id:9,title:'General Assembly',start:'2026-11-14',end:'2026-11-14',st:'09:00',et:'12:00',loc:'Auditorium',cat:'Org Meeting',desc:'Members only: updates and elections.',pub:false,hl:true},
  {id:10,title:'Hackathon: Build as One',start:'2026-12-05',end:'2026-12-06',st:'08:00',et:'08:00',loc:'Computer Lab',cat:'Competition',desc:'24 hours of building together.',pub:true,hl:true},
  {id:11,title:'Orientation 2027',start:'2027-08-11',end:'2027-08-11',st:'08:00',et:'12:00',loc:'PUP Lopez Gym',cat:'Academic',desc:'Welcome program for new members.',pub:true,hl:true}],
 tickets:[
  {id:1,subject:'Cannot open the portal',cat:'Internet / Portal Issue',status:'Open',desc:'The page stays blank on mobile.',owner:'2024-0001-LQ-0',date:'2026-10-06'},
  {id:2,subject:'Wrong name on my profile',cat:'Account Concern',status:'In Progress',desc:'Please fix my middle name.',owner:'2023-0412-LQ-0',date:'2026-10-05'},
  {id:3,subject:'Need the excuse letter template',cat:'Form Request',status:'Resolved',desc:'Where can I get it?',owner:'2024-0001-LQ-0',date:'2026-09-28'}]};
function loadPortalData(){/* TODO: fetch('/api/...') then Object.assign(DATA, json); renderAll(); */}
const PERMS={'President':['admin_org','admin_events','admin_forms','admin_cbl','manage_tickets'],'Vice President':['admin_org','admin_events','admin_forms','admin_cbl','manage_tickets'],'Secretary':['admin_events','admin_forms','manage_tickets'],'Institutional':['admin_events'],'Treasurer':[],'Assistant Treasurer':[],'Auditor':[],'Assistant Auditor':[],'Adviser':['admin_cbl'],'Member':[]};
const has=p=>state.currentUser.permissions.includes(p);

/* ---- icons (Font Awesome cannot load here, so <i class="fa-..."> becomes inline SVG) ---- */
const P_={house:'M3 11l9-8 9 8M5 10v10h5v-6h4v6h5V10',code:'M8 8l-5 4 5 4M16 8l5 4-5 4M14 5l-4 14',file:'M6 3h8l4 4v14H6zM14 3v4h4M9 12h6M9 16h6',calendar:'M3 6h18v15H3zM3 10h18M8 3v4M16 3v4','calendar-check':'M3 6h18v15H3zM3 10h18M8 3v4M16 3v4M9 15l2 2 4-4',headset:'M4 14v-2a8 8 0 0116 0v2M4 14h3v5H4zM17 14h3v5h-3zM17 19c0 1.500-2 2-5 2',sitemap:'M10 3h4v4h-4zM3 15h4v4H3zM17 15h4v4h-4zM12 7v4M12 11H5v4M12 11h7v4',scale:'M12 3v18M5 21h14M5 7h14M5 7l-3 7a3 3 0 006 0zM19 7l-3 7a3 3 0 006 0z',cog:'M12 9a3 3 0 100 6 3 3 0 000-6M12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2',signout:'M9 4H4v16h5M16 8l4 4-4 4M20 12H9',bars:'M3 6h18M3 12h18M3 18h18',bell:'M6 16v-5a6 6 0 0112 0v5l2 2H4zM10 20a2 2 0 004 0',ticket:'M3 7h18v4a2 2 0 000 2v4H3v-4a2 2 0 000-2zM14 7v10','arrow-right':'M5 12h14M13 6l6 6-6 6','arrow-left':'M19 12H5M11 6l-6 6 6 6',shield:'M12 3l8 3v6c0 5-4 8-8 9-4-1-8-4-8-9V6zM9 12l2 2 4-4',lock:'M6 11h12v10H6zM8 11V7a4 4 0 018 0v4',bullseye:'M12 3a9 9 0 100 18 9 9 0 000-18M12 8a4 4 0 100 8 4 4 0 000-8',eye:'M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12zM12 9a3 3 0 100 6 3 3 0 000-6','eye-slash':'M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12zM12 9a3 3 0 100 6 3 3 0 000-6M4 4l16 16',spinner:'M12 3a9 9 0 019 9',user:'M12 4a4 4 0 100 8 4 4 0 000-8M4 21c0-4 4-6 8-6s8 2 8 6',plus:'M12 5v14M5 12h14',xmark:'M6 6l12 12M18 6L6 18',minus:'M5 12h14','paper-plane':'M3 11l18-8-8 18-2-8z','chevron-down':'M6 9l6 6 6-6','chevron-left':'M15 5l-7 7 7 7','chevron-right':'M9 5l7 7-7 7',pin:'M12 21s-7-6-7-11a7 7 0 0114 0c0 5-7 11-7 11zM12 8a2 2 0 100 4 2 2 0 000-4',clock:'M12 3a9 9 0 100 18 9 9 0 000-18M12 7v5l3 2',image:'M3 5h18v14H3zM3 16l5-5 4 4 3-3 6 6',pen:'M4 20l1-5L16 4l4 4L9 19z',trash:'M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13',check:'M5 12l5 5 9-10',robot:'M5 8h14v11H5zM12 8V4M9 13h.01M15 13h.01M3 13v3M21 13v3',dot:'M12 10a2 2 0 100 4 2 2 0 000-4',download:'M12 4v11M7 11l5 5 5-5M4 20h16',moon:'M20 14A8 8 0 1110 4a7 7 0 0010 10z'};
const FA={'file-pdf':'file','file-word':'file','discord':'dot','facebook':'dot','github':'dot','file-invoice-dollar':'file','file-alt':'file','file-lines':'file','calendar-alt':'calendar','calendar-days':'calendar','ticket-alt':'ticket','sign-out-alt':'signout','right-from-bracket':'signout','gear':'cog','scale-balanced':'scale','shield-halved':'shield','sitemap':'sitemap','xmark':'xmark','times':'xmark','user-circle':'user','users':'user','id-card':'user','location-dot':'pin','map-marker-alt':'pin','paper-plane':'paper-plane'};
function iconize(root=document){root.querySelectorAll('i[class*="fa-"]').forEach(i=>{const fa=[...i.classList].find(c=>/^fa-/.test(c)&&!/^fa-(solid|regular|brands)$/.test(c));const n=fa?fa.slice(3):'';const k=FA[n]||n;const d=P_[k]||P_.dot;const s=document.createElementNS('http://www.w3.org/2000/svg','svg');s.setAttribute('viewBox','0 0 24 24');s.setAttribute('class','ico '+[...i.classList].filter(c=>!/^fa/.test(c)).join(' '));s.innerHTML='<path d="'+d+'"/>';i.replaceWith(s)})}
new MutationObserver(m=>{if(m.some(r=>r.addedNodes.length))iconize()}).observe(document.body,{childList:true,subtree:true});
function fixPlaceholders(){$$('img[src*="placehold.co"]').forEach(im=>{const m=im.src.match(/placehold\.co\/(\d+)x(\d+)\/([0-9a-fA-F]+)\/([0-9a-fA-F]+)\?text=([^&]*)/);if(!m)return;im.src='data:image/svg+xml,'+encodeURIComponent(`<svg xmlns="http://www.w3.org/2000/svg" width="${m[1]}" height="${m[2]}"><rect width="100%" height="100%" fill="#${m[3]}"/><text x="50%" y="54%" text-anchor="middle" font-family="sans-serif" font-weight="700" font-size="${m[1]/3}" fill="#${m[4]}">${decodeURIComponent(m[5])}</text></svg>`)})}

/* ---- binary rain (white background, faint navy digits) ---- */
function rain(){$$('canvas.rain').forEach(c=>{if(!c.offsetParent)return;const w=c.width=c.clientWidth,h=c.height=c.clientHeight,f=18,g=c.getContext('2d');if(!c._h||c._w!==w){c._w=w;c._h=Array.from({length:Math.ceil(w/f)},()=>Math.random()*h/f)}
 g.clearRect(0,0,w,h);g.font=f-4+'px monospace';c._h.forEach((y,i)=>{for(let k=0;k<7;k++){const L=!!c.closest('.wp');g.fillStyle=k==0&&i%9==0?'rgba(250,213,23,'+(L?.45:.5)+')':(L?'rgba(6,33,92,':'rgba(190,205,235,')+((L?.12:.3)-k*.04)+')';g.fillText(((i*7+k+Math.floor(y))%3)?1:0,i*f,(y-k)*f)}if(!RM)c._h[i]=y*f>h+120?-Math.random()*20:y+.35})})}
setInterval(rain,90);

/* ---- 3D tilt ---- */
function tilt(){if(RM||matchMedia('(pointer:coarse)').matches)return;$$('.tilt:not([data-t])').forEach(el=>{el.dataset.t=1;el.addEventListener('mousemove',e=>{const r=el.getBoundingClientRect(),x=(e.clientX-r.left)/r.width-.5,y=(e.clientY-r.top)/r.height-.5;el.style.transform=`perspective(800px) rotateX(${-y*8}deg) rotateY(${x*10}deg)`});el.addEventListener('mouseleave',()=>el.style.transform='')})}

/* ---- dates ---- */
const pd=s=>new Date(s+'T00:00:00'),TODAY=(()=>{const d=new Date();d.setHours(0,0,0,0);return d})();
const ymd=d=>d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0');
const MN=['January','February','March','April','May','June','July','August','September','October','November','December'];
const fdate=e=>{const a=pd(e.start),b=pd(e.end||e.start);return a.getTime()==b.getTime()?`${MN[a.getMonth()]} ${a.getDate()}, ${a.getFullYear()}`:`${MN[a.getMonth()]} ${a.getDate()}${a.getMonth()==b.getMonth()?'':' '+MN[b.getMonth()]}–${b.getDate()}, ${b.getFullYear()}`};
const t12=t=>{if(!t)return'';const[h,m]=t.split(':').map(Number);return(h%12||12)+':'+String(m).padStart(2,'0')+' '+(h<12?'AM':'PM')};
function statusOf(e){const s=pd(e.start),f=pd(e.end||e.start);if(f<TODAY)return'Past';if(s>TODAY)return'Future';return f>s?'Ongoing':'Current'}
const matches=(e,f)=>f=='All'||statusOf(e)==f||(f=='Current'&&statusOf(e)=='Ongoing');
function art(e){const h=[...e.title].reduce((a,c)=>a+c.charCodeAt(0),0)%40;const bits=Array.from({length:6},(_,i)=>`<text x="12" y="${28+i*22}" fill="#fad517" fill-opacity=".18" font-family="monospace" font-size="14">${Array.from({length:22},(_,k)=>(k*3+i+h)%3?1:0).join(' ')}</text>`).join('');return 'data:image/svg+xml,'+encodeURIComponent(`<svg xmlns="http://www.w3.org/2000/svg" width="480" height="270"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#06215c"/><stop offset="1" stop-color="#0a328c"/></linearGradient></defs><rect width="480" height="270" fill="url(#g)"/>${bits}<circle cx="410" cy="60" r="34" fill="#fad517"/><text x="24" y="236" fill="#fff" font-family="sans-serif" font-weight="700" font-size="24">${esc(e.title.slice(0,26))}</text></svg>`)}
const img=e=>e.img||art(e);

/* ---- population counters ---- */
let POP_SEEN=false;
function countTo(el,to,ms=1500){const from=+el.dataset.v||0;el.dataset.v=to;if(RM){el.textContent=to.toLocaleString();return}const t0=performance.now();(function f(t){const p=Math.min((t-t0)/ms,1),e=1-Math.pow(1-p,3);el.textContent=Math.round(from+(to-from)*e).toLocaleString();if(p<1)requestAnimationFrame(f)})(t0)}
function renderStats(){const p=DATA.population,tot=Object.values(p.program).reduce((a,b)=>a+b,0);const card=(n,l,big)=>`<div class="tilt card3d ${big?'p-6':'p-4'} text-center"><div class="stat-num ${big?'text-4xl':'text-2xl'} font-bold text-primary" data-to="${n}">0</div><div class="text-xs text-textMuted font-medium uppercase tracking-wide mt-1">${l}</div></div>`;
 const grid=(o,c)=>`<div class="grid grid-cols-2 ${c} gap-4">${Object.entries(o).map(([k,v])=>card(v,k)).join('')}</div>`;
 $('#pop-stats').innerHTML=`<div class="grid grid-cols-2 md:grid-cols-4 gap-5">${card(tot,'Total Members',1)}${Object.entries(p.program).map(([k,v])=>card(v,k+' Students',1)).join('')}${card(p.officers,'Active Officers',1)}</div><p class="text-center text-xs font-semibold text-textMuted uppercase tracking-widest">By year level</p>${grid(p.year,'md:grid-cols-4')}<p class="text-center text-xs font-semibold text-textMuted uppercase tracking-widest">By section</p>${grid(p.section,'md:grid-cols-4')}`;
 const run=()=>$$('#pop-stats .stat-num').forEach(el=>countTo(el,+el.dataset.to));if(POP_SEEN)run();else{if(window._io)window._io.disconnect();const io=window._io=new IntersectionObserver(en=>{if(en.some(x=>x.isIntersecting)){POP_SEEN=true;run();io.disconnect()}},{threshold:.25});io.observe($('#pop-stats'))}tilt()}

/* ---- modals ---- */
const openM=id=>{$('#'+id).classList.remove('hidden');$('#'+id).classList.add('flex')},closeM=id=>{$('#'+id).classList.add('hidden');$('#'+id).classList.remove('flex')};
addEventListener('keydown',e=>{if(e.key=='Escape')['m-event','m-form','m-ticket'].forEach(closeM)});

/* ---- calendar ---- */
const CAL={y:TODAY.getFullYear(),m:TODAY.getMonth(),f:'All',sel:ymd(TODAY)};
function renderCal(){
 const ys=[...new Set([...DATA.events.map(e=>pd(e.start).getFullYear()),CAL.y,TODAY.getFullYear()+1])].sort();
 $('#year-tabs').innerHTML=ys.map(y=>`<button onclick="setYear(${y})" class="px-4 py-1.5 rounded-full text-sm font-semibold border ${y==CAL.y?'bg-primary text-white border-primary shadow':'bg-white text-primary border-gray-200 hover:border-primary'}">${y}</button>`).join('');
 $('#cal-filters').innerHTML=['All','Past','Current','Ongoing','Future'].map(f=>`<button onclick="CAL.f='${f}';renderCal()" class="px-4 py-1.5 rounded-lg text-sm border ${CAL.f==f?'bg-secondary text-primary border-secondary font-semibold':'bg-white text-textMuted border-gray-200'}">${f}</button>`).join('');
 $('#cal-title').textContent=MN[CAL.m]+' '+CAL.y;
 const first=new Date(CAL.y,CAL.m,1),start=new Date(CAL.y,CAL.m,1-first.getDay());let h='',n=0;
 for(let i=0;i<42;i++){const d=new Date(start);d.setDate(start.getDate()+i);const k=ymd(d),evs=DATA.events.filter(e=>matches(e,CAL.f)&&k>=e.start&&k<=(e.end||e.start));if(evs.length&&d.getMonth()==CAL.m)n+=0;
  h+=`<div class="cal-cell ${d.getMonth()!=CAL.m?'off':''} ${k==ymd(TODAY)?'today':''} ${k==CAL.sel?'sel':''}" onclick="selDay('${k}')"><div class="text-xs font-semibold text-primary">${d.getDate()}</div>${evs.slice(0,2).map(e=>`<button class="chip ${statusOf(e)} hidden sm:block" onclick="event.stopPropagation();showEvent(${e.id})">${esc(e.title)}</button>`).join('')}${evs.length>2?`<div class="text-[10px] text-textMuted hidden sm:block">+${evs.length-2} more</div>`:''}${evs.length?`<div class="sm:hidden flex gap-0.5 mt-1">${evs.slice(0,3).map(()=>'<span class="w-1.5 h-1.5 rounded-full bg-primary"></span>').join('')}</div>`:''}</div>`}
 $('#cal-grid').innerHTML=h;
 const inM=DATA.events.filter(e=>matches(e,CAL.f)&&pd(e.start).getFullYear()*12+pd(e.start).getMonth()<=CAL.y*12+CAL.m&&pd(e.end||e.start).getFullYear()*12+pd(e.end||e.start).getMonth()>=CAL.y*12+CAL.m).length;
 $('#cal-count').textContent=`${inM} ${CAL.f=='All'?'':CAL.f.toLowerCase()+' '}event${inM==1?'':'s'} in ${MN[CAL.m]} ${CAL.y}`;
 dayList();
 const hl=DATA.events.filter(e=>pd(e.start).getFullYear()==CAL.y&&(e.hl||e.img));$('#hl-year').textContent=CAL.y;
 $('#year-hl').innerHTML=hl.length?hl.map(e=>`<button onclick="showEvent(${e.id})" class="tilt card3d overflow-hidden text-left"><img src="${img(e)}" alt="" class="w-full h-36 object-cover"><div class="p-4"><b class="text-primary text-sm block">${esc(e.title)}</b><span class="text-xs text-textMuted">${fdate(e)}</span></div></button>`).join(''):'<p class="text-textMuted text-sm">No highlights for this year yet.</p>';tilt()}
function dayList(){const d=pd(CAL.sel),L=DATA.events.filter(e=>matches(e,CAL.f)&&CAL.sel>=e.start&&CAL.sel<=(e.end||e.start));$('#day-title').textContent=`${MN[d.getMonth()]} ${d.getDate()}, ${d.getFullYear()}`;
 $('#day-list').innerHTML=L.length?L.map(e=>`<button onclick="showEvent(${e.id})" class="w-full text-left p-3 rounded-xl border border-gray-100 hover:border-primary flex gap-3 items-center"><img src="${img(e)}" alt="" class="w-14 h-14 rounded-lg object-cover"><span><b class="text-sm text-primary block">${esc(e.title)}</b><span class="text-xs text-textMuted">${t12(e.st)} – ${t12(e.et)} · ${statusOf(e)}</span></span></button>`).join(''):'<p class="text-sm text-textMuted">No events on this day.</p>'}
const selDay=k=>{CAL.sel=k;renderCal()},setYear=y=>{CAL.y=y;renderCal();renderDash()},calMove=n=>{CAL.m+=n;if(CAL.m<0){CAL.m=11;CAL.y--}if(CAL.m>11){CAL.m=0;CAL.y++}renderCal()},calToday=()=>{CAL.y=TODAY.getFullYear();CAL.m=TODAY.getMonth();CAL.sel=ymd(TODAY);renderCal()};
function showEvent(id){const e=DATA.events.find(x=>x.id==id);if(!e)return;const adm=has('admin_events');
 $('#m-event-c').innerHTML=`<img src="${img(e)}" alt="" class="w-full h-52 object-cover rounded-t-2xl"><div class="p-6"><span class="text-xs font-bold px-2 py-1 rounded-md bg-secondary/30 text-primary">${statusOf(e)} · ${esc(e.cat)}</span><h3 class="text-2xl font-bold text-primary mt-2 mb-3">${esc(e.title)}</h3><dl class="text-sm space-y-2"><div><dt class="text-textMuted text-xs">Date</dt><dd class="font-medium">${fdate(e)}</dd></div><div><dt class="text-textMuted text-xs">Time</dt><dd class="font-medium">${t12(e.st)} – ${t12(e.et)}</dd></div><div><dt class="text-textMuted text-xs">Location</dt><dd class="font-medium">${esc(e.loc)}</dd></div><div><dt class="text-textMuted text-xs">Description</dt><dd>${esc(e.desc)}</dd></div></dl><div class="flex justify-end gap-2 mt-6">${adm?`<button onclick="openEventForm(${e.id})" class="px-4 py-2 border border-gray-200 rounded-lg text-sm">Edit</button><button onclick="delEvent(${e.id})" class="px-4 py-2 border border-red-200 text-red-600 rounded-lg text-sm">Delete</button>`:''}<button onclick="closeM('m-event')" class="btn-primary px-5 py-2 rounded-lg text-sm">Close</button></div></div>`;openM('m-event')}
function openEventForm(id){const e=DATA.events.find(x=>x.id==id)||{title:'',start:CAL.sel,end:'',st:'08:00',et:'17:00',loc:'',cat:'Academic',desc:'',hl:false};const I='w-full px-3 py-2 border border-gray-200 rounded-lg text-sm';
 $('#m-form-c').innerHTML=`<h3 class="text-lg font-bold text-primary">${id?'Edit':'Add'} Event</h3><input type="hidden" id="f-id" value="${id||''}"><input id="f-title" required placeholder="Event Title" value="${esc(e.title)}" class="${I}"><div class="grid grid-cols-2 gap-3"><label class="text-xs text-textMuted">Start date<input id="f-start" type="date" required value="${e.start}" class="${I}"></label><label class="text-xs text-textMuted">End date<input id="f-end" type="date" value="${e.end||''}" class="${I}"></label><label class="text-xs text-textMuted">Start time<input id="f-st" type="time" value="${e.st}" class="${I}"></label><label class="text-xs text-textMuted">End time<input id="f-et" type="time" value="${e.et}" class="${I}"></label></div><input id="f-loc" placeholder="Location" value="${esc(e.loc)}" class="${I}"><select id="f-cat" class="${I} bg-white">${['Academic','Technology','Competition','Social','Org Meeting','Community'].map(c=>`<option ${c==e.cat?'selected':''}>${c}</option>`).join('')}</select><textarea id="f-desc" rows="3" placeholder="Description" class="${I}">${esc(e.desc)}</textarea><label class="text-xs text-textMuted block">Event image (optional, max 2 MB)<input id="f-img" type="file" accept="image/*" class="${I}"></label><label class="flex items-center gap-2 text-sm"><input id="f-hl" type="checkbox" ${e.hl?'checked':''}> Show in year highlights</label><p class="text-xs text-textMuted">Status (Past, Current, Ongoing, Future) is worked out from the dates.</p><div class="flex justify-end gap-2"><button type="button" onclick="closeM('m-form')" class="px-4 py-2 border border-gray-200 rounded-lg text-sm">Cancel</button><button class="btn-primary px-5 py-2 rounded-lg text-sm">Save Event</button></div>`;closeM('m-event');openM('m-form')}
function saveEvent(ev){ev.preventDefault();if(!has('admin_events'))return;const id=+$('#f-id').value,s=$('#f-start').value,en=$('#f-end').value||s;if(en<s)return showModal('Check the dates','The end date cannot be before the start date.');
 const done=im=>{const o={title:$('#f-title').value.trim(),start:s,end:en,st:$('#f-st').value,et:$('#f-et').value,loc:$('#f-loc').value,cat:$('#f-cat').value,desc:$('#f-desc').value,hl:$('#f-hl').checked,pub:true};if(id){const e=DATA.events.find(x=>x.id==id);Object.assign(e,o);if(im)e.img=im}else DATA.events.push({id:Date.now(),...o,img:im||undefined});closeM('m-form');CAL.y=pd(s).getFullYear();CAL.m=pd(s).getMonth();CAL.sel=s;renderAll()};
 const f=$('#f-img').files[0];if(!f)return done();if(f.size>2e6)return showModal('Image too large','Please choose an image under 2 MB.');const r=new FileReader();r.onload=()=>done(r.result);r.readAsDataURL(f)}
function delEvent(id){if(!has('admin_events'))return;showModal('Delete event?','This removes the event from the calendar.',false,()=>{DATA.events=DATA.events.filter(e=>e.id!=id);closeM('m-event');renderAll()})}

/* ---- dashboard, home strip, public events ---- */
function nextEvent(){return DATA.events.filter(e=>pd(e.start)>TODAY).sort((a,b)=>a.start<b.start?-1:1)[0]}
function renderDash(){const u=state.currentUser;$('#dash-fines').textContent='₱'+DATA.fines.filter(f=>!f.paid).reduce((a,f)=>a+f.amt,0).toFixed(2);
 const n=nextEvent();$('#dash-event').textContent=n?n.title:'No upcoming events';$('#dash-event-date').textContent=n?fdate(n):'';const days=n?Math.ceil((pd(n.start)-TODAY)/864e5):0;$('#dash-event-badge').textContent=!n?'None':days<=7?'This Week':'Upcoming';
 $('#dash-tickets').textContent=DATA.tickets.filter(t=>t.owner==u.id&&t.status!='Resolved').length}
const flipCard=e=>`<div class="flip" tabindex="0"><div class="flip-in"><div class="flip-f card3d"><img src="${img(e)}" alt="" class="w-full h-40 object-cover"><div class="p-4"><b class="text-primary text-sm">${esc(e.title)}</b></div></div><div class="flip-b"><b class="text-secondary">${esc(e.title)}</b><p class="text-sm mt-2">${fdate(e)}</p><p class="text-xs text-blue-200 mt-1">${esc(e.loc)}</p><p class="text-xs mt-3">${esc(e.desc)}</p></div></div></div>`;
function renderPublic(){const pub=DATA.events.filter(e=>e.pub);const up=pub.filter(e=>statusOf(e)!='Past').sort((a,b)=>a.start<b.start?-1:1);const rest=pub.filter(e=>statusOf(e)=='Past').sort((a,b)=>a.start<b.start?1:-1);$('#home-events').innerHTML=[...up,...rest].slice(0,4).map(flipCard).join('');$('#pub-events').innerHTML=[...up,...rest].map(flipCard).join('')}

/* ---- help desk ---- */
const BADGE={'Open':'bg-orange-100 text-orange-700','In Progress':'bg-yellow-100 text-yellow-800','Resolved':'bg-green-100 text-green-700'};
function renderTickets(){const u=state.currentUser,staff=has('manage_tickets'),L=staff?DATA.tickets:DATA.tickets.filter(t=>t.owner==u.id);
 $('#tk-scope').textContent=staff?'Staff view: you can see and update every ticket.':'You can see your own tickets.';
 $('#tk-counts').innerHTML=['Open','In Progress','Resolved'].map(s=>`<div class="card3d p-4 text-center"><div class="text-2xl font-bold text-primary">${L.filter(t=>t.status==s).length}</div><div class="text-xs text-textMuted">${s}</div></div>`).join('');
 $('#tk-list').innerHTML=L.length?L.slice().sort((a,b)=>b.id-a.id).map(t=>`<div class="bg-surface border border-gray-100 rounded-2xl p-4 shadow-sm flex flex-wrap gap-3 justify-between items-start"><div><b class="text-primary">Ticket #${String(t.id).padStart(3,'0')}</b> <span class="text-xs text-textMuted">${esc(t.date)}</span><div class="text-sm font-medium">${esc(t.subject)}</div><div class="text-xs text-textMuted">${esc(t.cat)}</div><p class="text-sm text-textMuted mt-1">${esc(t.desc)}</p></div><div class="text-right">${staff?`<select onchange="setTk(${t.id},this.value)" class="text-xs border border-gray-200 rounded-lg px-2 py-1 bg-white">${Object.keys(BADGE).map(s=>`<option ${s==t.status?'selected':''}>${s}</option>`).join('')}</select>`:`<span class="px-3 py-1 rounded-full text-xs font-bold ${BADGE[t.status]}">${t.status.toUpperCase()}</span>`}</div></div>`).join(''):'<p class="text-textMuted text-sm">No tickets yet. Create one if you need help.</p>'}
const setTk=(id,s)=>{DATA.tickets.find(t=>t.id==id).status=s;renderAll()};
const openTicketForm=()=>openM('m-ticket');
function saveTicket(ev){ev.preventDefault();DATA.tickets.push({id:Math.max(0,...DATA.tickets.map(t=>t.id))+1,subject:$('#t-sub').value.trim(),cat:$('#t-cat').value,status:'Open',desc:$('#t-desc').value.trim(),owner:state.currentUser.id,date:ymd(new Date())});ev.target.reset();closeM('m-ticket');renderAll()}

/* ---- AI chatbot ---- */
const CHAT=[{role:'assistant',content:"Hello! I'm the iBITS Help Desk Assistant. How can I help you today?"}];let chatBusy=false;
const KB=[[/excuse|letter|absen/,'You can access the Excuse Letter template through the Forms Hub. Fill it in, then save or print it.'],[/fine|penalt|pay/,'You can view your personal fines through My Fines. It shows the amount, reason, status and due date, and only you can see your own.'],[/event|gpoa|calendar|schedule/,'Open Events / GPOA to see the calendar. Use the Past, Current, Ongoing and Future filters, or click an event for details.'],[/ticket|concern|complain|help desk/,'Go to Help Desk and press Create New Ticket. You can follow it as Open, In Progress or Resolved.'],[/cbl|by-?law/,'The CBL (Org By-Laws) is in the Forms Hub. Changes need Adviser, President and VP approval.'],[/password|login|account/,'Use Forgot password? on the login page, or create an Account Concern ticket.'],[/^(hi|hello|hey)\b/,'Hi! Ask me about fines, events, forms or tickets.']];
async function askAI(){if(AI_CONFIG.endpoint){const r=await fetch(AI_CONFIG.endpoint,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({messages:CHAT})});if(!r.ok)throw new Error('HTTP '+r.status);const j=await r.json();if(!j.reply)throw new Error('Empty reply');return j.reply}
 await new Promise(r=>setTimeout(r,600));const q=CHAT[CHAT.length-1].content.toLowerCase();const k=KB.find(x=>x[0].test(q));return k?k[1]:"I'm not sure about that one. Please create a ticket in Help Desk and an officer will help you."}
function renderChat(extra=''){const b=m=>`<div class="msg-in flex ${m.role=='user'?'justify-end':''}"><div class="max-w-[85%] px-3 py-2 rounded-2xl text-sm ${m.role=='user'?'bg-primary text-white rounded-br-sm':m.err?'bg-red-50 text-red-700 border border-red-200':'bg-white border border-gray-200 text-textDark rounded-bl-sm'}">${esc(m.content)}</div></div>`;const el=$('#chat-msgs');el.innerHTML=CHAT.map(b).join('')+extra;el.scrollTop=el.scrollHeight}
async function sendChat(e){e.preventDefault();const i=$('#chat-in'),q=i.value.trim();if(!q||chatBusy)return;i.value='';CHAT.push({role:'user',content:q});chatBusy=true;$('#chat-send').disabled=true;renderChat('<div class="text-xs text-textMuted msg-in">Assistant is typing…</div>');
 try{CHAT.push({role:'assistant',content:await askAI()})}catch(err){CHAT.push({role:'assistant',content:'Sorry, I could not reach the assistant. Please try again or create a ticket.',err:true})}chatBusy=false;$('#chat-send').disabled=false;renderChat();i.focus()}
function toggleChat(force){const p=$('#chat-panel'),open=force??p.classList.contains('hidden');p.classList.toggle('hidden',!open);p.classList.toggle('flex',open);if(open){renderChat();$('#chat-in').focus()}}

/* ---- login / register ---- */
function loginMode(m){const T={choice:['SECURE ACCESS','IDENTIFY YOURSELF TO PROCEED.'],member:['MEMBER ACCESS','ENTER YOUR CREDENTIALS TO PROCEED.'],admin:['ADMINISTRATOR','ENTER YOUR CREDENTIALS TO PROCEED.'],register:['REGISTER ACCESS','CREATE YOUR MEMBER ACCOUNT.']};LOGIN_MODE=m;$('#lg-title').textContent=T[m][0];$('#lg-sub').textContent=T[m][1];$$('#view-login [data-panel]').forEach(p=>p.classList.toggle('hidden',!p.dataset.panel.split(' ').includes(m)));$$('#view-login [data-only]').forEach(p=>p.classList.toggle('hidden',p.dataset.only!=m))}
let LOGIN_MODE='choice';
function togglePw(id,b){const i=$('#'+id);i.type=i.type=='password'?'text':'password';b.innerHTML='<i class="fa-solid '+(i.type=='password'?'fa-eye':'fa-eye-slash')+'"></i>'}
const RULES=[[/.{8,}/,'At least 8 characters'],[/[A-Z]/,'At least one uppercase letter (A-Z)'],[/[a-z]/,'At least one lowercase letter (a-z)'],[/\d/,'At least one number (0-9)'],[/[!@#$%^&*]/,'At least one special character (!@#$%^&*)'],[/^\S*$/,'No spaces in between']];
function checkPw(){const v=$('#rg-pw').value;$('#pw-rules').innerHTML=RULES.map(r=>`<li class="${r[0].test(v)?'text-green-600':''}">${r[0].test(v)?'✓':'○'} ${r[1]}</li>`).join('');return RULES.every(r=>r[0].test(v))}
function doLogin(e){e.preventDefault();const btn=$('#lg-btn'),old=btn.innerHTML,id=$('#lg-id').value.trim(),adm=LOGIN_MODE=='admin',role=adm?$('#lg-role').value:'Member';btn.disabled=true;btn.innerHTML='<i class="fa-solid fa-spinner animate-spin"></i> Authenticating...';
 /* Authentication is simulated: no backend exists in this project. Replace with a real call, e.g. fetch('/api/login'). */
 setTimeout(()=>{state.currentUser={name:role+' '+id,fullName:id,id,role,program:adm?'Officer':'Member',permissions:PERMS[role]};state.isLoggedIn=true;$('#lg-pw').value='';btn.disabled=false;btn.innerHTML=old;updateUIState();navigate('dashboard')},900)}
function doRegister(e){e.preventDefault();if(!checkPw())return showModal('Password too weak','Please meet every password rule listed.');showModal('Registration submitted','Your account request was recorded. Real registration needs the backend.',true,()=>loginMode('member'));$('#rg-pw').value=''}

/* ---- wiring (wraps existing functions, does not replace them) ---- */
function afterUI(){const u=state.currentUser,adm=has('admin_org')||has('admin_cbl');adminMenu.style.display=adm?'block':'none';$('#admin-banner').style.display=adm?'flex':'none';$('#auth-user-name').textContent=u.name;$('#auth-user-role').textContent=u.role;$('#dash-greeting-name').textContent=u.name.split(' ')[0];const p=$('#admin-banner p');if(p)p.textContent='You have '+u.role+' privileges. '+(has('admin_org')?'You can manage Org structure and verify CBL updates.':'You can verify CBL updates.');$('#add-event-btn').classList.toggle('hidden',!has('admin_events'));$('#chat-fab').classList.toggle('hidden',!state.isLoggedIn);if(!state.isLoggedIn)toggleChat(false);renderAll()}
const _uis=updateUIState;updateUIState=function(){_uis();afterUI()};
const _nav=navigate;navigate=function(id){if(id==='register'){_nav('login');loginMode('register');checkPw();replay();return}_nav(id);if(id==='login'){loginMode('choice');replay()}if(id=='events')renderCal();if(id=='helpdesk')renderTickets();if(id=='dashboard')renderDash();iconize();tilt()};
function replay(){$$('#view-login .login-in').forEach(el=>{el.style.animation='none';el.offsetHeight;el.style.animation=''})}
function renderAll(){renderStats();renderPublic();renderCal();renderTickets();renderDash();iconize();tilt()}
document.addEventListener('DOMContentLoaded',()=>{['m-event-c','m-form-c','chat-panel'].forEach(i=>$('#'+i).classList.add('wp'));$('#m-ticket form').classList.add('wp');$$('.logo-img').forEach(i=>i.src=LOGO);fixPlaceholders();state.currentUser.permissions=PERMS[state.currentUser.role]||state.currentUser.permissions;loginMode('choice');checkPw();renderAll();loadPortalData()});

```

## `public/assets/js/tailwind-config.js`

```js
tailwind.config = {
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        primary: '#06215c',
        primaryLight: '#0a328c',
        secondary: '#fad517',
        secondaryHover: '#e5c215',
        background: '#ffffff',
        soft: '#f8fafc',
        surface: '#ffffff',
        textDark: '#1f2937',
        textMuted: '#6b7280'
      },
      fontFamily: { sans: ['Poppins', 'sans-serif'] },
      animation: {
        'fade-in': 'fadeIn 0.5s ease-out forwards',
        'slide-up': 'slideUp 0.6s ease-out forwards',
        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite'
      },
      keyframes: {
        fadeIn: {'0%': {opacity:'0'}, '100%': {opacity:'1'}},
        slideUp: {'0%': {opacity:'0', transform:'translateY(20px)'}, '100%': {opacity:'1', transform:'translateY(0)'}}
      }
    }
  }
};
```

## `public/index.php`

```php
<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iBITS Portal | We Code as One</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="assets/js/tailwind-config.js"></script>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="antialiased font-sans transition-colors duration-300">
<div id="app" class="flex h-screen overflow-hidden">
<?php require __DIR__ . '/../app/views/layouts/sidebar.php'; ?>
<main class="flex-1 flex flex-col h-screen overflow-hidden relative">
<?php require __DIR__ . '/../app/views/layouts/public-nav.php'; ?>
<?php require __DIR__ . '/../app/views/layouts/mobile-header.php'; ?>
<div id="content-viewport" class="flex-1 overflow-y-auto relative bg-background">
<?php
$viewFiles = [
    'public/home','public/events','public/faqs','public/about','public/org',
    'auth/login',
    'member/dashboard','admin/cbl','admin/org','member/fines','member/forms',
    'member/events','member/helpdesk','member/settings'
];
foreach ($viewFiles as $viewFile) {
    require __DIR__ . '/../app/views/' . $viewFile . '.php';
}
require __DIR__ . '/../app/views/layouts/footer.php';
?>
</div>
</main>
</div>
<?php
foreach (['m-event','m-form','m-ticket','chat-fab','chat-panel','custom-modal'] as $component) {
    require __DIR__ . '/../app/views/layouts/' . $component . '.php';
}
?>
<script src="assets/js/app.js"></script>
</body>
</html>

```

## `public/uploads/.gitkeep`

```text

```

## `storage/logs/.gitkeep`

```text

```
