<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
$u = require_feature('org');
$canEdit = can_manage('org');

if (is_post()) {
    require_manage('org');
    csrf_check();
    $act = post('action');
    $id = (int)post('id');

    if ($act === 'add') {
        $pos = post('position'); $idn = post('id_number'); $pw = (string)($_POST['password'] ?? '');
        $isStudent = (bool)preg_match('/^\d{4}-\d{5}-LQ-\d$/', $idn);
        $err = [];
        if (mb_strlen(post('full_name')) < 3)                                $err[] = 'Enter the full name.';
        if (!$isStudent)                  $err[] = 'ID must look like 2025-00001-LQ-0.';
        if (!isset(POSITIONS[$pos]))                                         $err[] = 'Choose a position.';
        if (!in_array(post('sex'), ['Female', 'Male', 'Non-Binary'], true))  $err[] = 'Select a sex.';
        if (strlen($pw) < 8)                                                 $err[] = 'Temporary password needs at least 8 characters.';
        if (!$err && q_val('SELECT 1 FROM users WHERE id_number = ?', [$idn])) $err[] = 'That ID number is already registered.';
        if ($err) foreach ($err as $x) flash('error', $x);
        else {
            q('INSERT INTO users (full_name,id_number,sex,program,year_level,password_hash,account_type,position) VALUES (?,?,?,?,?,?,?,?)',
              [post('full_name'), $idn, post('sex'), $isStudent ? (isset(PROGRAMS[post('program')]) ? post('program') : null) : null, null, password_hash($pw, PASSWORD_DEFAULT), 'admin', $pos]);
            flash('success', 'Officer added. Ask them to change the temporary password you set.');
        }
    } elseif ($act === 'position' && isset(POSITIONS[post('position')])) {
        if ($id === $u['id']) flash('error', 'You cannot change your own position.');
        else { q("UPDATE users SET position = ? WHERE id = ? AND account_type = 'admin'", [post('position'), $id]); flash('success', 'Position updated.'); }
    } elseif ($act === 'deactivate') {
        if ($id === $u['id']) flash('error', 'You cannot deactivate your own account.');
        else { q("UPDATE users SET status = 'inactive' WHERE id = ? AND account_type = 'admin'", [$id]); flash('success', 'Officer deactivated.'); }
    }
    redirect('admin/org.php');
}
$officers = q_all("SELECT * FROM users WHERE account_type = 'admin' AND status = 'active' ORDER BY full_name");

dash_start('Organization', 'org');
?>
<?php if (!$canEdit): ?><div class="alert alert-info">Your position (<?= e($u['position']) ?>) can view the organization but not change it.</div><?php endif; ?>

<?php if ($canEdit): ?>
<section class="card">
  <h2>Add an officer</h2>
  <form method="post" class="form-light">
    <?= csrf_field() ?><input type="hidden" name="action" value="add">
    <div class="grid2">
      <div class="field"><label>Full name</label><input name="full_name" required></div>
      <div class="field"><label>ID number</label><input name="id_number" placeholder="2025-00001-LQ-0" required></div>
      <div class="field"><label>Position</label><select name="position" required><option value="">Select</option><?php foreach (array_keys(POSITIONS) as $p): ?><option><?= e($p) ?></option><?php endforeach; ?></select></div>
      <div class="field"><label>Sex</label><select name="sex" required><option value="">Select</option><option>Female</option><option>Male</option><option>Non-Binary</option></select></div>
      <div class="field"><label>Program <small>(students)</small></label><select name="program"><option value="">—</option><?php foreach (PROGRAMS as $p => $_): ?><option><?= e($p) ?></option><?php endforeach; ?></select></div>
      <div class="field"><label>Temporary password</label><input name="password" type="password" minlength="8" autocomplete="new-password" required></div>
    </div>
    <button class="btn btn-navy" type="submit">Add officer</button>
  </form>
</section>
<?php endif; ?>

<section class="card">
  <h2>Officers <small>(<?= count($officers) ?>)</small></h2>
  <div class="tbl-wrap"><table>
    <thead><tr><th>Name</th><th>Position</th><th>Program</th><?php if ($canEdit): ?><th></th><?php endif; ?></tr></thead>
    <tbody>
    <?php foreach ($officers as $o): $self = (int)$o['id'] === $u['id']; ?>
      <tr>
        <td><div class="actions" style="flex-wrap:nowrap"><span class="avatar"><?= e(initials($o['full_name'])) ?></span><div><b><?= e($o['full_name']) ?></b><small><?= e($o['id_number']) ?></small></div></div></td>
        <td><?php if ($canEdit && !$self): ?>
          <form method="post" class="form-light actions"><?= csrf_field() ?><input type="hidden" name="action" value="position"><input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
            <select name="position" aria-label="Position" style="padding:6px 10px;border-radius:10px;border:1px solid #cdd5e4;font:inherit"><?php foreach (array_keys(POSITIONS) as $p): ?><option <?= $o['position'] === $p ? 'selected' : '' ?>><?= e($p) ?></option><?php endforeach; ?></select>
            <button class="btn btn-line btn-sm">Save</button></form>
          <?php else: ?><span class="badge badge-approved"><?= e($o['position']) ?></span><?= $self ? ' <small>(you)</small>' : '' ?><?php endif; ?></td>
        <td><?= e(trim(($o['program'] ?? '—') . ' ' . ($o['year_level'] ?? ''))) ?></td>
        <?php if ($canEdit): ?><td><?php if (!$self): ?><form method="post" class="inline" onsubmit="return confirm('Deactivate this officer? They will no longer be able to log in.')"><?= csrf_field() ?><input type="hidden" name="action" value="deactivate"><input type="hidden" name="id" value="<?= (int)$o['id'] ?>"><button class="btn btn-danger btn-sm">Deactivate</button></form><?php endif; ?></td><?php endif; ?>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</section>
<?php dash_end(); ?>
