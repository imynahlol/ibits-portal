<!DOCTYPE html>
<html>
<head>
    <title><?= e($_ENV['APP_NAME'] ?? 'iBITS Portal') ?></title>
    <link rel="stylesheet" href="<?= e($_ENV['APP_URL']) ?>/assets/css/style.css">
</head>
<body>
<nav class="navbar">
    <a class="brand" href="<?= e($_ENV['APP_URL']) ?>/">iBITS</a>
    <div class="nav-links">
        <?php if (!empty($_SESSION['user_id'])): ?>
            <span class="welcome">Hi, <?= e($_SESSION['user_name']) ?></span>
            <a href="<?= e($_ENV['APP_URL']) ?>/dashboard">Dashboard</a>
            <a href="<?= e($_ENV['APP_URL']) ?>/logout">Logout</a>
        <?php else: ?>
            <a href="<?= e($_ENV['APP_URL']) ?>/login">Login</a>
            <a href="<?= e($_ENV['APP_URL']) ?>/register">Register</a>
        <?php endif; ?>
    </div>
</nav>
<main class="container">