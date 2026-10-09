<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/app/bootstrap.php';

if ($u = current_user()) redirect(home_for($u));

$errors = [];
$idNumber = '';
if (is_post()) {
    csrf_check();
    $idNumber = post('id_number');
    $current = (string)($_POST['current_password'] ?? '');
    $new = (string)($_POST['new_password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');

    if (($wait = login_locked_for()) > 0) {
        $errors[] = "Too many attempts. Try again in {$wait} seconds.";
    } else {
        $member = q_one("SELECT id, password_hash FROM users
                         WHERE id_number = ? AND account_type = 'member' AND status = 'active'", [$idNumber]);
        if (!$member || !password_verify($current, $member['password_hash'])) {
            $errors[] = 'Student ID number or current password is incorrect.';
            $attempts = $_SESSION['_attempts'] ?? ['n' => 0, 'until' => 0];
            $attempts['n']++;
            if ($attempts['n'] >= MAX_ATTEMPTS) $attempts = ['n' => 0, 'until' => time() + LOCK_SECONDS];
            $_SESSION['_attempts'] = $attempts;
        } else {
            unset($_SESSION['_attempts']);
            if (strlen($new) < 8) $errors[] = 'New password must be at least 8 characters.';
            if ($new === 'admin123') $errors[] = 'Choose a password other than the default password.';
            if ($new !== $confirm) $errors[] = 'New password and confirmation do not match.';
            if (!$errors) {
                q("UPDATE users SET password_hash = ? WHERE id = ? AND account_type = 'member'",
                    [password_hash($new, PASSWORD_DEFAULT), (int)$member['id']]);
                flash('success', 'Password changed successfully. You can now sign in with your new password.');
                redirect('login.php?as=member');
            }
        }
    }
}

html_head('Change Member Password', 'auth');
?>
<canvas class="rain password-rain" data-rain="dark" aria-hidden="true"></canvas>
<div class="auth-stage">
  <?= back_button('login.php?as=member', 'Back to Member Login') ?>
<div class="auth-card">
  <img class="auth-logo" src="<?= e(asset('images/ibits-logo.png')) ?>" alt="iBITS">
  <h1>Change Password</h1>
  <p class="sub">Enter your member ID and current password to set a new password.</p>
  <?= flash_render() ?>
  <?php if ($errors): ?><div class="alert error"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
  <form method="post" autocomplete="off">
    <?= csrf_field() ?>
    <div class="field"><label for="id_number">Student ID Number</label><input id="id_number" name="id_number" type="text" value="<?= e($idNumber) ?>" placeholder="2025-00001-LQ-0" autocomplete="username" required autofocus></div>
    <div class="field"><label for="current_password">Current password</label><input id="current_password" name="current_password" type="password" autocomplete="current-password" required></div>
    <div class="field"><label for="new_password">New password</label><input id="new_password" name="new_password" type="password" minlength="8" autocomplete="new-password" required></div>
    <div class="field"><label for="confirm_password">Confirm new password</label><input id="confirm_password" name="confirm_password" type="password" minlength="8" autocomplete="new-password" required></div>
    <button class="btn btn-gold" type="submit">Save Password</button>
  </form>
</div></div>
<?php html_foot(); ?>
