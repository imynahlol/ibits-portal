<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

$u       = require_feature('org');
$canEdit = can_manage('org');

if (is_post()) {
    require_manage('org');
    csrf_check();

    $act = post('action');
    $id  = (int)post('id');

    if ($act === 'add') {
        $pos       = post('position');
        $idn       = post('id_number');
        $pw        = (string)($_POST['password'] ?? '');
        $isStudent = (bool)preg_match('/^\d{4}-\d{5}-LQ-\d$/', $idn);
        $err       = [];

        if (mb_strlen(post('full_name')) < 3) {
            $err[] = 'Enter the full name.';
        }
        if (!$isStudent) {
            $err[] = 'ID must look like 2025-00001-LQ-0.';
        }
        if (!isset(POSITIONS[$pos])) {
            $err[] = 'Choose a position.';
        }
        if (!in_array(post('sex'), ['Female', 'Male', 'Non-Binary'], true)) {
            $err[] = 'Select a sex.';
        }
        if (strlen($pw) < 8) {
            $err[] = 'Temporary password needs at least 8 characters.';
        }
        if (!$err && q_val('SELECT 1 FROM users WHERE id_number = ?', [$idn])) {
            $err[] = 'That ID number is already registered.';
        }

        if ($err) {
            foreach ($err as $x) {
                flash('error', $x);
            }
        } else {
            q(
                'INSERT INTO users (full_name,id_number,sex,program,year_level,password_hash,account_type,position) VALUES (?,?,?,?,?,?,?,?)',
                [
                    post('full_name'),
                    $idn,
                    post('sex'),
                    $isStudent ? (isset(PROGRAMS[post('program')]) ? post('program') : null) : null,
                    null,
                    password_hash($pw, PASSWORD_DEFAULT),
                    'admin',
                    $pos,
                ]
            );
            flash('success', 'Officer added. Ask them to change the temporary password you set.');
        }
    } elseif ($act === 'position' && isset(POSITIONS[post('position')])) {
        if ($id === $u['id']) {
            flash('error', 'You cannot change your own position.');
        } else {
            q(
                "UPDATE users SET position = ? WHERE id = ? AND account_type = 'admin'",
                [post('position'), $id]
            );
            flash('success', 'Position updated.');
        }
    } elseif ($act === 'deactivate') {
        if ($id === $u['id']) {
            flash('error', 'You cannot deactivate your own account.');
        } else {
            q(
                "UPDATE users SET status = 'inactive' WHERE id = ? AND account_type = 'admin'",
                [$id]
            );
            flash('success', 'Officer deactivated.');
        }
    }

    redirect('admin/org.php');
}

$officers = q_all(
    "SELECT * FROM users WHERE account_type = 'admin' AND status = 'active' ORDER BY full_name"
);

