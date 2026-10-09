<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/app/bootstrap.php';

<<<<<<< HEAD
$pop    = population();
$events = home_events(4);

html_head('We Code as One');
=======
$isLoggedIn = !empty($_SESSION['user_id']);

$user = [
    'id'       => $_SESSION['user_id']    ?? '',
    'name'     => $_SESSION['user_name']  ?? '',
    'fullName' => $_SESSION['user_name']  ?? '',
    'role'     => $_SESSION['role']       ?? 'Member',
    'program'  => $_SESSION['program']    ?? '',
];

$permsByRole = [
    'President'          => ['admin_org','admin_events','admin_forms','admin_cbl','manage_tickets'],
    'Vice President'     => ['admin_org','admin_events','admin_forms','admin_cbl','manage_tickets'],
    'Secretary'          => ['admin_events','admin_forms','manage_tickets'],
    'Institutional'      => ['admin_events'],
    'Treasurer'          => [],
    'Assistant Treasurer'=> [],
    'Auditor'            => [],
    'Assistant Auditor'  => [],
    'Adviser'            => ['admin_cbl'],
    'Member'             => [],
];
$perms = $permsByRole[$user['role']] ?? [];

$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$base = '/ibits-portal/public';
$route = trim(str_replace($base, '', $uri), '/') ?: 'home';

$routeToView = [
    'home'      => 'view-public-home',
    'login'     => 'view-login',
    'register'  => 'view-login',
    'events'    => 'view-public-events',
    'about'     => 'view-public-about',
    'faqs'      => 'view-public-faqs',
    'org'       => 'view-public-org',
    'dashboard' => 'view-member-dashboard',
    'fines'     => 'view-member-fines',
    'my-events' => 'view-member-events',
    'forms'     => 'view-member-forms',
    'helpdesk'  => 'view-member-helpdesk',
    'settings'  => 'view-member-settings',
];

$activeViewId = $routeToView[$route] ?? 'view-public-home';
>>>>>>> ca48e16fab416c6166ad70c9f09dea4d97d0472a
?>

<!-- ============ HEADER: We Code as One + Join / Member Log in ============ -->
<header class="hero">
  <canvas class="rain" aria-hidden="true"></canvas>
  <div class="container inner">
    <img class="logo" src="<?= e(asset('images/ibits-logo.png')) ?>" alt="iBITS PUPLC logo" width="230" height="230">
    <span class="badge-pill">OFFICIAL PORTAL</span>
    <h1>WE CODE AS ONE</h1>
    <p>At iBITS, we unite brilliant minds and passionate builders under a shared passion for technology to forge innovation together as a single, unified force.</p>
    <div class="cta">
      <a class="btn btn-gold" href="<?= e(url('register.php')) ?>">Join the Org</a>
      <a class="btn btn-ghost" href="<?= e(url('login.php')) ?>">Member Log in</a>
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
        <img class="mascot" src="<?= e(asset('images/ibits-mascot.png')) ?>" alt="iBITS mascot" loading="lazy">
      </div>
    </div>
  </section>

  <!-- ============ POPULATION ============ -->
  <section class="sec sec-dark" id="population">
    <canvas class="rain" data-rain="dark" aria-hidden="true"></canvas>
    <div class="container">
      <h2>Our Population</h2>
      <p class="lead">Active members across every program and year level.</p>

      <div class="pop-grid">
        <div class="pop-card main"><div class="pop-num" data-count="<?= $pop['total'] ?>"><?= $pop['total'] ?></div><div class="pop-label">Total iBITS Members</div></div>
        <?php foreach ($pop['programs'] as $prog => $p): ?>
          <div class="pop-card main"><div class="pop-num" data-count="<?= $p['total'] ?>"><?= $p['total'] ?></div><div class="pop-label">Total <?= e($prog) ?> Students</div></div>
        <?php endforeach; ?>
        <div class="pop-card main"><div class="pop-num" data-count="<?= $pop['officers'] ?>"><?= $pop['officers'] ?></div><div class="pop-label">iBITS Org Officers</div></div>
      </div>

      <?php foreach ($pop['programs'] as $prog => $p): ?>
        <h3 class="pop-sub"><?= e($prog) ?> · By Year Level</h3>
        <div class="pop-grid <?= count($p['levels']) === 3 ? 'n3' : '' ?>">
          <?php foreach ($p['levels'] as $label => $n): ?>
            <div class="pop-card"><div class="pop-num" data-count="<?= $n ?>"><?= $n ?></div><div class="pop-label"><?= e($label) ?></div></div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
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
