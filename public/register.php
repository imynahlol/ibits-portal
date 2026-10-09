<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/app/bootstrap.php';

if ($u = current_user()) redirect(home_for($u));

// Joining creates a MEMBER account only. Officer/adviser accounts are created
// by the President or Vice President in the Organization page.
$f = ['full_name' => '', 'id_number' => '', 'sex' => '', 'program' => '', 'year_level' => ''];
$errors = [];
$years = ['1st Year', '2nd Year', '3rd Year', '4th Year'];

if (is_post()) {
    csrf_check();
    foreach ($f as $k => $_) $f[$k] = post($k);
    $pw = (string)($_POST['password'] ?? '');

    if (mb_strlen($f['full_name']) < 3 || mb_strlen($f['full_name']) > 100) $errors[] = 'Enter your full name.';
    if (!preg_match('/^\d{4}-\d{5}-LQ-\d$/', $f['id_number']))               $errors[] = 'ID number must look like 2025-00001-LQ-0.';
    if (!in_array($f['sex'], ['Female', 'Male', 'Non-Binary'], true))         $errors[] = 'Select your sex.';
    if (!isset(PROGRAMS[$f['program']]))                                      $errors[] = 'Select your program.';
    elseif (!in_array($f['year_level'], array_slice($years, 0, PROGRAMS[$f['program']]), true)) $errors[] = 'Select your year level.';
    if (strlen($pw) < 8)                                                      $errors[] = 'Password must be at least 8 characters.';
    if ($pw !== (string)($_POST['password2'] ?? ''))                          $errors[] = 'Passwords do not match.';

    if (!$errors && q_val('SELECT 1 FROM users WHERE id_number = ?', [$f['id_number']])) {
        $errors[] = 'An account with this ID number already exists.';
    }
    if (!$errors) {
        q('INSERT INTO users (full_name,id_number,sex,program,year_level,password_hash,account_type) VALUES (?,?,?,?,?,?,?)',
          [$f['full_name'], $f['id_number'], $f['sex'], $f['program'], $f['year_level'], password_hash($pw, PASSWORD_DEFAULT), 'member']);
        flash('success', 'Welcome to iBITS! Your account is ready. Please log in.');
        redirect('login.php?as=member');
    }
    foreach ($errors as $er) flash('error', $er);
}

html_head('Join the Org', 'auth');
?>
<canvas class="rain" style="position:fixed;opacity:.5" aria-hidden="true"></canvas>
<div class="auth-top">
  <a class="brand" href="<?= e(url('index.php')) ?>"><img src="<?= e(asset('images/ibits-logo.png')) ?>" alt=""><span>iBITS PUPLC</span></a>
  <a class="back" href="<?= e(url('index.php')) ?>">&larr; Back to home</a>
</div>

<div class="auth-stage">
<div class="auth-card wide">
  <h1>Join iBITS</h1>
  <p class="sub">Create your member account. We code as one.</p>
  <?= flash_render() ?>

  <form method="post" action="<?= e(url('register.php')) ?>">
    <?= csrf_field() ?>
    <div class="field"><label for="full_name">Full name</label>
      <input id="full_name" name="full_name" value="<?= e($f['full_name']) ?>" autocomplete="name" required></div>
    <div class="grid2">
      <div class="field"><label for="id_number">ID number</label>
        <input id="id_number" name="id_number" value="<?= e($f['id_number']) ?>" placeholder="2025-00001-LQ-0" pattern="\d{4}-\d{5}-LQ-\d" required></div>
      <div class="field"><label for="sex">Sex</label>
        <select id="sex" name="sex" required>
          <option value="">Select</option>
          <?php foreach (['Female', 'Male', 'Non-Binary'] as $s): ?><option <?= $f['sex'] === $s ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?>
        </select></div>
    </div>
    <div class="grid2">
      <div class="field"><label for="program">Program</label>
        <select id="program" name="program" data-levels='<?= e(json_encode(PROGRAMS)) ?>' required>
          <option value="">Select program</option>
          <?php foreach (PROGRAMS as $p => $_): ?><option <?= $f['program'] === $p ? 'selected' : '' ?>><?= e($p) ?></option><?php endforeach; ?>
        </select></div>
      <div class="field"><label for="year_level">Year level</label>
        <select id="year_level" name="year_level" data-selected="<?= e($f['year_level']) ?>" required disabled><option value="">Select program first</option></select></div>
    </div>
    <div class="grid2">
      <div class="field"><label for="password">Password</label>
        <input id="password" name="password" type="password" minlength="8" autocomplete="new-password" required>
        <div class="hint">At least 8 characters.</div></div>
      <div class="field"><label for="password2">Confirm password</label>
        <input id="password2" name="password2" type="password" minlength="8" autocomplete="new-password" required></div>
    </div>
    <button class="btn btn-gold" type="submit">Create Account</button>
  </form>
  <div class="auth-links" style="justify-content:center">Already a member?&nbsp;<a href="<?= e(url('login.php')) ?>">Log in</a></div>
</div>
</div>
<?php html_foot(); ?>
