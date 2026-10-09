<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/app/bootstrap.php';
$u = current_user();
if (!$u || ($u['account_type'] ?? '') !== 'admin') { flash('error', 'Officer sign-in is required.'); redirect('login.php?as=admin'); }
$errors = [];
if (is_post()) {
    csrf_check();
    $current = (string)($_POST['current_password'] ?? '');
    $new = (string)($_POST['new_password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');
    $row = q_one('SELECT password_hash FROM users WHERE id = ?', [(int)$u['id']]);
    if (!$row || !password_verify($current, $row['password_hash'])) $errors[] = 'Current password is incorrect.';
    if (strlen($new) < 8) $errors[] = 'New password must be at least 8 characters.';
    if ($new === 'admin123') $errors[] = 'Choose a password other than the default password.';
    if ($new !== $confirm) $errors[] = 'New password and confirmation do not match.';
    if (!$errors) {
        q('UPDATE users SET password_hash = ?, must_change_password = 0 WHERE id = ?', [password_hash($new, PASSWORD_DEFAULT), (int)$u['id']]);
        $_SESSION['user']['must_change_password'] = 0;
        flash('success', 'Password changed successfully.');
        redirect(home_for($_SESSION['user']));
    }
}
html_head('Change Password', 'dash');
?>
<div class="auth-stage">
  <?php if (empty($u['must_change_password'])) echo back_button('admin/index.php', 'Back to dashboard'); ?>
<div class="auth-card">
  <img class="auth-logo" src="<?= e(asset('images/ibits-logo.png')) ?>" alt="iBITS">
  <h1>Change Password</h1>
  <p class="sub"><?= !empty($u['must_change_password']) ? 'For security, change the default officer password before continuing.' : 'Update your officer account password.' ?></p>
  <?= flash_render() ?>
  <?php if ($errors): ?><div class="alert error"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
  <form method="post" autocomplete="off">
    <?= csrf_field() ?>
    <div class="field"><label for="current_password">Current password</label><input id="current_password" name="current_password" type="password" autocomplete="current-password" required></div>
    <div class="field"><label for="new_password">New password</label><input id="new_password" name="new_password" type="password" minlength="8" autocomplete="new-password" required></div>
    <div class="field"><label for="confirm_password">Confirm new password</label><input id="confirm_password" name="confirm_password" type="password" minlength="8" autocomplete="new-password" required></div>
    <button class="btn btn-gold" type="submit">Save Password</button>
  </form>
</div></div>
<?php html_foot(); ?>