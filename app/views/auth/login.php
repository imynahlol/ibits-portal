<?php

require_once __DIR__ . '/../../helpers/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['user_id'])) {
    redirect($_ENV['APP_URL'] . '/dashboard');
}

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

$old_id = $_SESSION['old']['id_number'] ?? '';
unset($_SESSION['old']);

$success = flash('success');

$logoUrl = e($_ENV['APP_URL']) . '/assets/images/ibits-logo.png';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Login | <?= e($_ENV['APP_NAME'] ?? 'iBITS Portal') ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">

    <link rel="stylesheet" href="<?= e($_ENV['APP_URL']) ?>/assets/css/login.css">

    <noscript>
        <style>html:not(.ui-ready) body { visibility: visible; }</style>
    </noscript>
</head>

<body data-login-app data-logo="<?= $logoUrl ?>">

    <h2>Login</h2>

    <form method="POST"
          action="<?= e($_ENV['APP_URL']) ?>/login"
          class="form">

        <!-- Hidden input to track selected role (member or admin) -->
        <input type="hidden" name="role" id="user_role" value="">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <label>ID Number</label>
        <input type="text"
               name="id_number"
               value="<?= e($old_id) ?>"
               placeholder="2025-00122-LQ-0"
               autocomplete="username"
               required>

        <label>Password</label>
        <input type="password"
               name="password"
               autocomplete="current-password"
               required>

        <button type="submit">Sign In</button>
    </form>

    <p data-auth-note>
        No account yet?
        <a href="<?= e($_ENV['APP_URL']) ?>/register">Register here</a>
    </p>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <ul><li><?= e($success) ?></li></ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <script src="<?= e($_ENV['APP_URL']) ?>/assets/js/login.js" defer></script>

</body>

</html>