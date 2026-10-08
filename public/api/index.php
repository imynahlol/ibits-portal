<?php
declare(strict_types=1);

session_start();

require_once dirname(__DIR__, 2) . '/app/helpers/functions.php';
require_once dirname(__DIR__, 2) . '/app/helpers/Csrf.php';
require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/app/middleware/AuthMiddleware.php';
require_once dirname(__DIR__, 2) . '/app/middleware/RoleMiddleware.php';

foreach (glob(dirname(__DIR__, 2) . '/app/models/*.php') as $m)      require_once $m;
foreach (glob(dirname(__DIR__, 2) . '/app/controllers/*.php') as $c) require_once $c;

header('Content-Type: application/json; charset=utf-8');

$resource = trim((string) ($_GET['r'] ?? ''), '/');
$method   = $_SERVER['REQUEST_METHOD'];

$routes = [
    'bootstrap'         => ['GET'  => [ApiController::class,   'bootstrap']],
    'auth/login'        => ['POST' => [AuthController::class,  'login']],
    'auth/register'     => ['POST' => [AuthController::class,  'register']],
    'auth/logout'       => ['POST' => [AuthController::class,  'logout']],
    'auth/me'           => ['GET'  => [AuthController::class,  'me']],
    'events'            => ['GET'  => [EventController::class, 'index']],
    'events/store'      => ['POST' => [EventController::class, 'store']],
    'events/update'     => ['POST' => [EventController::class, 'update']],
    'events/delete'     => ['POST' => [EventController::class, 'destroy']],
    'fines'             => ['GET'  => [FineController::class,  'index']],
    'fines/store'       => ['POST' => [FineController::class,  'store']],
    'fines/pay'         => ['POST' => [FineController::class,  'pay']],
    'tickets'           => ['GET'  => [TicketController::class,'index']],
    'tickets/store'     => ['POST' => [TicketController::class,'store']],
    'tickets/status'    => ['POST' => [TicketController::class,'status']],
    'admin/org'         => ['GET'  => [AdminController::class, 'org']],
    'admin/cbl'         => ['GET'  => [AdminController::class, 'cbl']],
    'admin/cbl/approve' => ['POST' => [AdminController::class, 'approveAmendment']],
    'admin/cbl/reject'  => ['POST' => [AdminController::class, 'rejectAmendment']],
    'ai'                => ['POST' => [ApiController::class,   'ai']],
];

if (!isset($routes[$resource][$method])) {
    json_out(['ok' => false, 'error' => 'Not found'], 404);
}

[$class, $action] = $routes[$resource][$method];

// CSRF check for state-changing requests (skipped for login/register so guests can sign in).
if ($method === 'POST' && !in_array($resource, ['auth/login', 'auth/register'], true)) {
    Csrf::verifyOrFail($_SERVER['HTTP_X_CSRF_TOKEN'] ?? json_input()['_csrf'] ?? null);
}

(new $class())->{$action}();