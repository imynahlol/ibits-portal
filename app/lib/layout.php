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
<?php
    // The top navigation only appears on the public pages (home).
    // Login/register ('auth') and dashboard ('dash') keep their own layouts.
    if ($bodyClass === '') public_nav();
}
 
function html_foot(): void { ?>
<script src="<?= e(asset('js/site.js')) ?>"></script>
</body>
</html>
<?php }
 
/**
 * Translucent top navigation for the public home page.
 *   About     > Mission & Vision, Org Structure
 *   Community > Members, FAQs, Help Desk
 *   Events
 *   [user icon]
 *
 * NOTE: org-structure.php, members.php and faqs.php are placeholders for the
 * real page names in public/. Change the url(...) values below if they differ.
 */
function public_nav(): void {
    $u = current_user();
    $userHref  = $u ? (is_admin() ? 'admin/index.php' : 'member/index.php') : 'login.php';
    $userLabel = $u ? 'My dashboard' : 'Log in';
    $chev = '<svg class="chev" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>';
    ?>
<style>
.pnav{position:fixed;top:12px;left:50%;transform:translateX(-50%);z-index:1000;width:min(1400px,calc(100% - 48px));
  display:flex;align-items:center;justify-content:space-between;gap:16px;
  padding:7px 20px 7px 16px;font-family:'Poppins',system-ui,sans-serif;
  background:linear-gradient(180deg,rgba(48,55,76,.64),rgba(26,31,48,.5));
  -webkit-backdrop-filter:blur(16px) saturate(160%);backdrop-filter:blur(16px) saturate(160%);
  border:1px solid rgba(255,255,255,.16);border-radius:38px;
  box-shadow:0 22px 44px -16px rgba(0,0,0,.7),0 4px 0 rgba(0,0,0,.28),inset 0 1px 0 rgba(255,255,255,.2),inset 0 -2px 0 rgba(0,0,0,.22);
  transition:background .3s,box-shadow .3s}
.pnav-brand{display:flex;align-items:center;gap:14px;color:#fff;font-weight:700;font-size:19px;letter-spacing:.01em}
.pnav-brand img{width:38px;height:38px;border-radius:50%;object-fit:cover;background:#000;filter:drop-shadow(0 2px 6px rgba(0,0,0,.45))}
.pnav-links{display:flex;align-items:center;gap:6px}
.pnav-links>a,.pnav-dd-btn{color:#fff;background:none;border:0;cursor:pointer;font:500 16px 'Poppins',sans-serif;padding:6px 14px;border-radius:14px;display:inline-flex;align-items:center;gap:6px;transition:background .2s}
.pnav-links>a:hover,.pnav-dd-btn:hover,.pnav-dd.open .pnav-dd-btn{background:rgba(255,255,255,.1)}
.pnav .chev{width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;transition:transform .2s}
.pnav-dd{position:relative}
.pnav-dd.open .chev{transform:rotate(180deg)}
.pnav-menu{position:absolute;top:calc(100% + 14px);left:0;min-width:200px;padding:10px;
  background:rgba(24,28,44,.72);-webkit-backdrop-filter:blur(16px) saturate(160%);backdrop-filter:blur(16px) saturate(160%);
  border:1px solid rgba(255,255,255,.12);border-radius:18px;box-shadow:0 18px 40px -12px rgba(0,0,0,.6);
  opacity:0;visibility:hidden;transform:translateY(-6px);transition:opacity .18s,transform .18s,visibility .18s}
.pnav-dd.open .pnav-menu{opacity:1;visibility:visible;transform:none}
.pnav-menu a{display:block;color:#fff;font-weight:500;font-size:16px;padding:10px 16px;border-radius:12px;white-space:nowrap;transition:background .2s,color .2s}
.pnav-menu a:hover{background:rgba(250,213,23,.14);color:var(--gold)}
.theme-toggle{border:1px solid rgba(255,255,255,.25);background:rgba(255,255,255,.08);color:#fff;border-radius:20px;padding:8px 12px;font:500 13px 'Poppins',sans-serif;cursor:pointer;white-space:nowrap}.theme-toggle:hover{background:rgba(255,255,255,.16)}
.pnav-user{width:38px;height:38px;border-radius:50%;background:rgba(255,255,255,.12);display:grid;place-items:center;margin-left:10px;transition:background .2s}
.pnav-user .ico{width:20px;height:20px;color:#fff}
.pnav-user:hover{background:var(--gold)}
.pnav-user:hover .ico{color:var(--navy)}
.pnav-burger{display:none;background:none;border:0;cursor:pointer;padding:6px;color:#fff}
.pnav-burger .ico{width:28px;height:28px}
.pnav ~ .hero{padding-top:84px}
@media (hover:hover) and (min-width:861px){
  .pnav-dd:hover .pnav-menu{opacity:1;visibility:visible;transform:none}
  .pnav-dd:hover .chev{transform:rotate(180deg)}
}
@media (max-width:860px){
  .pnav{top:10px;width:calc(100% - 20px);border-radius:28px;flex-wrap:wrap;padding:10px 18px}
  .pnav-burger{display:block}
  .pnav-links{display:none;width:100%;flex-direction:column;align-items:stretch;gap:2px;padding:8px 0 6px}
  .pnav.open .pnav-links{display:flex}
  .pnav-menu{position:static;opacity:1;visibility:visible;transform:none;display:none;background:transparent;border:0;box-shadow:none;-webkit-backdrop-filter:none;backdrop-filter:none;padding:0 0 0 14px}
  .pnav-dd.open .pnav-menu{display:block}
  .theme-toggle{width:100%;margin:4px 0;text-align:center}.pnav-user{margin:6px 0 0;border-radius:12px;width:100%;height:44px}
}
</style>
 
<nav class="pnav" id="pnav" aria-label="Main">
  <a class="pnav-brand" href="<?= e(url('index.php')) ?>">
    <img src="<?= e(asset('images/ibits-logo.png')) ?>" alt="iBITS PUPLC logo" width="42" height="42">
    <span>iBITS PUPLC</span>
  </a>
 
  <button class="pnav-burger" type="button" aria-label="Menu" aria-expanded="false" data-pnav-toggle>
    <svg class="ico" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
  </button>
 
  <div class="pnav-links">
    <div class="pnav-dd">
      <button class="pnav-dd-btn" type="button" aria-expanded="false">About <?= $chev ?></button>
      <div class="pnav-menu">
        <a href="<?= e(url('index.php#mission-vision')) ?>">Mission &amp; Vision</a>
        <a href="<?= e(url('org-structure.php')) ?>">Org Structure</a>
      </div>
    </div>
 
    <div class="pnav-dd">
      <button class="pnav-dd-btn" type="button" aria-expanded="false">Community <?= $chev ?></button>
      <div class="pnav-menu">
        <a href="<?= e(url('members.php')) ?>">Members</a>
        <a href="<?= e(url('faqs.php')) ?>">FAQs</a>
        <a href="<?= e(url('member/helpdesk.php')) ?>">Help Desk</a>
      </div>
    </div>
 
    <a href="<?= e(url('index.php#events')) ?>">Events</a>
    <button class="theme-toggle" type="button" data-theme-toggle aria-label="Switch to dark mode" title="Switch to dark mode">☾ <span class="theme-label">Dark mode</span></button>
 
    <a class="pnav-user" href="<?= e(url($userHref)) ?>" aria-label="<?= e($userLabel) ?>" title="<?= e($userLabel) ?>"><?= icon('user') ?></a>
  </div>
</nav>
 
<script>
(function(){
  var nav=document.getElementById('pnav'); if(!nav) return;
  var dds=nav.querySelectorAll('.pnav-dd');
  function closeAll(except){dds.forEach(function(d){if(d!==except){d.classList.remove('open');d.querySelector('.pnav-dd-btn').setAttribute('aria-expanded','false');}});}
  dds.forEach(function(d){
    d.querySelector('.pnav-dd-btn').addEventListener('click',function(e){
      e.stopPropagation(); var open=!d.classList.contains('open'); closeAll(d);
      d.classList.toggle('open',open); this.setAttribute('aria-expanded',open?'true':'false');
    });
  });
  document.addEventListener('click',function(){closeAll(null);});
  document.addEventListener('keydown',function(e){if(e.key==='Escape')closeAll(null);});
  var burger=nav.querySelector('[data-pnav-toggle]');
  burger.addEventListener('click',function(){var o=nav.classList.toggle('open');burger.setAttribute('aria-expanded',o?'true':'false');});
})();
</script>
<?php }
 
/** Footer used on the public pages (home + login + register). */
function public_footer(): void { ?>
<footer class="site-footer" id="footer">
  <div class="container">
    <div class="foot-brand">
      <img src="<?= e(asset('images/ibits-logo.png')) ?>" alt="iBITS logo">
      <div>
        <div class="foot-name">iBITS PORTAL</div>
        <p>Institute of Brilliant Information Technology Students, PUPLC.</p>
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
        $items[] = ['change-password', 'Change Password', 'change-password.php', 'user'];
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
 