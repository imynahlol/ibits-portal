<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/app/bootstrap.php';

if ($u = current_user()) redirect(home_for($u));

// Step 1 (no ?as=) is Secure Access: choose Member or Admin. Step 2 is the form.
$as = $_GET['as'] ?? $_POST['as'] ?? '';
$as = in_array($as, ['member', 'admin'], true) ? $as : '';
$old = ['id' => '', 'position' => ''];

if (is_post() && $as) {
    csrf_check();
    $old['id'] = post('id_number');
    $old['position'] = post('position');

    if (($wait = login_locked_for()) > 0) {
        flash('error', "Too many attempts. Try again in {$wait} seconds.");
    } elseif ($as === 'admin' && !isset(POSITIONS[$old['position']])) {
        flash('error', 'Please choose your position.');
    } elseif ($user = attempt_login($as, $old['id'], (string)($_POST['password'] ?? ''), $old['position'])) {
        redirect(!empty($user['must_change_password']) ? 'change-password.php' : home_for($user));
    } else {
        flash('error', $as === 'admin'
            ? 'Invalid ID number, password or position.'
            : 'Invalid ID number or password.');
    }
}

html_head($as ? ($as === 'admin' ? 'Admin Log in' : 'Member Log in') : 'Secure Access', 'auth');
?>
<canvas class="rain" style="position:fixed;opacity:.5" aria-hidden="true"></canvas>
<div class="auth-top">
  <a class="brand" href="<?= e(url('index.php')) ?>"><img src="<?= e(asset('images/ibits-logo.png')) ?>" alt=""><span>iBITS PUPLC</span></a>
  <?= $as ? back_button('login.php', 'Change access type') : back_button('index.php', 'Back to home') ?>
</div>
<div class="auth-stage">
<div class="auth-card">
  <img class="auth-logo" src="<?= e(asset('images/ibits-logo.png')) ?>" alt="iBITS">

<?php if (!$as): ?>
  <!-- ===== STEP 1: SECURE ACCESS ===== -->
  <h1>Secure Access</h1>
  <p class="sub">Identify yourself to proceed.</p>
  <?= flash_render() ?>

  <a class="choice" href="<?= e(url('login.php?as=member')) ?>">
    <span class="ci"><?= icon('user') ?></span>
    <span><b>Member Access</b><small>Help desk: view and submit concerns</small></span>
  </a>
  <a class="choice" href="<?= e(url('login.php?as=admin')) ?>">
    <span class="ci"><?= icon('cbl') ?></span>
    <span><b>Admin Console</b><small>Organization officers</small></span>
  </a>
  <p class="auth-links" style="justify-content:center">Not a member yet?&nbsp;<a href="<?= e(url('register.php')) ?>">Join iBITS</a></p>

<?php else: ?>
  <!-- ===== STEP 2: LOG IN ===== -->
  <div class="role-chip"><?= icon($as === 'admin' ? 'cbl' : 'user') ?><?= $as === 'admin' ? 'Admin Console' : 'Member Access' ?></div>
  <h1>Log in</h1>
  <p class="sub"><?= $as === 'admin' ? 'Choose your position and enter your credentials.' : 'Enter your credentials to continue.' ?></p>
  <?= flash_render() ?>

  <form method="post" action="<?= e(url('login.php')) ?>" autocomplete="on">
    <?= csrf_field() ?>
    <input type="hidden" name="as" value="<?= e($as) ?>">

    <?php if ($as === 'admin'): ?>
      <div class="field">
        <label for="position">Position</label>
        <select id="position" name="position" required>
          <option value="">Select your position</option>
          <?php foreach (array_keys(POSITIONS) as $pos): ?>
            <option value="<?= e($pos) ?>" <?= $old['position'] === $pos ? 'selected' : '' ?>><?= e($pos) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    <?php endif; ?>

    <div class="field">
      <label for="id_number"><?= $as === 'admin' ? 'Student ID Number' : 'Student ID Number' ?></label>
      <input id="id_number" name="id_number" type="text" value="<?= e($old['id']) ?>" placeholder="2025-00001-LQ-0" autocomplete="username" required autofocus>
    </div>
    <div class="field">
      <label for="password">Password</label>
      <div class="pw">
        <input id="password" name="password" type="password" placeholder="Password" autocomplete="current-password" required>
        <button type="button" data-pw-toggle="password" aria-label="Show password"><svg class="ico" viewBox="0 0 24 24"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button>
      </div>
    </div>
    <button class="btn btn-gold" type="submit">Sign In</button>
  </form>

  <div class="auth-links">
    <?php if ($as === 'admin'): ?><a class="btn btn-line btn-sm" href="<?= e(url('change-admin-password.php')) ?>">Change Password</a><?php endif; ?>
    <?php if ($as === 'member'): ?><a class="btn btn-line btn-sm" href="<?= e(url('change-member-password.php')) ?>">Change Password</a><a href="<?= e(url('register.php')) ?>">Join iBITS</a><?php endif; ?>
  </div>
<?php endif; ?>
</div>
</div>
<?php html_foot(); ?>