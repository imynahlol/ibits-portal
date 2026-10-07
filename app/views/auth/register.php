<?php require __DIR__ . '/../layouts/header.php'; ?>

<h2>Register</h2>

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

<form method="POST" action="<?= e($_ENV['APP_URL']) ?>/register" class="form">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <label>Full Name</label>
    <input type="text" name="full_name" value="<?= e(old('full_name')) ?>" required>

    <label>Student Number</label>
    <input type="text" name="student_number" value="<?= e(old('student_number')) ?>" required>

    <label>Sex</label>
    <select name="sex" required>
        <option value="">-- Select --</option>
        <option value="Male"   <?= old('sex') === 'Male'   ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= old('sex') === 'Female' ? 'selected' : '' ?>>Female</option>
        <option value="Other"  <?= old('sex') === 'Other'  ? 'selected' : '' ?>>Other</option>
    </select>

    <label>Program</label>
    <input type="text" name="program" value="<?= e(old('program')) ?>" required>

    <label>Year Level</label>
    <input type="text" name="year_level" value="<?= e(old('year_level')) ?>" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <button type="submit">Register</button>
</form>

<p>Already registered? <a href="<?= e($_ENV['APP_URL']) ?>/login">Login here</a></p>

<?php
unset($_SESSION['old']);
require __DIR__ . '/../layouts/footer.php';
?>