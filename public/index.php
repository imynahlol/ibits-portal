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