// Two initials: first name + last name (ignores middle names and middle initials).
$orgInitials = function (string $name): string {
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    if (!$parts || $parts[0] === '') {
        return '?';
    }

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

<?php if (!$canEdit): ?>
    <div class="alert alert-info">
        Your position (<?= e($u['position']) ?>) can view the organization but not change it.
    </div>
<?php endif; ?>

<?php if ($canEdit): ?>
    <section class="card">
        <h2>Add an officer</h2>

        <form method="post" class="form-light">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="add">

            <div class="grid2">
                <div class="field">
                    <label>Full name</label>
                    <input name="full_name" required>
                </div>

                <div class="field">
                    <label>ID number</label>
                    <input name="id_number" placeholder="2025-00001-LQ-0" required>
                </div>

                <div class="field">
                    <label>Position</label>
                    <select name="position" required>
                        <option value="">Select</option>
                        <?php foreach (array_keys(POSITIONS) as $p): ?>
                            <option><?= e($p) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label>Sex</label>
                    <select name="sex" required>
                        <option value="">Select</option>
                        <option>Female</option>
                        <option>Male</option>
                        <option>Non-Binary</option>
                    </select>
                </div>

                <div class="field">
                    <label>Program <small>(students)</small></label>
                    <select name="program">
                        <option value="">—</option>
                        <?php foreach (PROGRAMS as $p => $_): ?>
                            <option><?= e($p) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label>Temporary password</label>
                    <input name="password" type="password" minlength="8"
                           autocomplete="new-password" required>
                </div>
            </div>

            <button class="btn btn-navy" type="submit">Add officer</button>
        </form>
    </section>
<?php endif; ?>

<style>
  /* ================= ORG CHART + LIST (same look as the public Org Structure page) =================
     These cards are self-contained dark glass panels, so they look identical
     whether the dashboard theme is light or dark. */
  section.os-card{
    background:linear-gradient(160deg,#0b1a3d 0%,#06122c 100%);
    border:1px solid rgba(255,255,255,.12);border-radius:24px;padding:30px 26px;color:#c3d0ec;
    box-shadow:0 24px 50px -18px rgba(0,0,0,.7),0 3px 0 rgba(250,213,23,.3);
  }
  section.os-card > h2{display:flex;align-items:center;gap:14px;margin:0 0 26px;font-size:22px;font-weight:700;color:#fff}
  section.os-card > h2::before{content:"";flex:none;width:6px;height:28px;border-radius:4px;background:#fad517}
  section.os-card > h2 small{font-weight:500;font-size:14px;color:#a9b8d6}

  /* ---- org chart ---- */
  .oc-scroll{overflow-x:auto;padding:4px 0 10px}
  .oc{--line:rgba(250,213,23,.45);display:table;margin:0 auto;text-align:center;padding:0 6px}
  .oc-root{display:inline-block;background:#fad517;color:#06215c;font-weight:800;font-size:12px;letter-spacing:.14em;text-transform:uppercase;padding:9px 24px;border-radius:999px;box-shadow:0 4px 0 #bda000}
  .oc-vline{width:2px;height:22px;background:var(--line);margin:0 auto}
  .oc-vline.sm{height:12px}

  .oc-node{display:flex;flex-direction:column;align-items:center;gap:2px;width:160px;margin:0 auto;font-size:13px;line-height:1.3}
  .oc-node b{color:#fff;font-size:14px;font-weight:600}
  .oc-node small{color:#a9b8d6;font-size:12px}
  .oc-circle{display:flex;align-items:center;justify-content:center;width:68px;height:68px;border-radius:50%;
             background:linear-gradient(145deg,#0a328c,#06215c);color:#fad517;font-weight:700;font-size:21px;letter-spacing:.5px;
             border:3px solid #fad517;margin-bottom:6px;box-shadow:0 8px 20px rgba(0,0,0,.45),0 0 0 4px rgba(250,213,23,.12);transition:transform .25s}
  .oc-node:hover .oc-circle{transform:translateY(-3px) scale(1.05)}
  .oc-sm{width:136px}
  .oc-sm .oc-circle{width:50px;height:50px;font-size:16px;border-width:2px}
  .oc-sm b{font-size:13px}
  .oc-sm small{font-size:11.5px}

  .oc-branches{display:flex;flex-wrap:nowrap;justify-content:center;align-items:flex-start}
  .oc-branch{position:relative;padding:22px 16px 0}
  .oc-branch::before{content:"";position:absolute;top:0;left:0;right:0;height:2px;background:var(--line)}
  .oc-branch:first-child::before{left:50%}
  .oc-branch:last-child::before{left:0;right:50%}
  .oc-branch:only-child::before{display:none}
  .oc-branch::after{content:"";position:absolute;top:0;left:50%;width:2px;height:22px;background:var(--line)}
  .oc-branch .oc-vline{height:16px}

  .oc-stretch{align-items:stretch}
  .oc-stretch .oc-branch{display:flex;padding:22px 8px 0}
  .oc-comm{width:184px;max-width:none;min-width:0;height:100%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.14);border-radius:18px;padding:16px 10px;
           box-shadow:0 3px 0 rgba(250,213,23,.25);transition:border-color .25s,transform .25s}
  .oc-comm:hover{border-color:rgba(250,213,23,.5);transform:translateY(-3px)}
  .oc-comm h4{margin:0 0 14px;font-size:13px;font-weight:700;color:#fad517;letter-spacing:.04em}
  .oc-comm .oc-node{margin-bottom:14px}
  .oc-comm .oc-node:last-child{margin-bottom:0}

  /* ---- list tables ---- */
  .os-group{display:flex;align-items:center;gap:12px;margin:30px 0 12px;font-size:16px;font-weight:700;color:#fff}
  .os-group.first{margin-top:0}
  .os-group .num{flex:none;min-width:34px;height:34px;padding:0 8px;border-radius:11px;display:flex;align-items:center;justify-content:center;background:rgba(250,213,23,.16);color:#fad517;font-size:13px;font-weight:700}
  .os-card .tbl-wrap{overflow-x:auto;border-radius:16px;border:1px solid rgba(255,255,255,.1);background:rgba(2,10,28,.35)}
  .os-card .org-tbl{table-layout:fixed;width:100%;min-width:560px;border-collapse:collapse;background:transparent}
  .os-card .org-tbl th,.os-card .org-tbl td{text-align:left;padding:13px 16px}
  .os-card .org-tbl th{font-size:12px;text-transform:uppercase;letter-spacing:.1em;color:#fad517;background:rgba(250,213,23,.08);border-bottom:1px solid rgba(250,213,23,.25)}
  .os-card .org-tbl th:nth-child(1),.os-card .org-tbl td:nth-child(1){width:46%}
  .os-card .org-tbl th:nth-child(2),.os-card .org-tbl td:nth-child(2){width:32%}
  .os-card .org-tbl th:nth-child(3),.os-card .org-tbl td:nth-child(3){width:22%}
  .os-card .org-tbl td{color:#c3d0ec;font-size:15px;background:transparent;border-bottom:1px solid rgba(255,255,255,.08);overflow-wrap:anywhere;vertical-align:middle}
  .os-card .org-tbl tbody tr:last-child td{border-bottom:0}
  .os-card .org-tbl tbody tr{transition:background .2s}
  .os-card .org-tbl tbody tr:hover td{background:rgba(250,213,23,.07)}
  .org-person{display:flex;align-items:center;gap:12px;flex-wrap:nowrap}
  .org-person b{color:#fff;font-weight:600}
  .org-avatar{flex:none;display:flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:50%;
              background:linear-gradient(145deg,#0a328c,#06215c);color:#fad517;border:2px solid #fad517;font-weight:700;font-size:12px}

  @media(max-width:860px){
    section.os-card{padding:22px 16px}
    section.os-card > h2{font-size:19px}
  }
</style>

<section class="card os-card">
    <h2>Organizational structure <small>(A.Y. 2026-2027)</small></h2>

    <div class="oc-scroll">
        <div class="oc">
            <?= $orgNode($ex[0]) ?>
            <div class="oc-vline"></div>
            <?= $orgNode($ex[1]) ?>
            <div class="oc-vline"></div>

            <div class="oc-branches">
                <?php foreach ([[2, 3], [4, 5], [6, 7]] as [$head, $assist]): ?>
                    <div class="oc-branch">
                        <?= $orgNode($ex[$head]) ?>
                        <div class="oc-vline"></div>
                        <?= $orgNode($ex[$assist]) ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="oc-vline" style="margin-top:22px"></div>
            <span class="oc-root">Committees</span>
            <div class="oc-vline"></div>

            <div class="oc-branches oc-stretch">
                <?php foreach (ORG_COMMITTEES as $committee => $members): ?>
                    <div class="oc-branch">
                        <div class="oc-comm">
                            <h4><?= e($committee) ?></h4>
                            <?= implode(
                                '<div class="oc-vline sm"></div>',
                                array_map(fn($m) => $orgNode($m, true), $members)
                            ) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="card os-card">
    <h2>Organization list <small>(A.Y. 2026-2027)</small></h2>

    <h3 class="os-group first">
        <span class="num">EX</span>Executive Officers
    </h3>

    <div class="tbl-wrap">
        <table class="org-tbl">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Program &amp; Year</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (ORG_EXECUTIVES as $m): ?>
                    <tr>
                        <td>
                            <div class="org-person">
                                <span class="org-avatar"><?= e($orgInitials($m[0])) ?></span>
                                <b><?= e($m[0]) ?></b>
                            </div>
                        </td>
                        <td><?= e($m[1]) ?></td>
                        <td><?= e($m[2]) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php $ci = 0; ?>
    <?php foreach (ORG_COMMITTEES as $committee => $members): ?>
        <?php $ci++; ?>
        <h3 class="os-group">
            <span class="num"><?= str_pad((string)$ci, 2, '0', STR_PAD_LEFT) ?></span><?= e($committee) ?>
        </h3>

        <div class="tbl-wrap">
            <table class="org-tbl">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Program &amp; Year</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $m): ?>
                        <tr>
                            <td>
                                <div class="org-person">
                                    <span class="org-avatar"><?= e($orgInitials($m[0])) ?></span>
                                    <b><?= e($m[0]) ?></b>
                                </div>
                            </td>
                            <td><?= e($m[1]) ?></td>
                            <td><?= e($m[2]) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>
</section>

<section class="card">
    <h2>Officer accounts <small>(<?= count($officers) ?>)</small></h2>

    <div class="tbl-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Program</th>
                    <?php if ($canEdit): ?><th></th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($officers as $o): ?>
                    <?php $self = (int)$o['id'] === $u['id']; ?>
                    <tr>
                        <td>
                            <div class="actions" style="flex-wrap:nowrap">
                                <span class="avatar"><?= e(initials($o['full_name'])) ?></span>
                                <div>
                                    <b><?= e($o['full_name']) ?></b>
                                    <small><?= e($o['id_number']) ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if ($canEdit && !$self): ?>
                                <form method="post" class="form-light actions">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="position">
                                    <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                                    <select name="position" aria-label="Position"
                                            style="padding:6px 10px;border-radius:10px;border:1px solid #cdd5e4;font:inherit">
                                        <?php foreach (array_keys(POSITIONS) as $p): ?>
                                            <option <?= $o['position'] === $p ? 'selected' : '' ?>><?= e($p) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-line btn-sm">Save</button>
                                </form>
                            <?php else: ?>
                                <span class="badge badge-approved"><?= e($o['position']) ?></span><?= $self ? ' <small>(you)</small>' : '' ?>
                            <?php endif; ?>
                        </td>
                        <td><?= e(trim(($o['program'] ?? '—') . ' ' . ($o['year_level'] ?? ''))) ?></td>
                        <?php if ($canEdit): ?>
                            <td>
                                <?php if (!$self): ?>
                                    <form method="post" class="inline"
                                          onsubmit="return confirm('Deactivate this officer? They will no longer be able to log in.')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="deactivate">
                                        <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                                        <button class="btn btn-danger btn-sm">Deactivate</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<?php dash_end(); ?>