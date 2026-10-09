<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/app/bootstrap.php';

/* =====================================================================
   SETTINGS
   ===================================================================== */

// true  = only logged-in users can see / print / download the member list.
// false = anyone can (the list shows student names publicly).
const MEMBERS_REQUIRE_LOGIN = true;

if (MEMBERS_REQUIRE_LOGIN && !current_user()) {
    header('Location: ' . url('login.php'));
    exit;
}

/** 1 -> "1st Year", 2 -> "2nd Year"; text values like "3rd Year" pass through. */
function year_label($v): string {
    $v = trim((string)$v);
    if ($v !== '' && ctype_digit($v)) {
        $n = (int)$v;
        $suffix = ($n % 100 >= 11 && $n % 100 <= 13) ? 'th' : ([1 => 'st', 2 => 'nd', 3 => 'rd'][$n % 10] ?? 'th');
        return $n . $suffix . ' Year';
    }
    return $v;
}

/**
 * Loads the member list: active org officers first, then active student
 * members grouped by program and year level (same accounts the home page
 * "Our Population" section counts). Returns null if the query fails.
 */
function members_rows(): ?array {
    try {
        $list = q_all("SELECT full_name, program, year_level, position, account_type
                       FROM users
                       WHERE status = 'active' AND account_type IN ('member', 'admin')
                       ORDER BY (account_type = 'admin') DESC, program, year_level, full_name");
        $out = [];
        foreach ($list as $r) {
            $pos  = trim((string)($r['position'] ?? ''));
            $prog = trim((string)($r['program'] ?? ''));
            $yr   = year_label($r['year_level'] ?? '');
            $out[] = [
                'name'     => (string)$r['full_name'],
                'program'  => $prog !== '' ? $prog : '—',
                'year'     => $yr !== '' ? $yr : '—',
                'position' => $pos !== '' ? $pos : 'Member',
                'officer'  => $r['account_type'] === 'admin',
            ];
        }
        return $out;
    } catch (Throwable $ex) {
        error_log('members.php: ' . $ex->getMessage());
        return null;
    }
}

$rows   = members_rows();
$failed = $rows === null;
$rows   = $rows ?? [];
$total  = count($rows);

$byProgram = [];
$officers  = 0;
foreach ($rows as $r) {
    if ($r['officer']) { $officers++; continue; }
    $byProgram[$r['program']] = ($byProgram[$r['program']] ?? 0) + 1;
}
ksort($byProgram);

$orgName = 'Institute of Brilliant Information Technology Students, PUPLC';
$logoUrl = asset('images/ibits-logo.png');

html_head('Members', 'home');
?>
<style>
/* ---------- Members page ---------- */
.mem-hero{position:relative;padding:130px 0 56px;text-align:center;color:#fff;
  background:radial-gradient(720px 360px at 50% 0,rgba(10,50,140,.55),transparent 70%),var(--ink)}
.mem-hero::after{content:"";position:absolute;left:0;right:0;bottom:0;height:1px;background:linear-gradient(90deg,transparent,rgba(250,213,23,.55),transparent)}
.mem-hero h1{font-size:clamp(2rem,5vw,3.2rem);font-weight:800;line-height:1.1;margin-bottom:10px;letter-spacing:-.01em}
.mem-hero p{max-width:560px;margin:0 auto;color:#b8c4de;font-size:15px}
.mem-sec{padding:48px 0 76px}
.mem-toolbar{display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin-bottom:32px}
.mem-toolbar .btn[disabled]{opacity:.45;cursor:not-allowed;transform:none!important}
.mem-toolbar .btn .ico{width:18px;height:18px}
.mem-stats{margin-bottom:36px}
#roster{scroll-margin-top:100px}
.mem-table{width:100%;border-collapse:separate;border-spacing:0;font-size:14.5px;color:#1f2937;border:1px solid #e3e8f2;border-radius:16px;overflow:hidden}
.mem-table th{background:var(--navy);color:#fff;font-size:12.5px;letter-spacing:.08em;text-transform:uppercase;padding:13px 16px;border-bottom:3px solid var(--gold);text-align:left}
.mem-table td{padding:12px 16px;border-bottom:1px solid #e9eef6;background:#fff}
.mem-table tbody tr:nth-child(even) td{background:#f4f6fb}
.mem-table tbody tr:last-child td{border-bottom:0}
.mem-table td:first-child,.mem-table th:first-child{width:56px;text-align:center}
.print-head{display:none}

/* ---------- Print ---------- */
@page{size:A4;margin:12mm}
@media print{
  *{-webkit-print-color-adjust:exact;print-color-adjust:exact}
  html,body{background:#fff!important}
  .pnav,.mem-hero,.mem-toolbar,.site-footer,.rain{display:none!important}
  .mem-sec{padding:0}
  #roster{display:block!important}
  .sec h2::after{animation:none}
  .print-head{display:flex;align-items:center;gap:14px;background:var(--navy);color:#fff;padding:14px 18px;border-radius:12px;border-bottom:4px solid var(--gold);margin-bottom:18px}
  .print-head img{width:54px;height:54px;border-radius:50%}
  .print-head b{display:block;font-size:20px;line-height:1.2}
  .print-head span{font-size:11px;color:#cbd5e1}
  .print-head em{margin-left:auto;font-style:normal;font-size:11px;color:var(--gold)}
  .mem-table thead{display:table-header-group}
  .mem-table tr{break-inside:avoid}
  .pop-card{break-inside:avoid;transform:none!important}
  .sec-white{background:#fff!important}
}
</style>

<header class="mem-hero">
  <div class="container">
    <span class="badge-pill">DIRECTORY</span>
    <h1>Members</h1>
    <p>View, print, or download the official iBITS member list.</p>
  </div>
</header>

<main>
  <section class="sec sec-white mem-sec" id="members" data-logo="<?= e($logoUrl) ?>" data-org="<?= e($orgName) ?>">
    <div class="container">

      <div class="print-head">
        <img src="<?= e($logoUrl) ?>" alt="">
        <div><b>iBITS Members Directory</b><span><?= e($orgName) ?></span></div>
        <em>Generated <?= e(date('F j, Y')) ?></em>
      </div>

      <?php if ($failed): ?>
        <p class="empty">The member list couldn't be loaded right now. Please try again later.</p>
      <?php endif; ?>

      <div class="mem-toolbar" role="group" aria-label="Member list actions">
        <button class="btn btn-navy" type="button" id="btn-view" aria-expanded="false" aria-controls="roster"<?= $total ? '' : ' disabled' ?>>
          <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
          <span>View members</span>
        </button>
        <button class="btn btn-navy" type="button" id="btn-print"<?= $total ? '' : ' disabled' ?>>
          <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="7"/></svg>
          <span>Print</span>
        </button>
        <button class="btn btn-gold" type="button" id="btn-pdf"<?= $total ? '' : ' disabled' ?>>
          <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v12M7 10l5 5 5-5M4 21h16"/></svg>
          <span>Download PDF</span>
        </button>
        <button class="btn btn-gold" type="button" id="btn-xlsx"<?= $total ? '' : ' disabled' ?>>
          <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v12M7 10l5 5 5-5M4 21h16"/></svg>
          <span>Download Excel</span>
        </button>
      </div>

      <?php if ($total): ?>
        <div class="mem-stats pop-grid">
          <div class="pop-card pop-card-primary">
            <div class="pop-num"><?= $total ?></div>
            <div class="pop-label">Total Members</div>
          </div>
          <?php foreach ($byProgram as $prog => $n): ?>
            <div class="pop-card pop-card-primary">
              <div class="pop-num"><?= $n ?></div>
              <div class="pop-label"><?= e($prog) ?> Students</div>
            </div>
          <?php endforeach; ?>
          <?php if ($officers): ?>
            <div class="pop-card pop-card-primary">
              <div class="pop-num"><?= $officers ?></div>
              <div class="pop-label">iBITS Org Officers</div>
            </div>
          <?php endif; ?>
        </div>

        <div id="roster" hidden>
          <h2>Member List</h2>
          <div class="tbl-wrap">
            <table class="mem-table" id="members-table">
              <thead>
                <tr><th>#</th><th>Name</th><th>Program</th><th>Year Level</th><th>Position</th></tr>
              </thead>
              <tbody>
                <?php foreach ($rows as $i => $r): ?>
                  <tr<?= $r['officer'] ? ' data-officer="1"' : '' ?>>
                    <td><?= $i + 1 ?></td>
                    <td><?= e($r['name']) ?></td>
                    <td><?= e($r['program']) ?></td>
                    <td><?= e($r['year']) ?></td>
                    <td><?= e($r['position']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php elseif (!$failed): ?>
        <p class="empty">No members found.</p>
      <?php endif; ?>

    </div>
  </section>
</main>

<?php public_footer(); ?>

<script>
(function(){
  var app = document.getElementById('members');
  if (!app) return;

  var LOGO_URL = app.getAttribute('data-logo');
  var ORG      = app.getAttribute('data-org');
  var LIBS = {
    jspdf:     'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js',
    autotable: 'https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js',
    exceljs:   'https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js'
  };
  var NAVY = [6, 33, 92], GOLD = [250, 213, 23];

  var roster  = document.getElementById('roster');
  var btnView = document.getElementById('btn-view');
  var btnPrint= document.getElementById('btn-print');
  var btnPdf  = document.getElementById('btn-pdf');
  var btnXlsx = document.getElementById('btn-xlsx');

  /* ---------- helpers ---------- */
  var loaded = {};
  function loadScript(src) {
    if (loaded[src]) return loaded[src];
    loaded[src] = new Promise(function(res, rej){
      var s = document.createElement('script');
      s.src = src; s.onload = res;
      s.onerror = function(){ delete loaded[src]; rej(new Error('Could not load ' + src)); };
      document.head.appendChild(s);
    });
    return loaded[src];
  }

  function getRows() {
    return Array.prototype.map.call(document.querySelectorAll('#members-table tbody tr'), function(tr){
      var a = Array.prototype.map.call(tr.children, function(td){ return td.textContent.trim(); });
      a.officer = tr.getAttribute('data-officer') === '1';
      return a;
    });
  }

  function getLogo() {
    return new Promise(function(resolve){
      var img = new Image();
      img.onload = function(){
        try {
          var c = document.createElement('canvas');
          c.width = img.naturalWidth; c.height = img.naturalHeight;
          c.getContext('2d').drawImage(img, 0, 0);
          resolve({ data: c.toDataURL('image/png'), w: c.width, h: c.height });
        } catch (e) { resolve(null); }
      };
      img.onerror = function(){ resolve(null); };
      img.src = LOGO_URL;
    });
  }

  function today() {
    return new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
  }
  function stamp() { return new Date().toISOString().slice(0, 10); }

  function saveBlob(blob, name) {
    var a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = name;
    document.body.appendChild(a);
    a.click();
    setTimeout(function(){ URL.revokeObjectURL(a.href); a.remove(); }, 1000);
  }

  function programCounts(rows) {
    var m = {};
    rows.forEach(function(r){ if (!r.officer) m[r[2]] = (m[r[2]] || 0) + 1; });
    return Object.keys(m).sort().map(function(k){ return [k, m[k]]; });
  }
  function officerCount(rows) { return rows.filter(function(r){ return r.officer; }).length; }

  function busy(btn, fn) {
    var label = btn.querySelector('span'), old = label.textContent;
    btn.disabled = true; label.textContent = 'Preparing…';
    return fn().catch(function(err){
      console.error(err);
      alert('Sorry, the file could not be created. Please check your internet connection and try again.');
    }).then(function(){ btn.disabled = false; label.textContent = old; });
  }

  /* ---------- view ---------- */
  if (btnView && roster) {
    btnView.addEventListener('click', function(){
      var opening = roster.hasAttribute('hidden');
      roster.toggleAttribute('hidden', !opening);
      btnView.setAttribute('aria-expanded', opening ? 'true' : 'false');
      btnView.querySelector('span').textContent = opening ? 'Hide members' : 'View members';
      if (opening) roster.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  }

  /* ---------- print ---------- */
  if (btnPrint) btnPrint.addEventListener('click', function(){ window.print(); });

  /* ---------- PDF ---------- */
  async function makePdf() {
    var rows = getRows();
    await loadScript(LIBS.jspdf);
    await loadScript(LIBS.autotable);
    var logo = await getLogo();

    var doc = new window.jspdf.jsPDF({ unit: 'mm', format: 'a4' });
    var W = doc.internal.pageSize.getWidth(), H = doc.internal.pageSize.getHeight();

    function header() {
      doc.setFillColor.apply(doc, NAVY);  doc.rect(0, 0, W, 32, 'F');
      doc.setFillColor.apply(doc, GOLD);  doc.rect(0, 32, W, 1.6, 'F');
      var x = 14;
      if (logo) {
        var s = 20, w = s, h = s;
        if (logo.w > logo.h) h = s * logo.h / logo.w; else w = s * logo.w / logo.h;
        doc.addImage(logo.data, 'PNG', 14 + (s - w) / 2, 6 + (s - h) / 2, w, h);
        x = 40;
      }
      doc.setTextColor(255, 255, 255); doc.setFont('helvetica', 'bold'); doc.setFontSize(18);
      doc.text('iBITS Members Directory', x, 16);
      doc.setFont('helvetica', 'normal'); doc.setFontSize(8.5); doc.setTextColor(203, 213, 225);
      doc.text(ORG, x, 22);
      doc.setTextColor.apply(doc, GOLD);
      doc.text('Generated ' + today(), W - 14, 16, { align: 'right' });
    }

    // Summary cards (same look as the website's population cards)
    var cards = [['Total Members', rows.length]].concat(
      programCounts(rows).slice(0, 4).map(function(p){ return [p[0] + ' Students', p[1]]; })
    );
    if (officerCount(rows)) cards.push(['iBITS Org Officers', officerCount(rows)]);
    var gap = 4, top = 41, ch = 20;
    var cw = (W - 28 - gap * (cards.length - 1)) / cards.length;
    cards.forEach(function(c, i){
      var x = 14 + i * (cw + gap);
      doc.setFillColor.apply(doc, NAVY); doc.roundedRect(x, top, cw, ch, 3, 3, 'F');
      doc.setFillColor.apply(doc, GOLD); doc.rect(x + 4, top, 10, 1.2, 'F');
      doc.setTextColor.apply(doc, GOLD); doc.setFont('helvetica', 'bold'); doc.setFontSize(17);
      doc.text(String(c[1]), x + 5, top + 11.5);
      doc.setTextColor(203, 213, 225); doc.setFont('helvetica', 'normal'); doc.setFontSize(7.5);
      doc.text(doc.splitTextToSize(c[0], cw - 8)[0], x + 5, top + 17);
    });

    doc.autoTable({
      head: [['#', 'Name', 'Program', 'Year Level', 'Position']],
      body: rows,
      startY: top + ch + 8,
      margin: { top: 40, left: 14, right: 14, bottom: 18 },
      theme: 'striped',
      styles: { font: 'helvetica', fontSize: 9, cellPadding: 2.6, textColor: [31, 41, 55], lineColor: [227, 232, 242], lineWidth: 0.1 },
      headStyles: { fillColor: NAVY, textColor: [255, 255, 255], fontStyle: 'bold', halign: 'left' },
      alternateRowStyles: { fillColor: [244, 246, 251] },
      columnStyles: { 0: { cellWidth: 12, halign: 'center' } },
      didDrawPage: header
    });

    var pages = doc.getNumberOfPages();
    for (var i = 1; i <= pages; i++) {
      doc.setPage(i);
      doc.setFillColor.apply(doc, GOLD); doc.rect(14, H - 13, W - 28, 0.6, 'F');
      doc.setFont('helvetica', 'normal'); doc.setFontSize(8); doc.setTextColor(100, 116, 139);
      doc.text('iBITS PUPLC \u2022 We code as one.', 14, H - 8);
      doc.text('Page ' + i + ' of ' + pages, W - 14, H - 8, { align: 'right' });
    }

    doc.save('iBITS-Members-' + stamp() + '.pdf');
  }

  /* ---------- Excel ---------- */
  async function makeXlsx() {
    var rows = getRows();
    await loadScript(LIBS.exceljs);
    var logo = await getLogo();

    var wb = new window.ExcelJS.Workbook();
    wb.creator = 'iBITS PUPLC';
    wb.created = new Date();

    var fillNavy = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF06215C' } };
    var fillGold = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFAD517' } };
    var fillZebra = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFF4F6FB' } };
    var thin = { style: 'thin', color: { argb: 'FFE3E8F2' } };

    var ws = wb.addWorksheet('Members', {
      views: [{ state: 'frozen', ySplit: 6 }],
      pageSetup: { orientation: 'portrait', fitToPage: true, fitToWidth: 1, fitToHeight: 0 }
    });
    ws.columns = [{ width: 8 }, { width: 36 }, { width: 18 }, { width: 16 }, { width: 24 }];

    // Banner (rows 1-2) + gold stripe (row 3) + date (row 4)
    for (var r = 1; r <= 2; r++) {
      ws.getRow(r).height = 28;
      for (var c = 1; c <= 5; c++) ws.getCell(r, c).fill = fillNavy;
    }
    ws.mergeCells('B1:E1'); ws.mergeCells('B2:E2');
    ws.getCell('B1').value = 'iBITS Members Directory';
    ws.getCell('B1').font = { name: 'Calibri', size: 20, bold: true, color: { argb: 'FFFFFFFF' } };
    ws.getCell('B1').alignment = { vertical: 'bottom', horizontal: 'left' };
    ws.getCell('B2').value = ORG;
    ws.getCell('B2').font = { name: 'Calibri', size: 10, color: { argb: 'FFCBD5E1' } };
    ws.getCell('B2').alignment = { vertical: 'top', horizontal: 'left' };
    ws.getRow(3).height = 5;
    for (c = 1; c <= 5; c++) ws.getCell(3, c).fill = fillGold;
    ws.mergeCells('A4:E4');
    ws.getCell('A4').value = 'Generated ' + today() + '  \u2022  ' + rows.length + ' members';
    ws.getCell('A4').font = { name: 'Calibri', size: 10, italic: true, color: { argb: 'FF64748B' } };
    ws.getCell('A4').alignment = { horizontal: 'right', vertical: 'middle' };
    ws.getRow(4).height = 20;
    ws.getRow(5).height = 6;

    if (logo) {
      try {
        var id = wb.addImage({ base64: logo.data, extension: 'png' });
        ws.addImage(id, { tl: { col: 0.15, row: 0.15 }, ext: { width: 52, height: 52 } });
      } catch (e) { /* logo is optional */ }
    }

    // Table header (row 6)
    var head = ws.getRow(6);
    head.values = ['#', 'Name', 'Program', 'Year Level', 'Position'];
    head.height = 24;
    head.eachCell(function(cell, col){
      cell.fill = fillNavy;
      cell.font = { name: 'Calibri', size: 11, bold: true, color: { argb: 'FFFFFFFF' } };
      cell.alignment = { vertical: 'middle', horizontal: col === 1 ? 'center' : 'left', indent: col === 1 ? 0 : 1 };
      cell.border = { bottom: { style: 'medium', color: { argb: 'FFFAD517' } } };
    });

    // Data rows
    rows.forEach(function(row, i){
      var xr = ws.getRow(7 + i);
      xr.values = [Number(row[0]), row[1], row[2], row[3], row[4]];
      xr.height = 20;
      xr.eachCell(function(cell, col){
        cell.font = { name: 'Calibri', size: 11, color: { argb: 'FF1F2937' } };
        cell.alignment = { vertical: 'middle', horizontal: col === 1 ? 'center' : 'left', indent: col === 1 ? 0 : 1 };
        cell.border = { bottom: thin };
        if (i % 2 === 1) cell.fill = fillZebra;
      });
    });

    ws.autoFilter = { from: 'A6', to: 'E6' };

    // Summary sheet
    var sm = wb.addWorksheet('Summary');
    sm.columns = [{ width: 34 }, { width: 14 }];
    sm.mergeCells('A1:B1');
    sm.getCell('A1').value = 'Membership Summary';
    sm.getCell('A1').font = { name: 'Calibri', size: 16, bold: true, color: { argb: 'FFFFFFFF' } };
    sm.getCell('A1').fill = fillNavy;
    sm.getCell('B1').fill = fillNavy;
    sm.getCell('A1').alignment = { vertical: 'middle', indent: 1 };
    sm.getRow(1).height = 30;
    sm.getRow(2).height = 4;
    sm.getCell('A2').fill = fillGold; sm.getCell('B2').fill = fillGold;
    var lines = [['Total Members', rows.length]].concat(programCounts(rows).map(function(p){ return [p[0] + ' Students', p[1]]; }));
    if (officerCount(rows)) lines.push(['iBITS Org Officers', officerCount(rows)]);
    lines.forEach(function(l, i){
      var rr = sm.getRow(4 + i);
      rr.values = l;
      rr.height = 22;
      rr.getCell(1).font = { name: 'Calibri', size: 11, bold: i === 0 };
      rr.getCell(2).font = { name: 'Calibri', size: 11, bold: true, color: { argb: 'FF06215C' } };
      rr.getCell(2).alignment = { horizontal: 'center' };
      rr.getCell(1).alignment = { indent: 1, vertical: 'middle' };
      rr.getCell(1).border = rr.getCell(2).border = { bottom: thin };
      if (i === 0) { rr.getCell(1).fill = fillZebra; rr.getCell(2).fill = fillZebra; }
    });

    var buf = await wb.xlsx.writeBuffer();
    saveBlob(new Blob([buf], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' }),
             'iBITS-Members-' + stamp() + '.xlsx');
  }

  if (btnPdf)  btnPdf.addEventListener('click',  function(){ busy(btnPdf,  makePdf); });
  if (btnXlsx) btnXlsx.addEventListener('click', function(){ busy(btnXlsx, makeXlsx); });
})();
</script>

<?php html_foot(); ?>