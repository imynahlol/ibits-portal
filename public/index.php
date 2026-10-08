<?php
// front controller: every request goes through here.

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/functions.php';

// small router

$base = '/ibits-portal/public';
$uri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace($base, '', $uri);
$path = trim($path, '/');
$method = $_SERVER['REQUEST_METHOD'];

// route table: 'method path' => [Controller, method]
$routes = [
    // Public
    'GET '          => ['PageController', 'home'],
    'GET home'      => ['PageController', 'home'],
    'GET about'     => ['PageController', 'about'],
    'GET events'    => ['PageController', 'events'],
    'GET faqs'      => ['PageController', 'faqs'],

    // Auth
    'GET login'     => ['AuthController', 'showLogin'],
    'GET register'  => ['AuthController', 'showRegister'],
    'POST login'    => ['AuthController', 'login'],
    'POST register' => ['AuthController', 'register'],
    'GET logout'    => ['AuthController', 'logout'],

    // Member
    'GET dashboard' => ['DashboardController', 'index'],
];

$key = $method . ' ' . $path;
$key = rtrim($key, ' '); // normalize '' case

// fallback: if path is empty and method is GET, treat as login
if ($path === '' && $method === 'GET') {
    $key = 'GET ';
}

if (!isset($routes[$key])) {
    http_response_code(404);
    echo "<h1>404 - Page Not Found</h1>";
    echo "<p>No route for <code>" . e($method . ' /' . $path) . "</code></p>";
    exit;
}

[$controller, $action] = $routes[$key];

$controllerFile = __DIR__ . "/../app/controllers/$controller.php";
if (!file_exists($controllerFile)) {
    http_response_code(500);
    die("Controller not found: $controller");
}

require_once $controllerFile;
$instance = new $controller();
$instance->$action();