<?php
/* Safe helpers: use the site's own url()/asset() when they exist */
$ibU = function (string $p) { return function_exists('url') ? url($p) : '/' . ltrim($p, '/'); };
$ibA = function (string $p) { return function_exists('asset') ? asset($p) : 'assets/' . ltrim($p, '/'); };
$ibE = function ($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); };
?>
<style>
footer#app-footer.ibits-footer{background:#0b1f4d!important;border:0!important}
footer#app-footer.ibits-footer a{color:rgba(219,234,254,.8)!important;cursor:pointer}
footer#app-footer.ibits-footer a:hover{color:#fff!important}
footer#app-footer.ibits-footer .social a:hover{color:#f5c26b!important}
footer#app-footer.ibits-footer .text-white{color:#fff!important}
</style>
<footer class="ibits-footer bg-[#0b1f4d] text-blue-100/80 pt-14 pb-6 mt-auto transition-all" id="app-footer">
<div class="container mx-auto px-6">

<!-- Brand: logo with the institute name beside it -->
<div class="flex items-center gap-5">
<img src="<?= $ibE($ibA('images/ibits-logo.png')) ?>" alt="iBITS logo" class="w-20 h-20 rounded-full shadow-lg shrink-0">
<div>
<div class="text-white font-bold text-2xl leading-tight">iBITS PUPLC</div>
<p class="mt-1 text-sm leading-relaxed max-w-xl">Institute of Brilliant Information Technology Students, PUP Lopez Campus.</p>
</div>
</div>

<!-- Links and information below -->
<div class="mt-10 pt-10 border-t border-blue-300/20 grid grid-cols-2 md:grid-cols-[1fr_1fr_1fr_1.3fr_1.3fr] gap-x-8 gap-y-10 text-left">

<!-- About -->
<div>
<h4 class="font-mono text-xs font-bold tracking-[0.2em] uppercase text-[#f5c26b] mb-5">About</h4>
<ul class="space-y-3 text-base">
<li><a class="transition-colors" href="<?= $ibE($ibU('about')) ?>" data-view="public-about">Who We Are</a></li>
<li><a class="transition-colors" href="<?= $ibE($ibU('about')) ?>#mission" data-view="public-about" data-scroll="#view-public-about .grid">Mission &amp; Vision</a></li>
<li><a class="transition-colors" href="<?= $ibE($ibU('officers')) ?>" data-view="public-org">Officers</a></li>
</ul>
</div>

<!-- Community -->
<div>
<h4 class="font-mono text-xs font-bold tracking-[0.2em] uppercase text-[#f5c26b] mb-5">Community</h4>
<ul class="space-y-3 text-base">
<li><a class="transition-colors" href="<?= $ibE($ibU('members')) ?>" data-view="public-org">Members</a></li>
<li><a class="transition-colors" href="<?= $ibE($ibU('gallery')) ?>">Gallery</a></li>
</ul>
</div>

<!-- Portal -->
<div>
<h4 class="font-mono text-xs font-bold tracking-[0.2em] uppercase text-[#f5c26b] mb-5">Portal</h4>
<ul class="space-y-3 text-base">
<li><a class="transition-colors" href="<?= $ibE($ibU('events')) ?>" data-view="public-events">Events</a></li>
<li><a class="transition-colors" href="<?= $ibE($ibU('get-id')) ?>" data-view="register">Get ID</a></li>
<li><a class="transition-colors" href="<?= $ibE($ibU('login')) ?>" data-view="login">Log in</a></li>
</ul>
</div>

<!-- Address -->
<div>
<h4 class="font-mono text-xs font-bold tracking-[0.2em] uppercase text-[#f5c26b] mb-5">Address</h4>
<p class="text-base leading-relaxed">PUP Lopez Campus, Lopez, Quezon</p>
<a href="https://www.google.com/maps/search/?api=1&query=PUP+Lopez+Quezon+Campus" target="_blank" rel="noopener" class="block mt-4 w-40">
<img src="<?= $ibE($ibA('images/ibits-map.png')) ?>" alt="Map of PUP Lopez Campus" class="w-full h-auto rounded-xl">
</a>
</div>

<!-- Contact -->
<div>
<h4 class="font-mono text-xs font-bold tracking-[0.2em] uppercase text-[#f5c26b] mb-5">Contact</h4>
<a class="text-base transition-colors break-all" href="mailto:ibitspupofficial@gmail.com">ibitspupofficial@gmail.com</a>
</div>

</div>

<!-- Bottom bar -->
<div class="mt-12 pt-6 border-t border-blue-300/20 flex flex-col md:flex-row items-center justify-between gap-4 text-xs font-mono">
<p>© 2026 iBITS Organization. "We Code as One". All rights reserved.</p>
<div class="social flex gap-4 text-xl">
<a class="transition-colors" href="#"><i class="fa-brands fa-facebook"></i></a>
<a class="transition-colors" href="#"><i class="fa-brands fa-github"></i></a>
<a class="transition-colors" href="#"><i class="fa-brands fa-discord"></i></a>
</div>
</div>
</div>
</footer>

<script>
/* Works on both builds: real page links by default; uses navigate() only when the single-page version exists */
(function () {
    document.querySelectorAll('#app-footer a[data-view]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            var v = a.getAttribute('data-view');
            var id = (v === 'register') ? 'login' : v;
            if (typeof navigate === 'function' && document.getElementById('view-' + id)) {
                e.preventDefault();
                navigate(v);
                setTimeout(function () {
                    var box = document.getElementById('content-viewport');
                    var t = a.getAttribute('data-scroll');
                    var el = t ? document.querySelector(t) : null;
                    if (el) { el.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
                    else if (box) { box.scrollTo({ top: 0, behavior: 'smooth' }); }
                }, 80);
            }
        });
    });
})();
</script>