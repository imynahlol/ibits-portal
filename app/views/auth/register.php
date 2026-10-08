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

<form method="POST" action="<?= e($_ENV['APP_URL']) ?>/register" class="form" id="registerForm">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <fieldset class="radio-group">
        <legend>I am a:</legend>
        <label>
            <input type="radio" name="account_type" value="student"
                   <?= old('account_type', 'student') === 'student' ? 'checked' : '' ?>>
            Student
        </label>
        <label>
            <input type="radio" name="account_type" value="adviser"
                   <?= old('account_type') === 'adviser' ? 'checked' : '' ?>>
            Adviser
        </label>
    </fieldset>

    <label>Full Name</label>
    <input type="text" name="full_name" value="<?= e(old('full_name')) ?>" required>

    <label>ID Number</label>
    <input
        type="text"
        name="id_number"
        id="id_number"
        value="<?= e(old('id_number')) ?>"
        placeholder="2025-00122-LQ-0"
        required
    >
    <small id="id_hint">Students: <code>20XX-XXXXX-LQ-X</code></small>

    <label>Sex</label>
    <select name="sex" required>
        <option value="">-- Select --</option>
        <option value="Female"     <?= old('sex') === 'Female'     ? 'selected' : '' ?>>Female</option>
        <option value="Male"       <?= old('sex') === 'Male'       ? 'selected' : '' ?>>Male</option>
        <option value="Non-Binary" <?= old('sex') === 'Non-Binary' ? 'selected' : '' ?>>Non-Binary</option>
    </select>

    <div id="student_fields">
        <label>Program</label>
        <input type="text" name="program" value="<?= e(old('program')) ?>">

        <label>Year Level</label>
        <input type="text" name="year_level" value="<?= e(old('year_level')) ?>">
    </div>

    <label>Password</label>
    <input type="password" name="password" required minlength="6">

    <button type="submit">Register</button>
</form>

<p>Already registered? <a href="<?= e($_ENV['APP_URL']) ?>/login">Login here</a></p>

<script>
(function () {
    const radios       = document.querySelectorAll('input[name="account_type"]');
    const idInput      = document.getElementById('id_number');
    const idHint       = document.getElementById('id_hint');
    const studentBlock = document.getElementById('student_fields');

    function applyMode() {
        const mode = document.querySelector('input[name="account_type"]:checked').value;

        if (mode === 'adviser') {
            idInput.placeholder = '00812';
            idInput.pattern     = '^[0-9]{5}$';
            idHint.innerHTML    = 'Advisers: <code>XXXXX</code> (5 digits)';
            studentBlock.style.display = 'none';
            studentBlock.querySelectorAll('input').forEach(i => i.required = false);
        } else {
            idInput.placeholder = '2025-00122-LQ-0';
            idInput.pattern     = '^[0-9]{4}-[0-9]{5}-LQ-[0-9]$';
            idHint.innerHTML    = 'Students: <code>20XX-XXXXX-LQ-X</code>';
            studentBlock.style.display = 'block';
            studentBlock.querySelectorAll('input').forEach(i => i.required = true);
        }
    }

    radios.forEach(r => r.addEventListener('change', applyMode));
    applyMode();
})();
</script>

<?php
unset($_SESSION['old']);
require __DIR__ . '/../layouts/footer.php';
?>