<?php
declare(strict_types=1);

/* ===== iBITS export kit: shared by fines.php and finance.php =====
   Set this to wherever your logo lives (relative to public/). */
const IBITS_LOGO_PATH = 'assets/img/ibits-logo.png';

const IBITS_ORG    = 'Institute of Brilliant Information Technology Students';
const IBITS_SCHOOL = 'Polytechnic University of the Philippines, Lopez Quezon Campus';

function ibits_signatories(): array {
    return [
        ['name' => 'Irish Nicole J. Japone', 'position' => 'iBITS, Treasurer'],
        ['name' => 'Elyza Irene B. Ventura', 'position' => 'iBITS, General Secretary'],
        ['name' => 'Allea Grace C. Ranillo', 'position' => 'iBITS, President'],
    ];
}

if (!function_exists('ibits_position_in')) {
    function ibits_position_in(array $u, array $allowed): bool {
        foreach (['position', 'position_title', 'officer_position', 'role', 'title'] as $k) {
            if (isset($u[$k]) && in_array(strtolower(trim((string)$u[$k])), $allowed, true)) return true;
        }
        return false;
    }
}

/* Builds the config used by Print / PDF (JS side). */
function ibits_export_cfg(array $u, string $title, string $file, string $orientation, array $head, array $rows, array $totals): array {
    return [
        'title' => $title, 'filename' => $file, 'orientation' => $orientation,
        'printedBy' => (string)($u['full_name'] ?? ''), 'generated' => date('F j, Y g:i A'),
        'org' => IBITS_ORG, 'school' => IBITS_SCHOOL,
        'logo' => url(IBITS_LOGO_PATH), 'signatories' => ibits_signatories(),
        'head' => $head, 'rows' => $rows, 'totals' => $totals,
    ];
}

/* Excel (HTML-based .xls) with the same header / footer / signatories. */
function ibits_export_xls(string $title, array $head, array $rows, array $totals, string $file): void {
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $file . '"');
    header('Cache-Control: max-age=0');
    $h = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
    $n = count($head);
    $navy = '#06215c'; $gold = '#fad51b';
    $band = fn(string $inner, string $style = '') => '<tr><td colspan="' . $n . '" style="background:' . $navy . ';color:#ffffff;' . $style . '">' . $inner . '</td></tr>';

    echo "\xEF\xBB\xBF<html><head><meta charset=\"utf-8\"></head><body><table border=\"0\" style=\"font-family:Calibri,Arial\">";

    /* Header */
    echo $band('<b style="font-size:15pt">' . $h(IBITS_ORG) . '</b>');
    echo $band($h(IBITS_SCHOOL));
    echo '<tr><td colspan="' . $n . '" style="background:' . $gold . ';height:6px"></td></tr>';
    echo '<tr><td colspan="' . $n . '" style="font-size:16pt;font-weight:bold;color:' . $navy . '">' . $h($title) . '</td></tr>';
    echo '<tr><td colspan="' . $n . '" style="color:#555555">Generated ' . date('F j, Y g:i A') . '</td></tr><tr><td colspan="' . $n . '"></td></tr>';

    /* Table */
    echo '<tr>';
    foreach ($head as $c) echo '<th style="background:' . $navy . ';color:#ffffff;border:1px solid #06215c">' . $h($c['label']) . '</th>';
    echo '</tr>';
    $i2 = 0;
    foreach ($rows as $r) {
        $bg = ($i2++ % 2) ? '#f5f6fa' : '#ffffff';
        echo '<tr>';
        foreach ($r as $i => $v) {
            echo !empty($head[$i]['money'])
                ? '<td style="border:1px solid #ccd;background:' . $bg . ';mso-number-format:\'#,##0.00\'">' . number_format((float)$v, 2, '.', '') . '</td>'
                : '<td style="border:1px solid #ccd;background:' . $bg . ';mso-number-format:\'\@\'">' . $h($v) . '</td>';
        }
        echo '</tr>';
    }
    echo '<tr><td colspan="' . $n . '"></td></tr>';

    /* Totals */
    foreach ($totals as $t) {
        echo '<tr><td colspan="2" style="background:#f5f6fa;border:1px solid #ccd"><b>' . $h($t['label']) . '</b></td>'
           . '<td style="background:#f5f6fa;border:1px solid #ccd;' . (!empty($t['money']) ? 'mso-number-format:\'#,##0.00\'' : '') . '"><b>'
           . (!empty($t['money']) ? number_format((float)$t['value'], 2, '.', '') : $h($t['value'])) . '</b></td></tr>';
    }

    /* Signatories: blank signature space, then name and position */
    for ($k = 0; $k < 3; $k++) echo '<tr><td colspan="' . $n . '" style="height:22px"></td></tr>';
    $base = intdiv($n, 3); $rem = $n % 3;
    $sizes = [$base + ($rem > 0 ? 1 : 0), $base + ($rem > 1 ? 1 : 0), $base];
    $sigs = ibits_signatories();
    echo '<tr>';
    foreach ($sigs as $i => $s) echo '<td colspan="' . max(1, $sizes[$i]) . '" style="border-top:1px solid #000000;text-align:center"><b>' . $h($s['name']) . '</b></td>';
    echo '</tr><tr>';
    foreach ($sigs as $i => $s) echo '<td colspan="' . max(1, $sizes[$i]) . '" style="text-align:center">' . $h($s['position']) . '</td>';
    echo '</tr><tr><td colspan="' . $n . '"></td></tr>';

    /* Footer */
    echo '<tr><td colspan="' . $n . '" style="background:' . $gold . ';height:4px"></td></tr>';
    echo $band('iBITS Portal | System-generated record, ' . date('F j, Y'), 'font-size:9pt');
    echo '</table></body></html>';
    exit;
}

