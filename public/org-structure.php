<?php

declare(strict_types=1);
// Public, read-only page: no login, no position checks, no forms, no database writes.
require_once dirname(__DIR__) . '/app/bootstrap.php';

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Org Structure | iBITS PUPLC</title>
<?php /* If your other public pages share a header/head include, replace this <head> and the opening <body> with it. */ ?>
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
.org-tbl{table-layout:fixed;width:100%;min-width:560px;border-collapse:collapse}
.org-tbl th,.org-tbl td{text-align:left;padding:10px 12px;border-bottom:1px solid #eef2f8}
.org-tbl th{font-size:12px;text-transform:uppercase;letter-spacing:.06em}
.org-tbl th:nth-child(1),.org-tbl td:nth-child(1){width:46%}
.org-tbl th:nth-child(2),.org-tbl td:nth-child(2){width:32%}
.org-tbl th:nth-child(3),.org-tbl td:nth-child(3){width:22%}
.org-tbl td{overflow-wrap:anywhere;vertical-align:middle}
.org-person{display:flex;align-items:center;gap:10px;flex-wrap:nowrap}
.org-avatar{flex:none;display:flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:50%;background:#14264f;color:#f2b705;font-weight:700;font-size:12px}
.org-card{max-width:1100px;margin:24px auto;padding:22px;border-radius:16px}
.org-card h2{margin:0 0 16px}.org-card h2 small{font-weight:400;opacity:.7}
.tbl-wrap{overflow-x:auto}

/* ---- Dark mode (always on) ---- */
:root{
  color-scheme:dark;
  --os-name:#eef2fb;
  --os-text:#dbe4f5;
  --os-muted:#a7b4cf;
  --os-circle:#1e3a78;
  --os-comm-bg:rgba(255,255,255,.06);
  --os-comm-bd:rgba(255,255,255,.16);
  --os-line:#6b7ba0;
  --os-row:rgba(255,255,255,.10);
  --os-row-h:rgba(255,255,255,.22);
  --os-page:#0b1220;
  --os-card:#111b30;
  --os-card-bd:rgba(255,255,255,.10);
}
html,body{background:var(--os-page);color:var(--os-text)}
section.org-card{background:var(--os-card);border:1px solid var(--os-card-bd);color:var(--os-text);box-shadow:0 8px 24px rgba(0,0,0,.35)}
.org-card h2,.org-card h3{color:var(--os-name)}

.oc-node b,.oc-comm h4,.org-tbl td b{color:var(--os-name)}
.oc-node small,.org-tbl th{color:var(--os-muted)}
.oc-circle,.oc-root,.org-avatar{background:var(--os-circle)}
.oc-comm{background:var(--os-comm-bg);border-color:var(--os-comm-bd)}
.oc-vline,.oc-branch::before,.oc-branch::after{background:var(--os-line)}
.org-tbl td{color:var(--os-text);border-bottom-color:var(--os-row)}
.org-tbl th{border-bottom-color:var(--os-row-h)}
</style>
</head>
<body>

<section class="card org-card">
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

<section class="card org-card">
  <h2>Organization list <small>(A.Y. 2026-2027)</small></h2>
  <h3 style="margin:6px 0 8px;font-size:14px">Executive Officers</h3>
  <div class="tbl-wrap"><table class="org-tbl">
    <thead><tr><th>Name</th><th>Position</th><th>Program &amp; Year</th></tr></thead>
    <tbody>
    <?php foreach (ORG_EXECUTIVES as $m): ?>
      <tr>
        <td><div class="org-person"><span class="org-avatar"><?= e($orgInitials($m[0])) ?></span><b><?= e($m[0]) ?></b></div></td>
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
          <td><div class="org-person"><span class="org-avatar"><?= e($orgInitials($m[0])) ?></span><b><?= e($m[0]) ?></b></div></td>
          <td><?= e($m[1]) ?></td><td><?= e($m[2]) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  <?php endforeach; ?>
</section>

</body>
</html>