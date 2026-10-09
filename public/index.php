<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/app/bootstrap.php';

$pop    = population();
$events = home_events(4);

html_head('We Code as One');
?>

<!-- ============ HEADER: We Code as One + Join / Member Log in ============ -->
<header class="hero">
  <canvas class="rain" aria-hidden="true"></canvas>
  <div class="container inner">
    <img class="logo" src="<?= e(asset('images/ibits-logo.png')) ?>" alt="iBITS Logo" width="230" height="230">
    <span class="badge-pill">OFFICIAL PORTAL</span>
    <h1>WE CODE AS ONE</h1>
    <p>At iBITS, we unite brilliant minds and passionate builders<br>under a shared passion for technology to forge innovationtogether as a single, unified force.</p>
    <div class="cta">
      <a class="btn cta-register" href="<?= e(url('register.php')) ?>">Join the Org</a>
      <a class="btn btn-ghost"    href="<?= e(url('login.php')) ?>">Member Login</a>
    </div>
  </div>
</header>

<main>
  <!-- ============ WHO WE ARE ============ -->
  <section class="sec sec-light" id="who-we-are">
    <canvas class="rain" data-rain="light" aria-hidden="true"></canvas>
    <div class="container">
      <h2>Who We Are</h2>
      <div class="who">
        <div class="navy-card">
          <h3>We Are More Than an Academic Organization</h3>
          <p class="sub">A Unified Force for Tech Innovators.</p>
          <p>At the <b>Institute of Brilliant Information Technology Students</b>, we unite brilliant minds and passionate builders under a shared love for technology to forge innovation together as a single, unified force.</p>
          <p>We create spaces where students don't just study technology, <b>we code as one.</b></p>
        </div>
        <img class="mascot" src="<?= e(asset('images/ibits-mascot.png')) ?>" alt="iBITS Bitty" loading="lazy">
      </div>
    </div>
  </section>

  <!-- ============ POPULATION ============ -->
  <section class="sec sec-dark pop-section" id="population">
    <canvas class="rain" data-rain="dark" aria-hidden="true"></canvas>
    <div class="container">
      <h2>Our Population</h2>
      <p class="lead">Live counts across every section and year level.</p>

      <?php
      $bsit = ['BSIT 1' => 48, 'BSIT 2' => 50, 'BSIT 3' => 49, 'BSIT 4' => 48];
      $dit  = ['DIT 1'  => 44, 'DIT 2'  => 40, 'DIT 3'  => 32];
      $officers      = 38;
      $bsitTotal     = array_sum($bsit);
      $ditTotal      = array_sum($dit);
      $studentsTotal = $bsitTotal + $ditTotal;
      $overallTotal  = $studentsTotal + $officers;
      ?>

      <!-- Summary row -->
      <div class="pop-grid">
        <div class="pop-card pop-card-primary">
          <div class="pop-num" data-target="<?= $overallTotal ?>">0</div>
          <div class="pop-label">Total iBITS Members</div>
        </div>
        <div class="pop-card pop-card-primary">
          <div class="pop-num" data-target="<?= $bsitTotal ?>">0</div>
          <div class="pop-label">Total BSIT Students</div>
        </div>
        <div class="pop-card pop-card-primary">
          <div class="pop-num" data-target="<?= $ditTotal ?>">0</div>
          <div class="pop-label">Total DIT Students</div>
        </div>
        <div class="pop-card pop-card-primary">
          <div class="pop-num" data-target="<?= $officers ?>">0</div>
          <div class="pop-label">iBITS Org Officers</div>
        </div>
      </div>

      <!-- BSIT breakdown -->
      <h3 class="pop-subhead">BSIT · By Year Level</h3>
      <div class="pop-grid">
        <?php foreach ($bsit as $label => $count): ?>
          <div class="pop-card pop-card-dark">
            <div class="pop-num" data-target="<?= (int)$count ?>">0</div>
            <div class="pop-label"><?= htmlspecialchars($label) ?></div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- DIT breakdown -->
      <h3 class="pop-subhead">DIT · By Year Level</h3>
      <div class="pop-grid pop-grid-3">
        <?php foreach ($dit as $label => $count): ?>
          <div class="pop-card pop-card-dark">
            <div class="pop-num" data-target="<?= (int)$count ?>">0</div>
            <div class="pop-label"><?= htmlspecialchars($label) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ============ MISSION & VISION + EVENTS ============ -->
  <section class="sec sec-light" id="mission-vision">
    <canvas class="rain" data-rain="light" aria-hidden="true"></canvas>
    <div class="container">
      <h2>Mission &amp; Vision</h2>
      <div class="mv">
        <article class="mv-card">
          <div class="mv-ico navy"><svg class="ico" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.5"/></svg></div>
          <h3>Our Mission</h3>
          <p>To foster a collaborative environment where IT students can develop technical excellence, leadership skills, and ethical professionalism through innovative projects and community engagement.</p>
        </article>
        <article class="mv-card">
          <div class="mv-ico gold"><svg class="ico" viewBox="0 0 24 24"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg></div>
          <h3>Our Vision</h3>
          <p>To be the leading student organization that produces globally competitive technology professionals who drive digital transformation and social progress.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="sec" id="events" style="background:var(--bg)">
    <div class="container">
      <h2>Events</h2>
      <?php if (!$events): ?>
        <p class="empty">No events posted yet. Check back soon.</p>
      <?php else: ?>
        <div class="ev-grid">
          <?php foreach ($events as $ev): $st = event_status($ev); ?>
            <article class="ev">
              <div class="ev-date"><b><?= e(fmt_date($ev['start_datetime'], 'j')) ?></b><span><?= e(fmt_date($ev['start_datetime'], 'M Y')) ?></span></div>
              <h3><?= e($ev['title']) ?></h3>
              <?php if ($ev['description']): ?><p><?= e($ev['description']) ?></p><?php endif; ?>
              <div class="ev-meta"><span><?= e($ev['location']) ?></span><span class="tag tag-<?= e($st) ?>"><?= e($st) ?></span></div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php public_footer(); html_foot(); ?>
