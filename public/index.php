<?php
declare(strict_types=1);

session_start();

require_once dirname(__DIR__) . '/app/helpers/functions.php';
require_once dirname(__DIR__) . '/app/helpers/Csrf.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/app/middleware/AuthMiddleware.php';
require_once dirname(__DIR__) . '/app/middleware/RoleMiddleware.php';

foreach (glob(dirname(__DIR__) . '/app/models/*.php') as $m)      require_once $m;
foreach (glob(dirname(__DIR__) . '/app/controllers/*.php') as $c) require_once $c;

if (env('APP_DEBUG') === 'true') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}

$route = $_GET['route'] ?? 'public-home';

$allowed = [
    'public-home', 'public-about', 'public-org', 'public-events', 'public-faqs',
    'login', 'register', 'dashboard', 'fines', 'events', 'forms', 'helpdesk',
    'settings', 'admin-org', 'admin-cbl',
];

$currentView = in_array($route, $allowed, true) ? $route : 'public-home';

$guestOnly = ['login', 'register'];
$protected = ['dashboard', 'fines', 'events', 'forms', 'helpdesk', 'settings', 'admin-org', 'admin-cbl'];

if (in_array($currentView, $guestOnly, true)) {
    AuthMiddleware::requireGuest();
}

if (in_array($currentView, $protected, true)) {
    AuthMiddleware::requireLogin();
}

if ($currentView === 'admin-org' || $currentView === 'admin-cbl') {
    RoleMiddleware::requireAny(['admin_org', 'admin_cbl']);
}

view('layouts.app', [
    'title'       => 'iBITS Portal | We Code as One',
    'currentView' => $currentView,
]);