<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/app/bootstrap.php';

if (current_user()) {
    $u = current_user();
    if (($u['account_type'] ?? '') === 'admin') redirect('change-password.php');
    redirect(home_for($u));
}

$errors = [];
$old = ['id_number' => '', 'position' => ''];
if (is_post()) {
    csrf_check();
    $old['id_number'] = post('id_number');
    $old['position'] = post('position');
    $new = (string)($_POST['new_password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');

    if (($wait = login_locked_for()) > 0) {
        $errors[] = "Too many attempts. Try again in {$wait} seconds.";
    } else {
        $u = q_one("SELECT id FROM users WHERE id_number = ? AND position = ? AND account_type = 'admin' AND status = 'active'", [$old['id_number'], $old['position']]);
        if (!$u) {
            $errors[] = 'No active officer account matches that ID number and position.';
            $a = $_SESSION['_attempts'] ?? ['n' => 0, 'until' => 0];
            $a['n']++;
            if ($a['n'] >= MAX_ATTEMPTS) $a = ['n' => 0, 'until' => time() + LOCK_SECONDS];
            $_SESSION['_attempts'] = $a;
        } else {
            unset($_SESSION['_attempts']);
            if (strlen($new) < 8) $errors[] = 'New password must be at least 8 characters.';
            if ($new === 'admin123') $errors[] = 'Choose a password other than the default password.';
            if ($new !== $confirm) $errors[] = 'New password and confirmation do not match.';
            if (!$errors) {
                q("UPDATE users SET password_hash = ?, must_change_password = 0 WHERE id = ? AND account_type = 'admin'", [password_hash($new, PASSWORD_DEFAULT), (int)$u['id']]);
                flash('success', 'Password changed successfully. You can now sign in with your new password.');
                redirect('login.php?as=admin');
            }
        }
    }
}

html_head('Change Admin Password', 'auth');
?>
<canvas class="rain password-rain" data-rain="dark" aria-hidden="true"></canvas>
<div class="auth-stage"><div class="auth-card">
  <img class="auth-logo" src="<?= e(asset('images/ibits-logo.png')) ?>" alt="iBITS">
  <h1>Change Admin Password</h1>
  <p class="sub">Enter your officer ID and position to set a new password.</p>
  <?= flash_render() ?>
  <?php if ($errors): ?><div class="alert error"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
  <form method="post" autocomplete="off">
    <?= csrf_field() ?>
    <div class="field"><label for="position">Position</label><select id="position" name="position" required><option value="">Select your position</option><?php foreach (array_keys(POSITIONS) as $pos): ?><option value="<?= e($pos) ?>" <?= $old['position'] === $pos ? 'selected' : '' ?>><?= e($pos) ?></option><?php endforeach; ?></select></div>
    <div class="field"><label for="id_number">Student ID Number</label><input id="id_number" name="id_number" type="text" value="<?= e($old['id_number']) ?>" autocomplete="username" required></div>
    <div class="field"><label for="new_password">New password</label><input id="new_password" name="new_password" type="password" minlength="8" autocomplete="new-password" required></div>
    <div class="field"><label for="confirm_password">Confirm new password</label><input id="confirm_password" name="confirm_password" type="password" minlength="8" autocomplete="new-password" required></div>
    <button class="btn btn-gold" type="submit">Save Password</button>
  </form>
  <p class="auth-links"><a href="<?= e(url('login.php?as=admin')) ?>">&larr; Back to Admin Login</a></p>
</div></div>
<?php html_foot(); ?>