/* Export bar (buttons) + all the print / PDF JS. */
function ibits_export_ui(array $cfg, string $ariaLabel, string $xlsUrl, bool $hasRows): void {
    $json = json_encode($cfg, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
    $dis = $hasRows ? '' : 'disabled';
    ?>
<style>
.export-bar{display:flex;flex-wrap:wrap;align-items:center;gap:10px;margin:0 0 18px;padding:0 4px;animation:ex-in .45s ease both}
.export-bar .export-label{font-size:.78rem;letter-spacing:.08em;text-transform:uppercase;opacity:.6;margin-right:4px}
.btn-export{display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:999px;border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.05);color:inherit;font:inherit;font-size:.9rem;font-weight:600;text-decoration:none;cursor:pointer;position:relative;overflow:hidden;transition:transform .2s ease,background .2s ease,color .2s ease,border-color .2s ease,box-shadow .2s ease;animation:ex-in .45s ease both}
.btn-export:nth-of-type(1){animation-delay:.05s}.btn-export:nth-of-type(2){animation-delay:.12s}.btn-export:nth-of-type(3){animation-delay:.19s}
.btn-export svg{width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;flex:none}
.btn-export:hover,.btn-export:focus-visible{transform:translateY(-2px);background:#fad51b;color:#06215c;border-color:#fad51b;box-shadow:0 8px 18px rgba(250,213,27,.25);outline:0}
.btn-export:active{transform:translateY(0) scale(.97)}
.btn-export[data-act="print"]:hover svg{animation:ex-pop .5s ease}
.btn-export[data-act="pdf"]:hover .ex-arrow{animation:ex-down .8s ease infinite}
.btn-export[data-act="xls"]:hover svg{animation:ex-wiggle .6s ease}
.btn-export[disabled]{opacity:.45;pointer-events:none}
.btn-export.is-loading{pointer-events:none;opacity:.85}
.btn-export.is-loading svg{animation:ex-spin .8s linear infinite}
@keyframes ex-in{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
@keyframes ex-pop{0%,100%{transform:scale(1)}40%{transform:scale(1.25) rotate(-6deg)}70%{transform:scale(.95)}}
@keyframes ex-down{0%,100%{transform:translateY(0)}50%{transform:translateY(3px)}}
@keyframes ex-wiggle{0%,100%{transform:rotate(0)}25%{transform:rotate(-12deg)}75%{transform:rotate(12deg)}}
@keyframes ex-spin{to{transform:rotate(360deg)}}
@media (prefers-reduced-motion:reduce){.export-bar,.btn-export,.btn-export *{animation:none!important;transition:none!important}}
</style>
<div class="export-bar" role="toolbar" aria-label="<?= e($ariaLabel) ?>">
  <span class="export-label">Export</span>
  <button type="button" class="btn-export" data-act="print" <?= $dis ?>>
    <svg viewBox="0 0 24 24"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg><span>Print</span>
  </button>
  <button type="button" class="btn-export" data-act="pdf" <?= $dis ?>>
    <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><g class="ex-arrow"><path d="M12 12v6"/><path d="m9 15 3 3 3-3"/></g></svg><span>Download PDF</span>
  </button>
  <a class="btn-export" data-act="xls" href="<?= e($xlsUrl) ?>" <?= $hasRows ? '' : 'disabled aria-disabled="true"' ?>>
    <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18M15 3v18"/></svg><span>Download Excel</span>
  </a>
</div>
<script>
(function () {
  var C = <?= $json ?>;
  var bar = document.querySelector('.export-bar');
  if (!bar || !C) return;
  var NAVY = [6, 33, 92], GOLD = [250, 213, 23];

  function money(v, s) { return s + Number(v).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}); }
  function esc(s) { return String(s == null ? '' : s).replace(/[&<>"]/g, function (c) { return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]; }); }
  function cell(v, h, sym) { return h.money ? money(v, sym) : String(v == null ? '' : v); }
  function busy(btn, ms) { btn.classList.add('is-loading'); setTimeout(function () { btn.classList.remove('is-loading'); }, ms); }
  function absLogo() { return new URL(C.logo, location.href).href; }

  /* ---------------- PRINT ---------------- */
  function doPrint(btn) {
    busy(btn, 1100);
    var th = C.head.map(function (h) { return '<th class="' + (h.money ? 'r' : '') + '">' + esc(h.label) + '</th>'; }).join('');
    var tb = C.rows.map(function (r) {
      return '<tr>' + r.map(function (v, i) { return '<td class="' + (C.head[i].money ? 'r' : '') + '">' + esc(cell(v, C.head[i], '\u20B1')) + '</td>'; }).join('') + '</tr>';
    }).join('');
    var stats = C.totals.map(function (t) { return '<div><small>' + esc(t.label) + '</small><b>' + esc(t.money ? money(t.value, '\u20B1') : t.value) + '</b></div>'; }).join('');
    var sigs = C.signatories.map(function (s) {
      return '<div class="sig"><div class="sline"></div><b>' + esc(s.name) + '</b><span>' + esc(s.position) + '</span></div>';
    }).join('');

    var css =
      '@page{size:A4 ' + C.orientation + ';margin:12mm 12mm 16mm;@bottom-right{content:"Page " counter(page) " of " counter(pages);font:9px Arial,sans-serif;color:#555}}' +
      '*{box-sizing:border-box}body{margin:0;font-family:Poppins,Arial,Helvetica,sans-serif;color:#111;font-size:11px;-webkit-print-color-adjust:exact;print-color-adjust:exact}' +
      '.shell{width:100%;border-collapse:collapse}.shell>tbody>tr>td,.shell>tfoot>tr>td{padding:0;border:0}.shell>tfoot>tr>td{height:13mm}' +
      '.hdr{display:flex;align-items:center;gap:14px;background:#06215c;color:#fff;padding:12px 16px;border-bottom:4px solid #fad51b;border-radius:8px 8px 0 0}' +
      '.hdr img{width:56px;height:56px;object-fit:contain}.hdr b{display:block;font-size:14px}.hdr span{display:block;font-size:10px;opacity:.85}' +
      '.ttl{display:flex;justify-content:space-between;align-items:flex-end;margin:14px 0 10px}h1{margin:0;font-size:18px;color:#06215c}.meta{color:#555;font-size:10px;text-align:right}' +
      '.stats{display:flex;gap:10px;margin-bottom:12px}.stats div{flex:1;border:1px solid #cfd5e6;border-left:4px solid #fad51b;border-radius:6px;padding:7px 10px}.stats small{display:block;color:#555;font-size:9px}.stats b{font-size:13px;color:#06215c}' +
      'table.data{width:100%;border-collapse:collapse}table.data th{background:#06215c;color:#fff;text-align:left;padding:6px 8px;font-size:10px}' +
      'table.data td{padding:6px 8px;border-bottom:1px solid #ddd;vertical-align:top}table.data tr{page-break-inside:avoid}table.data tbody tr:nth-child(even) td{background:#f5f6fa}.r{text-align:right}' +
      '.sigs{display:flex;gap:28px;margin-top:46px;page-break-inside:avoid}.sig{flex:1;text-align:center}.sline{border-bottom:1px solid #111;height:40px;margin-bottom:5px}.sig b{display:block;font-size:11px}.sig span{font-size:10px;color:#444}' +
      '.ftr{position:fixed;left:0;right:0;bottom:0;border-top:2px solid #fad51b;padding-top:4px;font-size:9px;color:#555;display:flex;justify-content:space-between}';

    var body =
      '<table class="shell"><tbody><tr><td>' +
      '<div class="hdr"><img src="' + esc(absLogo()) + '" alt="" onerror="this.style.display=\'none\'"><div><b>' + esc(C.org) + '</b><span>' + esc(C.school) + '</span></div></div>' +
      '<div class="ttl"><h1>' + esc(C.title) + '</h1><div class="meta">Generated ' + esc(C.generated) + (C.printedBy ? '<br>by ' + esc(C.printedBy) : '') + '</div></div>' +
      '<div class="stats">' + stats + '</div>' +
      '<table class="data"><thead><tr>' + th + '</tr></thead><tbody>' + tb + '</tbody></table>' +
      '<div class="sigs">' + sigs + '</div>' +
      '</td></tr></tbody><tfoot><tr><td></td></tr></tfoot></table>' +
      '<div class="ftr"><span>iBITS Portal</span><span>System-generated record</span></div>';

    var w = window.open('', '_blank');
    if (!w) { alert('Please allow pop-ups to print.'); return; }
    w.document.open();
    w.document.write('<!doctype html><html><head><meta charset="utf-8"><title>' + esc(C.title) + '</title>' +
      '<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">' +
      '<style>' + css + '</style></head><body>' + body + '</body></html>');
    w.document.close();
    w.focus();
    setTimeout(function () { w.print(); }, 900);
  }

  /* ---------------- PDF ---------------- */
  function loadScript(src) {
    return new Promise(function (res, rej) {
      var s = document.createElement('script'); s.src = src; s.onload = res; s.onerror = rej; document.head.appendChild(s);
    });
  }
  function loadLogo() {
    return new Promise(function (res) {
      var im = new Image();
      im.onload = function () {
        try {
          var c = document.createElement('canvas'); c.width = im.naturalWidth; c.height = im.naturalHeight;
          c.getContext('2d').drawImage(im, 0, 0); res({d: c.toDataURL('image/png')});
        } catch (e) { res(null); }
      };
      im.onerror = function () { res(null); };
      im.src = absLogo();
    });
  }

  function doPdf(btn) {
    btn.classList.add('is-loading');
    var libs = window.jspdf ? Promise.resolve() :
      loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js')
        .then(function () { return loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js'); });

    Promise.all([libs, loadLogo()]).then(function (r) {
      var logo = r[1];
      var doc = new window.jspdf.jsPDF({orientation: C.orientation, unit: 'pt', format: 'a4'});
      var W = doc.internal.pageSize.getWidth(), H = doc.internal.pageSize.getHeight(), M = 40;

      /* Header band (first page) */
      doc.setFillColor(NAVY[0], NAVY[1], NAVY[2]); doc.rect(0, 0, W, 86, 'F');
      doc.setFillColor(GOLD[0], GOLD[1], GOLD[2]); doc.rect(0, 86, W, 4, 'F');
      var tx = M;
      if (logo) { doc.addImage(logo.d, 'PNG', M, 15, 56, 56); tx = M + 70; }
      doc.setTextColor(255); doc.setFont('helvetica', 'bold'); doc.setFontSize(13); doc.text(C.org, tx, 42);
      doc.setFont('helvetica', 'normal'); doc.setFontSize(9); doc.text(C.school, tx, 58);

      /* Title + meta */
      doc.setFont('helvetica', 'bold'); doc.setFontSize(16); doc.setTextColor(NAVY[0], NAVY[1], NAVY[2]); doc.text(C.title, M, 118);
      doc.setFont('helvetica', 'normal'); doc.setFontSize(9); doc.setTextColor(90);
      doc.text('Generated ' + C.generated + (C.printedBy ? ' by ' + C.printedBy : ''), M, 133);

      /* Summary boxes */
      var cnt = C.totals.length, gap = 8, bw = (W - 2 * M - (cnt - 1) * gap) / cnt, by = 146;
      C.totals.forEach(function (t, i) {
        var bx = M + i * (bw + gap);
        doc.setDrawColor(207, 213, 230); doc.setFillColor(255, 255, 255); doc.roundedRect(bx, by, bw, 38, 4, 4, 'FD');
        doc.setFillColor(GOLD[0], GOLD[1], GOLD[2]); doc.rect(bx, by + 3, 3, 32, 'F');
        doc.setFontSize(8); doc.setTextColor(90); doc.text(t.label, bx + 10, by + 15);
        doc.setFont('helvetica', 'bold'); doc.setFontSize(11); doc.setTextColor(NAVY[0], NAVY[1], NAVY[2]);
        doc.text(t.money ? money(t.value, 'PHP ') : String(t.value), bx + 10, by + 30);
        doc.setFont('helvetica', 'normal');
      });

      /* Table */
      doc.autoTable({
        startY: by + 54,
        margin: {left: M, right: M, top: 40, bottom: 60},
        head: [C.head.map(function (h) { return h.label; })],
        body: C.rows.map(function (row) { return row.map(function (v, i) { return cell(v, C.head[i], 'PHP '); }); }),
        styles: {fontSize: 8, cellPadding: 4},
        headStyles: {fillColor: NAVY, textColor: 255},
        alternateRowStyles: {fillColor: [245, 246, 250]},
        columnStyles: C.head.reduce(function (o, h, i) { if (h.money) o[i] = {halign: 'right'}; return o; }, {})
      });

      /* Signatories (blank signature space above name) */
      var y = doc.lastAutoTable.finalY + 36;
      if (y + 90 > H - 60) { doc.addPage(); y = 60; }
      var colW = (W - 2 * M) / 3;
      C.signatories.forEach(function (s, i) {
        var cx = M + i * colW, mid = cx + colW / 2;
        doc.setDrawColor(0); doc.setLineWidth(0.6); doc.line(cx + 14, y + 34, cx + colW - 14, y + 34);
        doc.setFont('helvetica', 'bold'); doc.setFontSize(10); doc.setTextColor(17); doc.text(s.name, mid, y + 48, {align: 'center'});
        doc.setFont('helvetica', 'normal'); doc.setFontSize(9); doc.setTextColor(70); doc.text(s.position, mid, y + 61, {align: 'center'});
      });

      /* Footer on every page */
      var total = doc.getNumberOfPages();
      for (var p = 1; p <= total; p++) {
        doc.setPage(p);
        doc.setDrawColor(GOLD[0], GOLD[1], GOLD[2]); doc.setLineWidth(1.5); doc.line(M, H - 40, W - M, H - 40);
        doc.setFont('helvetica', 'normal'); doc.setFontSize(8); doc.setTextColor(90);
        doc.text('iBITS Portal  |  System-generated record', M, H - 27);
        doc.text('Page ' + p + ' of ' + total, W - M, H - 27, {align: 'right'});
      }
      doc.save(C.filename + '.pdf');
    }).catch(function () {
      alert('Could not load the PDF tool (internet needed). Opening the print view instead: choose "Save as PDF".');
      doPrint(btn);
    }).then(function () { btn.classList.remove('is-loading'); });
  }

  bar.addEventListener('click', function (e) {
    var btn = e.target.closest('.btn-export');
    if (!btn || btn.hasAttribute('disabled')) return;
    var act = btn.getAttribute('data-act');
    if (act === 'print') doPrint(btn);
    else if (act === 'pdf') doPdf(btn);
    else if (act === 'xls') busy(btn, 1500);
  });
})();
</script>
<?php
}