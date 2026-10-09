<?php
declare(strict_types=1);

const ICONS = [
    'home'     => '<path d="M3 11l9-8 9 8M5 10v10h5v-6h4v6h5V10"/>',
    'events'   => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>',
    'forms'    => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6M8 13h8M8 17h5"/>',
    'fines'    => '<circle cx="12" cy="12" r="9"/><path d="M14.5 9a2.5 2 0 0 0-2.5-1.5c-1.5 0-2.5.8-2.5 2s1 1.7 2.5 2 2.5.8 2.5 2-1 2-2.5 2A2.7 2 0 0 1 9.5 15M12 6v2M12 16v2"/>',
    'helpdesk' => '<path d="M21 12a8 8 0 0 1-11.5 7.2L4 21l1.8-5.5A8 8 0 1 1 21 12z"/>',
    'org'      => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.5 3-5.5 6.5-5.5s6.5 2 6.5 5.5M16 4.5a3.5 3.5 0 0 1 0 7M18 14.8c2 .7 3.5 2.4 3.5 5.2"/>',
    'cbl'      => '<path d="M12 3 4 6v6c0 4.5 3.4 8 8 9 4.6-1 8-4.5 8-9V6l-8-3Z"/><path d="m8.5 12 2.5 2.5 4.5-5"/>',
    'logout'   => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>',
    'user'     => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-6 8-6s8 2 8 6"/>',
];
function icon(string $name): string {
    return '<svg class="ico" viewBox="0 0 24 24" aria-hidden="true">' . (ICONS[$name] ?? '') . '</svg>';
}

function html_head(string $title, string $bodyClass = ''): void { ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> | iBITS Portal</title>
<link rel="icon" href="<?= e(asset('images/ibits-logo.png')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('css/site.css')) ?>">
</head>
<body class="<?= e($bodyClass) ?>">
<?php }

function html_foot(): void { ?>
<script src="<?= e(asset('js/site.js')) ?>"></script>
</body>
</html>
<?php }

/** Footer used on the public pages (home + login + register). */
function public_footer(): void { ?>
<footer class="site-footer" id="footer">
  <div class="container">
    <div class="foot-brand">
      <img src="<?= e(asset('images/ibits-logo.png')) ?>" alt="iBITS logo">
      <div>
        <div class="foot-name">iBITS PUPLC</div>
        <p>Institute of Brilliant Information Technology Students, PUP Lopez Campus.</p>
      </div>
    </div>
    <div class="foot-grid">
      <div>
        <h4>Explore</h4>
        <ul>
          <li><a href="<?= e(url('index.php#who-we-are')) ?>">Who We Are</a></li>
          <li><a href="<?= e(url('index.php#population')) ?>">Population</a></li>
          <li><a href="<?= e(url('index.php#mission-vision')) ?>">Mission &amp; Vision</a></li>
          <li><a href="<?= e(url('index.php#events')) ?>">Events</a></li>
        </ul>
      </div>
      <div>
        <h4>Portal</h4>
        <ul>
          <li><a href="<?= e(url('register.php')) ?>">Join the Org</a></li>
          <li><a href="<?= e(url('login.php')) ?>">Log in</a></li>
        </ul>
      </div>
      <div>
        <h4>Address</h4>
        <p>PUP Lopez Campus, Lopez, Quezon</p>
        <p><a href="https://www.google.com/maps/search/?api=1&amp;query=PUP+Lopez+Quezon+Campus" target="_blank" rel="noopener">View on map</a></p>
      </div>
      <div>
        <h4>Contact</h4>
        <p><a href="mailto:ibitspupofficial@gmail.com">ibitspupofficial@gmail.com</a></p>
      </div>
    </div>
    <p class="copy">&copy; <?= date('Y') ?> iBITS PUPLC. We code as one.</p>
  </div>
</footer>
<?php }

/**
 * Dashboard shell (sidebar + content) for member and admin pages.
 * The menu is built from what this user is allowed to open.
 */
function dash_start(string $title, string $active): void {
    $u = current_user();
    $admin = is_admin();
    html_head($title, 'dash');

    $items = [];
    if ($admin) {
        $items[] = ['index', 'Overview', 'admin/index.php', 'home'];
        foreach (user_features() as $key => $level) {
            [$label, $page] = FEATURES[$key];
            $items[] = [$key, $label, $page, $key];
        }
    } else {
        $items[] = ['index',    'Overview',  'member/index.php',    'home'];
        $items[] = ['helpdesk', 'Help Desk', 'member/helpdesk.php', 'helpdesk'];
    }
    ?>
<div class="dash-wrap">
  <aside class="side" id="side">
    <a class="side-brand" href="<?= e(url($admin ? 'admin/index.php' : 'member/index.php')) ?>">
      <img src="<?= e(asset('images/ibits-logo.png')) ?>" alt="">
      <span>iBITS Portal</span>
    </a>
    <div class="side-who">
      <span class="avatar"><?= e(initials($u['full_name'])) ?></span>
      <div>
        <b><?= e($u['full_name']) ?></b>
        <small><?= $admin ? e($u['position']) : 'Member' ?></small>
      </div>
    </div>
    <nav>
      <?php foreach ($items as [$key, $label, $page, $ic]): ?>
        <a href="<?= e(url($page)) ?>" class="<?= $key === $active ? 'on' : '' ?>"><?= icon($ic) ?><span><?= e($label) ?></span></a>
      <?php endforeach; ?>
    </nav>
    <form method="post" action="<?= e(url('logout.php')) ?>" class="side-out">
      <?= csrf_field() ?>
      <button type="submit"><?= icon('logout') ?><span>Log out</span></button>
    </form>
  </aside>
  <div class="dash-main">
    <header class="dash-top">
      <button class="burger" type="button" aria-label="Menu" data-toggle="side">
        <svg class="ico" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
      </button>
      <h1><?= e($title) ?></h1>
      <?php if ($admin): ?><span class="pos-chip"><?= e($u['position']) ?></span><?php endif; ?>
    </header>
    <main class="dash-content">
      <?= flash_render() ?>
<?php }

function dash_end(): void { ?>
    </main>
  </div>
</div>
<?php html_foot(); }
