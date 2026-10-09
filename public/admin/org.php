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

// Two initials: first name + last name (ignores middle names and middle initials).
$orgInitials = function (string $name): string {
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    if (!$parts || $parts[0] === '') return '?';
    $first = mb_substr($parts[0], 0, 1);
    $last  = count($parts) > 1 ? mb_substr($parts[count($parts) - 1], 0, 1) : '';
    return mb_strtoupper($first . $last);
};
$orgNode = function (array $m, bool $small = false) use ($orgInitials): string {
    [$name, $title] = $m;
    return '<div class="oc-node' . ($small ? ' oc-sm' : '') . '" title="' . e($name) . '">'
         . '<span class="oc-circle">' . e($orgInitials($name)) . '</span>'
         . '<b>' . e($name) . '</b><small>' . e($title) . '</small></div>';
};
$ex = ORG_EXECUTIVES; // 0 Pres, 1 VP, 2 Sec, 3 AsstSec, 4 Treas, 5 AsstTreas, 6 Aud, 7 AsstAud

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

<style>
.oc{--oc-navy:var(--navy,#14264f);--oc-gold:var(--gold,#f2b705);--oc-line:#9aa7c2;display:table;margin:0 auto;text-align:center;padding:0 6px}
.oc-root{display:inline-block;background:var(--oc-navy);color:var(--oc-gold);font-weight:700;font-size:12px;letter-spacing:.12em;text-transform:uppercase;padding:8px 22px;border-radius:999px}
.oc-stretch{align-items:stretch!important}
.oc-stretch .oc-branch{display:flex;padding:22px 7px 0}
.oc-stretch .oc-comm{width:178px;max-width:none;min-width:0;height:100%}
.oc-vline.sm{height:12px}
.oc-node{display:flex;flex-direction:column;align-items:center;gap:2px;width:150px;margin:0 auto;font-size:13px;line-height:1.25}
.oc-node b{color:var(--oc-navy);font-size:13px}
.oc-node small{color:var(--muted,#64748b)}
.oc-circle{display:flex;align-items:center;justify-content:center;width:64px;height:64px;border-radius:50%;background:var(--oc-navy);color:var(--oc-gold);font-weight:700;font-size:20px;letter-spacing:.5px;border:3px solid var(--oc-gold);margin-bottom:4px}
.oc-sm .oc-circle{width:48px;height:48px;font-size:15px;border-width:2px}
.oc-sm{width:130px;font-size:12px}.oc-sm b{font-size:12px}
.oc-vline{width:2px;height:22px;background:var(--oc-line);margin:0 auto}
.oc-branches{display:flex;flex-wrap:nowrap;justify-content:center;align-items:flex-start}
.oc-branch{position:relative;padding:22px 14px 0}
.oc-branch::before{content:"";position:absolute;top:0;left:0;right:0;height:2px;background:var(--oc-line)}
.oc-branch:first-child::before{left:50%}.oc-branch:last-child::before{left:0;right:50%}
.oc-branch:only-child::before{display:none}
.oc-branch::after{content:"";position:absolute;top:0;left:50%;width:2px;height:22px;background:var(--oc-line)}
.oc-branch .oc-vline{height:16px}
.oc-comm{background:#f6f8fc;border:1px solid #dbe2f0;border-radius:14px;padding:14px 10px;min-width:170px;max-width:200px}
.oc-comm h4{margin:0 0 12px;font-size:13px;color:var(--oc-navy)}
.oc-comm .oc-node{margin-bottom:14px}.oc-comm .oc-node:last-child{margin-bottom:0}
.oc-scroll{overflow-x:auto;padding-bottom:6px}
.org-tbl{table-layout:fixed;width:100%;min-width:560px}
.org-tbl th:nth-child(1),.org-tbl td:nth-child(1){width:46%}
.org-tbl th:nth-child(2),.org-tbl td:nth-child(2){width:32%}
.org-tbl th:nth-child(3),.org-tbl td:nth-child(3){width:22%}
.org-tbl td{overflow-wrap:anywhere;vertical-align:middle}
.oc-label{margin:26px 0 4px;font-size:12px;letter-spacing:.12em;text-transform:uppercase;color:var(--muted,#64748b)}

/* ---- Light / dark colors for the org chart and list ---- */
.oc-node b,.oc-comm h4,.org-tbl td b{color:var(--os-name,#14264f)}
.oc-node small,.org-tbl th{color:var(--os-muted,#64748b)}
.oc-circle,.oc-root{background:var(--os-circle,#14264f)}
.oc-comm{background:var(--os-comm-bg,#f6f8fc);border-color:var(--os-comm-bd,#dbe2f0)}
.oc-vline,.oc-branch::before,.oc-branch::after{background:var(--os-line,#9aa7c2)}
.org-tbl td{color:var(--os-text,inherit);border-bottom-color:var(--os-row,#eef2f8)}
.org-tbl th{border-bottom-color:var(--os-row-h,#e2e8f0)}
/* dark mode: follows the device setting unless the site picks light/dark itself */
@media (prefers-color-scheme: dark){
  :root:not([data-theme]):not(.light):not(.dark){--os-name:#eef2fb;--os-text:#dbe4f5;--os-muted:#a7b4cf;--os-circle:#1e3a78;--os-comm-bg:rgba(255,255,255,.06);--os-comm-bd:rgba(255,255,255,.16);--os-line:#6b7ba0;--os-row:rgba(255,255,255,.10);--os-row-h:rgba(255,255,255,.22)}
}
:root[data-theme="dark"],html.dark,body.dark,body.dark-mode,[data-theme="dark"]{--os-name:#eef2fb;--os-text:#dbe4f5;--os-muted:#a7b4cf;--os-circle:#1e3a78;--os-comm-bg:rgba(255,255,255,.06);--os-comm-bd:rgba(255,255,255,.16);--os-line:#6b7ba0;--os-row:rgba(255,255,255,.10);--os-row-h:rgba(255,255,255,.22)}
</style>

<section class="card">
  <h2>Organizational structure <small>(A.Y. 2026-2027)</small></h2>
  <div class="oc-scroll"><div class="oc">
    <?= $orgNode($ex[0]) ?>
    <div class="oc-vline"></div>
    <?= $orgNode($ex[1]) ?>
    <div class="oc-vline"></div>
    <div class="oc-branches">
      <?php foreach ([[2, 3], [4, 5], [6, 7]] as [$head, $assist]): ?>
        <div class="oc-branch"><?= $orgNode($ex[$head]) ?><div class="oc-vline"></div><?= $orgNode($ex[$assist]) ?></div>
      <?php endforeach; ?>
    </div>

    <div class="oc-vline" style="margin-top:22px"></div>
    <span class="oc-root">Committees</span>
    <div class="oc-vline"></div>
    <div class="oc-branches oc-stretch">
      <?php foreach (ORG_COMMITTEES as $committee => $members): ?>
        <div class="oc-branch"><div class="oc-comm">
          <h4><?= e($committee) ?></h4>
          <?= implode('<div class="oc-vline sm"></div>', array_map(fn($m) => $orgNode($m, true), $members)) ?>
        </div></div>
      <?php endforeach; ?>
    </div>
  </div></div>
</section>

<section class="card">
  <h2>Organization list <small>(A.Y. 2026-2027)</small></h2>
  <h3 style="margin:6px 0 8px;font-size:14px">Executive Officers</h3>
  <div class="tbl-wrap"><table class="org-tbl">
    <thead><tr><th>Name</th><th>Position</th><th>Program &amp; Year</th></tr></thead>
    <tbody>
    <?php foreach (ORG_EXECUTIVES as $m): ?>
      <tr>
        <td><div class="actions" style="flex-wrap:nowrap"><span class="avatar"><?= e($orgInitials($m[0])) ?></span><b><?= e($m[0]) ?></b></div></td>
        <td><?= e($m[1]) ?></td><td><?= e($m[2]) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?php foreach (ORG_COMMITTEES as $committee => $members): ?>
    <h3 style="margin:22px 0 8px;font-size:14px"><?= e($committee) ?></h3>
    <div class="tbl-wrap"><table class="org-tbl">
      <thead><tr><th>Name</th><th>Position</th><th>Program &amp; Year</th></tr></thead>
      <tbody>
      <?php foreach ($members as $m): ?>
        <tr>
          <td><div class="actions" style="flex-wrap:nowrap"><span class="avatar"><?= e($orgInitials($m[0])) ?></span><b><?= e($m[0]) ?></b></div></td>
          <td><?= e($m[1]) ?></td><td><?= e($m[2]) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  <?php endforeach; ?>
</section>

<section class="card">
  <h2>Officer accounts <small>(<?= count($officers) ?>)</small></h2>
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