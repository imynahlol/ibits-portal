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

// Same shared header/footer as index.php and faqs.php.
html_head('Org Structure', 'org');
?>

<style>
  /* ================= ORG STRUCTURE (scoped; same look as FAQs) ================= */
  .os-sec{padding-top:150px;padding-bottom:90px}
  .os-wrap{max-width:1150px;margin:0 auto;position:relative;z-index:2}

  .os-badge{display:flex;width:max-content;margin:0 auto 22px;align-items:center;gap:10px;padding:9px 22px;border-radius:999px;border:1px solid rgba(255,255,255,.18);background:rgba(8,26,69,.55);font-size:13px;font-weight:700;letter-spacing:.2em;color:#fad517}
  .os-badge i{width:9px;height:9px;border-radius:50%;background:#fad517;box-shadow:0 0 10px #fad517}
  .os-title{text-align:center;font-weight:800;line-height:1.1;margin-bottom:14px;font-size:clamp(34px,6vw,64px);color:#fff}
  .os-title span{color:#fad517}
  .os-sub{text-align:center;color:#a9b8d6;max-width:560px;margin:0 auto 46px;font-size:15px;line-height:1.7}

  /* ---- glass cards ---- */
  .os-card{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:24px;padding:30px 26px;margin-bottom:28px;
           box-shadow:0 24px 50px -18px rgba(0,0,0,.7),0 3px 0 rgba(250,213,23,.25)}
  .os-card > h2{display:flex;align-items:center;gap:14px;margin:0 0 26px;font-size:24px;font-weight:700;color:#fff}
  .os-card > h2::before{content:"";flex:none;width:6px;height:28px;border-radius:4px;background:#fad517}
  .os-card > h2 small{font-weight:500;font-size:14px;color:#a9b8d6}

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
  .oc-sm b{font-size:13px}.oc-sm small{font-size:11.5px}

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
  .oc-comm{width:184px;height:100%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.14);border-radius:18px;padding:16px 10px;
           box-shadow:0 3px 0 rgba(250,213,23,.25);transition:border-color .25s,transform .25s}
  .oc-comm:hover{border-color:rgba(250,213,23,.5);transform:translateY(-3px)}
  .oc-comm h4{margin:0 0 14px;font-size:13px;font-weight:700;color:#fad517;letter-spacing:.04em}
  .oc-comm .oc-node{margin-bottom:14px}
  .oc-comm .oc-node:last-child{margin-bottom:0}

  /* ---- list tables ---- */
  .os-group{display:flex;align-items:center;gap:12px;margin:30px 0 12px;font-size:16px;font-weight:700;color:#fff}
  .os-group:first-of-type{margin-top:0}
  .os-group .num{flex:none;min-width:34px;height:34px;padding:0 8px;border-radius:11px;display:flex;align-items:center;justify-content:center;background:rgba(250,213,23,.16);color:#fad517;font-size:13px;font-weight:700}
  .tbl-wrap{overflow-x:auto;border-radius:16px;border:1px solid rgba(255,255,255,.1);background:rgba(2,10,28,.35)}
  .org-tbl{table-layout:fixed;width:100%;min-width:560px;border-collapse:collapse}
  .org-tbl th,.org-tbl td{text-align:left;padding:13px 16px}
  .org-tbl th{font-size:12px;text-transform:uppercase;letter-spacing:.1em;color:#fad517;background:rgba(250,213,23,.08);border-bottom:1px solid rgba(250,213,23,.25)}
  .org-tbl th:nth-child(1),.org-tbl td:nth-child(1){width:46%}
  .org-tbl th:nth-child(2),.org-tbl td:nth-child(2){width:32%}
  .org-tbl th:nth-child(3),.org-tbl td:nth-child(3){width:22%}
  .org-tbl td{color:#c3d0ec;font-size:15px;border-bottom:1px solid rgba(255,255,255,.08);overflow-wrap:anywhere;vertical-align:middle}
  .org-tbl tbody tr:last-child td{border-bottom:0}
  .org-tbl tbody tr{transition:background .2s}
  .org-tbl tbody tr:hover{background:rgba(250,213,23,.07)}
  .org-person{display:flex;align-items:center;gap:12px;flex-wrap:nowrap}
  .org-person b{color:#fff;font-weight:600}
  .org-avatar{flex:none;display:flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:50%;
              background:linear-gradient(145deg,#0a328c,#06215c);color:#fad517;border:2px solid #fad517;font-weight:700;font-size:12px}

  @media(max-width:860px){
    .os-card{padding:22px 16px}
    .os-card > h2{font-size:20px}
  }
</style>

<main>
  <section class="sec sec-dark os-sec" id="org-structure">
    <canvas class="rain" data-rain="dark" aria-hidden="true"></canvas>
    <div class="container os-wrap">

      <span class="os-badge"><i></i>OUR TEAM</span>
      <h1 class="os-title">Organizational <span>Structure</span></h1>
      <p class="os-sub">Meet the officers and committees who keep iBITS running for A.Y. 2026-2027.</p>

      <!-- ============ ORG CHART ============ -->
      <section class="os-card">
        <h2>Organizational chart <small>(A.Y. 2026-2027)</small></h2>
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

      <!-- ============ ORG LIST ============ -->
      <section class="os-card">
        <h2>Organization list <small>(A.Y. 2026-2027)</small></h2>

        <h3 class="os-group"><span class="num">EX</span>Executive Officers</h3>
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

        <?php $ci = 0; foreach (ORG_COMMITTEES as $committee => $members): $ci++; ?>
          <h3 class="os-group"><span class="num"><?= str_pad((string)$ci, 2, '0', STR_PAD_LEFT) ?></span><?= e($committee) ?></h3>
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

    </div>
  </section>
</main>

<?php public_footer(); html_foot(); ?>