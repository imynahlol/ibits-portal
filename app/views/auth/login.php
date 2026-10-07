<?php require __DIR__ . '/../layouts/header.php'; ?>

<h2>Login</h2>

<?php if ($msg = flash('success')): ?>
    <div class="alert alert-success"><?= e($msg) ?></div>
<?php endif; ?>

<?php if (!empty($_SESSION['errors'])): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php unset($_SESSION['errors']); ?>
<?php endif; ?>

<form method="POST" action="<?= e($_ENV['APP_URL']) ?>/login" class="form">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <label>ID Number</label>
    <input
        type="text"
        name="id_number"
        value="<?= e(old('id_number')) ?>"
        placeholder="2025-00122-LQ-0"
        required
    >

    <label>Password</label>
    <input type="password" name="password" required>

    <button type="submit">Login</button>
</form>

<p>No account yet? <a href="<?= e($_ENV['APP_URL']) ?>/register">Register here</a></p>

<?php
unset($_SESSION['old']);
require __DIR__ . '/../layouts/footer.php';
?>