<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/app/bootstrap.php';

if ($u = current_user()) redirect(home_for($u));

$f = ['account_type' => 'member', 'full_name' => '', 'id_number' => '', 'sex' => '', 'program' => '', 'year_level' => '', 'position' => ''];
$errors = [];
$years = ['1st Year', '2nd Year', '3rd Year', '4th Year'];

if (is_post()) {
    csrf_check();
    foreach ($f as $k => $_) $f[$k] = post($k);
    $pw = (string)($_POST['password'] ?? '');
    $pw2 = (string)($_POST['password2'] ?? '');
    $isAdmin = $f['account_type'] === 'admin';

    if (!in_array($f['account_type'], ['member', 'admin'], true)) $errors[] = 'Choose a valid account type.';
    if (mb_strlen($f['full_name']) < 3 || mb_strlen($f['full_name']) > 100) $errors[] = 'Enter your full name.';
    if (!preg_match('/^\d{4}-\d{5}-LQ-\d$/', $f['id_number'])) $errors[] = 'ID number must look like 2025-00001-LQ-0.';
    if (!in_array($f['sex'], ['Female', 'Male', 'Non-Binary'], true)) $errors[] = 'Select your sex.';

    if (!isset(PROGRAMS[$f['program']])) $errors[] = 'Select your program.';
    elseif (!in_array($f['year_level'], array_slice($years, 0, PROGRAMS[$f['program']]), true)) $errors[] = 'Select your year level for the selected program.';
    if ($isAdmin && !isset(POSITIONS[$f['position']])) $errors[] = 'Select a valid officer position.';

    if (strlen($pw) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($pw !== $pw2) $errors[] = 'Passwords do not match.';
    if (!$errors && q_val('SELECT 1 FROM users WHERE id_number = ?', [$f['id_number']])) $errors[] = 'An account with this ID number already exists.';

    if (!$errors) {
        q('INSERT INTO users (full_name,id_number,sex,program,year_level,password_hash,account_type,position) VALUES (?,?,?,?,?,?,?,?)', [
            $f['full_name'], $f['id_number'], $f['sex'],
            $f['program'], $f['year_level'],
            password_hash($pw, PASSWORD_DEFAULT), $f['account_type'],
            $isAdmin ? $f['position'] : null
        ]);
        flash('success', $isAdmin ? 'Officer account created. Please log in.' : 'Welcome to iBITS! Your member account is ready. Please log in.');
        redirect('login.php?as=' . ($isAdmin ? 'admin' : 'member'));
    }
    foreach ($errors as $er) flash('error', $er);
}

html_head('Register', 'auth');
?>
<canvas class="rain" style="position:fixed;opacity:.5" aria-hidden="true"></canvas>
<div class="auth-top">
  <a class="brand" href="<?= e(url('index.php')) ?>"><img src="<?= e(asset('images/ibits-logo.png')) ?>" alt=""><span>iBITS PUPLC</span></a>
  <a class="back" href="<?= e(url('index.php')) ?>">&larr; Back to home</a>
</div>
<div class="auth-stage">
<div class="auth-card wide">
  <img class="auth-logo" src="<?= e(asset('images/ibits-logo.png')) ?>" alt="iBITS">
  <h1>Create an Account</h1>
  <p class="sub">Join the iBITS portal. Officers and members must provide their program and year level.</p>
  <?= flash_render() ?>
  <form method="post" action="<?= e(url('register.php')) ?>">
    <?= csrf_field() ?>
    <div class="field"><label for="account_type">Account type</label>
      <select id="account_type" name="account_type" required>
        <option value="member" <?= $f['account_type'] === 'member' ? 'selected' : '' ?>>Member</option>
        <option value="admin" <?= $f['account_type'] === 'admin' ? 'selected' : '' ?>>Organization Officer / Admin</option>
      </select>
    </div>
    <div class="field"><label for="full_name">Full name</label>
      <input id="full_name" name="full_name" value="<?= e($f['full_name']) ?>" autocomplete="name" required></div>
    <div class="grid2">
      <div class="field"><label for="id_number">Student ID number</label>
        <input id="id_number" name="id_number" value="<?= e($f['id_number']) ?>" placeholder="2025-00001-LQ-0" pattern="\d{4}-\d{5}-LQ-\d" required></div>
      <div class="field"><label for="sex">Sex</label>
        <select id="sex" name="sex" required>
          <option value="">Select</option>
          <?php foreach (['Female', 'Male', 'Non-Binary'] as $sx): ?><option value="<?= e($sx) ?>" <?= $f['sex'] === $sx ? 'selected' : '' ?>><?= e($sx) ?></option><?php endforeach; ?>
        </select></div>
    </div>
    <div id="student-fields">
      <div class="grid2">
        <div class="field"><label for="program">Program</label>
          <select id="program" name="program" data-levels='<?= e(json_encode(PROGRAMS)) ?>'>
            <option value="">Select program</option>
            <?php foreach (PROGRAMS as $program => $_): ?><option value="<?= e($program) ?>" <?= $f['program'] === $program ? 'selected' : '' ?>><?= e($program) ?></option><?php endforeach; ?>
          </select></div>
        <div class="field"><label for="year_level">Year level</label>
          <select id="year_level" name="year_level" data-selected="<?= e($f['year_level']) ?>"><option value="">Select year</option></select></div>
      </div>
    </div>
    <div id="admin-fields" style="display:none">
      <div class="field"><label for="position">Officer position</label>
        <select id="position" name="position">
          <option value="">Select position</option>
          <?php foreach (array_keys(POSITIONS) as $pos): ?><option value="<?= e($pos) ?>" <?= $f['position'] === $pos ? 'selected' : '' ?>><?= e($pos) ?></option><?php endforeach; ?>
        </select></div>
      <p class="alert alert-info">Register using your assigned organization position. Your officer account is created when you submit this form; the President or Vice President does not need to create it for you.</p>
    </div>
    <div class="grid2">
      <div class="field"><label for="password">Password</label><input id="password" name="password" type="password" minlength="8" autocomplete="new-password" required><div class="hint">At least 8 characters.</div></div>
      <div class="field"><label for="password2">Confirm password</label><input id="password2" name="password2" type="password" minlength="8" autocomplete="new-password" required></div>
    </div>
    <button class="btn btn-gold" type="submit">Create Account</button>
  </form>
  <div class="auth-links" style="justify-content:center">Already registered?&nbsp;<a href="<?= e(url('login.php')) ?>">Log in</a></div>
</div>
</div>
<script>
(function(){
 const type=document.getElementById('account_type'), student=document.getElementById('student-fields'), admin=document.getElementById('admin-fields');
 const program=document.getElementById('program'), year=document.getElementById('year_level'), pos=document.getElementById('position');
 const years=['1st Year','2nd Year','3rd Year','4th Year'];
 function fillYears(){
   const levels=JSON.parse(program.dataset.levels||'{}'), selected=year.dataset.selected||year.value, count=levels[program.value]||0;
   year.innerHTML='<option value="">Select year</option>';
   for(let i=0;i<count;i++){const o=document.createElement('option');o.value=years[i];o.textContent=years[i];if(o.value===selected)o.selected=true;year.appendChild(o);}
   year.disabled=count===0;
 }
 function mode(){
   const isAdmin=type.value==='admin';
   student.style.display='block'; admin.style.display=isAdmin?'block':'none';
   program.required=true; year.required=true; pos.required=isAdmin;
   fillYears();
 }
 type.addEventListener('change',mode); program.addEventListener('change',()=>{year.dataset.selected='';fillYears();}); mode();
})();
</script>
<?php html_foot(); ?>
