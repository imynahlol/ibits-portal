<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

/* -------------------------------------------------------------
   Current user (from session — set by AuthController on login)
   ------------------------------------------------------------- */
$isLoggedIn = !empty($_SESSION['user_id']);

$user = [
    'id'       => $_SESSION['user_id']    ?? '',
    'name'     => $_SESSION['user_name']  ?? '',
    'fullName' => $_SESSION['user_name']  ?? '',
    'role'     => $_SESSION['role']       ?? 'Member',
    'program'  => $_SESSION['program']    ?? '',
];

/* -------------------------------------------------------------
   Permissions by role (mirrors your PERMS map)
   ------------------------------------------------------------- */
$permsByRole = [
    'President'          => ['admin_org','admin_events','admin_forms','admin_cbl','manage_tickets'],
    'Vice President'     => ['admin_org','admin_events','admin_forms','admin_cbl','manage_tickets'],
    'Secretary'          => ['admin_events','admin_forms','manage_tickets'],
    'Institutional'      => ['admin_events'],
    'Treasurer'          => [],
    'Assistant Treasurer'=> [],
    'Auditor'            => [],
    'Assistant Auditor'  => [],
    'Adviser'            => ['admin_cbl'],
    'Member'             => [],
];
$perms = $permsByRole[$user['role']] ?? [];

/* -------------------------------------------------------------
   Route → view mapping (as before)
   ------------------------------------------------------------- */
$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$base = '/ibits-portal/public';
$route = trim(str_replace($base, '', $uri), '/') ?: 'home';

$routeToView = [
    'home'      => 'view-public-home',
    'login'     => 'view-login',
    'register'  => 'view-login',
    'events'    => 'view-public-events',
    'about'     => 'view-public-about',
    'faqs'      => 'view-public-faqs',
    'org'       => 'view-public-org',
    'dashboard' => 'view-member-dashboard',
    'fines'     => 'view-member-fines',
    'my-events' => 'view-member-events',
    'forms'     => 'view-member-forms',
    'helpdesk'  => 'view-member-helpdesk',
    'settings'  => 'view-member-settings',
];

$activeViewId = $routeToView[$route] ?? 'view-public-home';
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
<body
    class="antialiased font-sans transition-colors duration-300"
    data-logged-in="<?= $isLoggedIn ? '1' : '0' ?>"
    data-user-id="<?= htmlspecialchars((string)$user['id'], ENT_QUOTES) ?>"
    data-user-name="<?= htmlspecialchars($user['name'], ENT_QUOTES) ?>"
    data-user-full-name="<?= htmlspecialchars($user['fullName'], ENT_QUOTES) ?>"
    data-user-role="<?= htmlspecialchars($user['role'], ENT_QUOTES) ?>"
    data-user-program="<?= htmlspecialchars($user['program'], ENT_QUOTES) ?>"
    data-user-perms="<?= htmlspecialchars(implode(',', $perms), ENT_QUOTES) ?>"
    data-initial-view="<?= htmlspecialchars($activeViewId, ENT_QUOTES) ?>"
>
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

<script>
    window.__INITIAL_VIEW__ = <?= json_encode($activeViewId) ?>;
</script>
<script src="assets/js/app.js"></script>
</body>
</html>