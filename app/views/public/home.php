<!-- PUBLIC HOME / LANDING -->
<div class="view-section active w-full min-h-full" id="view-public-home">

    <!-- ========================================================
         HERO SECTION
         ======================================================== -->
    <section class="zd relative overflow-hidden text-center pt-20 pb-24"
             style="background: #020a1c;">

        <canvas class="rain absolute inset-0 w-full h-full"></canvas>

        <div class="relative z-10 px-6 max-w-4xl mx-auto flex flex-col items-center">

            <!-- iBITS LOGO -->
            <div class="relative mb-0 animate-slide-up">
                <div class="coin-wrap" style="width: min(260px, 76vw);">
                    <div class="coin">
                        <img alt="iBITS Logo"
                             class="w-full h-auto rounded-full relative z-10"
                             style="border: none; box-shadow: none;
                                    filter: drop-shadow(0 18px 30px rgba(10, 36, 99, 0.25));"
                             src="<?= asset('assets/images/ibits-logo.png') ?>" />
                    </div>
                    <div class="absolute left-1/2 -bottom-3 -translate-x-1/2 w-3/4 h-5 rounded-full"
                         style="background: radial-gradient(ellipse at center, rgba(10,36,99,0.25), transparent 70%);
                                transform: translateX(-50%);">
                    </div>
                </div>
            </div>

            <!-- PORTAL BADGE -->
            <span class="px-4 py-1 rounded-full
                         bg-white bg-opacity-10
                         text-secondary text-sm font-semibold tracking-wider
                         mb-6
                         border border-white border-opacity-20
                         animate-slide-up"
                  style="margin-top: -12px;">
                OFFICIAL PORTAL
            </span>

            <!-- MOTTO -->
            <h1 class="text-5xl md:text-7xl font-light text-white mb-6 leading-tight animate-slide-up"
                style="animation-delay: 0.1s;">
                <b class="font-bold">WE CODE AS ONE</b>
            </h1>

            <!-- DESCRIPTION -->
            <p class="text-sm md:text-base text-slate-300 mb-9 max-w-xl mx-auto animate-slide-up"
               style="animation-delay: 0.2s;">
                At iBITS, we unite brilliant minds and passionate builders under<br>
                a shared passion for technology to forge innovation<br>
                together as a single, unified force.
            </p>

            <!-- CTA BUTTONS -->
            <div class="flex gap-4 animate-slide-up" style="animation-delay: 0.3s;">

                <a href="#"
                onclick="navigate('login'); loginMode('register'); return false;"
                class="cta-register px-8 py-3 rounded-full text-lg font-semibold
                        inline-block text-center cursor-pointer">
                    Join the Org
                </a>

                <a href="#"
                onclick="navigate('loginMode'); return false;"
                class="bg-white/10 hover:bg-white/20 text-white
                        border border-white/30
                        px-8 py-3 rounded-full text-lg font-medium
                        backdrop-blur-sm transition-all duration-200
                        inline-block text-center cursor-pointer">
                    Member Login
                </a>
            </div>

        </div>
    </section>


    <!-- ========================================================
         WHO WE ARE (light section)
         ======================================================== -->
    <div class="wp wp-sec relative overflow-hidden py-16">

        <canvas class="rain absolute inset-0 w-full h-full"></canvas>

        <div class="relative z-10 container mx-auto px-6 max-w-5xl">

            <h2 class="text-4xl font-bold text-center mb-4 text-primary">
                Who We Are
            </h2>

            <div class="grid md:grid-cols-2 gap-6 items-center">

                <!-- Copy block -->
                <div class="tilt navy-card p-8">
                    <h3 class="text-2xl font-bold mb-1">
                        We Are More Than an Academic Organization
                    </h3>

                    <p class="text-secondary font-semibold mb-4">
                        A Unified Force for Tech Innovators.
                    </p>

                    <p class="text-sm leading-relaxed text-blue-100">
                        At the <b>Institute of Brilliant Information Technology Students</b>,
                        we unite brilliant minds and passionate builders under a shared
                        love for technology to forge innovation together as a single,
                        unified force.
                    </p>

                    <p class="text-sm mt-3 text-blue-100">
                        We create spaces where students don't just study technology,
                        <b>we code as one.</b>
                    </p>
                </div>

                <!-- iBITS Mascot -->
                <div class="mascot-wrap">
                    <img alt="iBITS Mascot"
                         class="mascot-img"
                         src="<?= asset('assets/images/ibits-mascot.png') ?>" />
                </div>
            </div>
        </div>
    </div>


    <!-- ========================================================
         OUR POPULATION (full-width dark section)
         ======================================================== -->
    <section class="pop-section relative overflow-hidden py-20"
             style="background: #020a1c;">

        <canvas class="rain absolute inset-0 w-full h-full" data-rain="dark"></canvas>

        <div class="relative z-10 container mx-auto px-6 max-w-5xl">

            <h2 class="text-3xl md:text-4xl font-bold text-center mb-3"
                style="color: #ffffff;">
                Our Population
            </h2>

            <p class="text-center text-sm mb-12" style="color: #a9b8d6;">
                Live counts across every section and year level.
            </p>

            <?php
            $bsit = [
                'BSIT 1' => 48,
                'BSIT 2' => 50,
                'BSIT 3' => 49,
                'BSIT 4' => 48,
            ];
            $dit = [
                'DIT 1' => 44,
                'DIT 2' => 40,
                'DIT 3' => 32,
            ];
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


    <!-- ========================================================
         MISSION · VISION · EVENTS (light section)
         ======================================================== -->
    <div class="wp wp-sec relative overflow-hidden py-16">

        <canvas class="rain absolute inset-0 w-full h-full"></canvas>

        <div class="relative z-10 container mx-auto px-6 max-w-5xl">

            <!-- ---------- MISSION & VISION ---------- -->
            <h2 class="text-4xl font-bold text-center mb-12 text-primary">
                Mission &amp; Vision
            </h2>

            <div class="grid md:grid-cols-2 gap-12">

                <!-- Mission -->
                <div class="about-mv-card p-8 rounded-3xl shadow-sm
                            border border-gray-100 animate-slide-up"
                     style="animation-delay: 0.1s;
                            background: #ffffff;
                            color: #1f2937;">

                    <div class="about-mv-icon about-mv-icon-navy
                                w-14 h-14 rounded-xl
                                flex items-center justify-center">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9"/>
                            <circle cx="12" cy="12" r="5"/>
                            <circle cx="12" cy="12" r="1.5"/>
                        </svg>
                    </div>

                    <h3 style="color: #06215c; font-weight: 700; font-size: 1.875rem;">
                        Our Mission
                    </h3>

                    <p style="color: #475569; line-height: 1.7;">
                        To foster a collaborative environment where IT students can
                        develop technical excellence, leadership skills, and ethical
                        professionalism through innovative projects and community
                        engagement.
                    </p>
                </div>

                <!-- Vision -->
                <div class="about-mv-card p-8 rounded-3xl shadow-sm
                            border border-gray-100 animate-slide-up"
                     style="animation-delay: 0.2s;
                            background: #ffffff;
                            color: #1f2937;">

                    <div class="about-mv-icon about-mv-icon-gold
                                w-14 h-14 rounded-xl
                                flex items-center justify-center">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </div>

                    <h3 style="color: #06215c; font-weight: 700; font-size: 1.875rem;">
                        Our Vision
                    </h3>

                    <p style="color: #475569; line-height: 1.7;">
                        To be the leading student organization that produces globally
                        competitive technology professionals who drive digital
                        transformation and social progress.
                    </p>
                </div>
            </div>

            <!-- ---------- EVENTS ---------- -->
            <h2 class="text-4xl font-bold text-center mt-20 mb-8 text-primary">
                Events
            </h2>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5" id="home-events"></div>

        </div>
    </div>

</div>


<!-- ============================================================
     INLINE CSS
     ============================================================ -->
<style>
    /* ---------- Binary rain canvas ---------- */
    canvas.rain {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        display: block;
    }

    /* ---------- CTA: "Join the Org" button ---------- */
    .cta-register {
        position: relative;
        overflow: hidden;
        background: #06215c;
        color: #ffffff;
        box-shadow: 0 4px 0 #031236, 0 8px 20px rgba(6, 33, 92, .35);
        transition: transform .15s ease, box-shadow .15s ease, background .2s ease;
    }

    .cta-register:hover {
        background: #0a328c;
        transform: translateY(-2px);
        box-shadow: 0 6px 0 #031236, 0 12px 24px rgba(6, 33, 92, .45);
    }

    .cta-register:active {
        transform: translateY(3px);
        box-shadow: 0 1px 0 #031236, 0 2px 6px rgba(6, 33, 92, .5);
    }

    .cta-register::before {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: inherit;
        background: radial-gradient(circle at center, rgba(250, 213, 23, .35), transparent 60%);
        opacity: 0;
        transition: opacity .3s ease;
        pointer-events: none;
    }

    .cta-register:hover::before { opacity: 1; }

    .cta-register .cta-ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, .55);
        transform: scale(0);
        animation: cta-ripple .55s ease-out forwards;
        pointer-events: none;
    }

    @keyframes cta-ripple {
        to { transform: scale(4); opacity: 0; }
    }

    /* ---------- iBITS Mascot ---------- */
    .mascot-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        perspective: 900px;
    }

    .mascot-img {
        display: block;
        width: 100%;
        max-width: 480px;
        height: auto;
        object-fit: contain;
        transform-style: preserve-3d;
        animation: mascot-coin 9s ease-in-out infinite;
        filter: drop-shadow(0 18px 30px rgba(6, 33, 92, 0.25));
        will-change: transform;
    }

    @keyframes mascot-coin {
        0%, 100% { transform: rotateY(-20deg) rotateX(6deg); }
        50%      { transform: rotateY(20deg) rotateX(-4deg); }
    }

    @media (prefers-reduced-motion: reduce) {
        .mascot-img { animation: none; }
    }

    /* ---------- Mission & Vision cards ---------- */
    .about-mv-card {
        display: grid;
        grid-template-columns: auto 1fr;
        align-items: center;
        column-gap: 18px;
        position: relative;
        overflow: hidden;
        transition: transform .35s cubic-bezier(.2, .7, .2, 1),
                    box-shadow .35s,
                    border-color .35s;
    }

    .about-mv-card:hover {
        transform: translateY(-4px);
        border-color: rgba(250, 213, 23, .55);
        box-shadow: 0 10px 25px -5px rgba(6, 33, 92, .1),
                    0 8px 10px -6px rgba(6, 33, 92, .1);
    }

    .about-mv-card::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, #fad517, #fff4a8);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform .5s cubic-bezier(.2, .7, .2, 1);
    }

    .about-mv-card:hover::before { transform: scaleX(1); }

    .about-mv-card::after {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(115deg,
            transparent 40%,
            rgba(250, 213, 23, .13) 50%,
            transparent 60%);
        transform: translateX(-120%);
        transition: transform .9s cubic-bezier(.2, .7, .2, 1);
    }

    .about-mv-card:hover::after { transform: translateX(120%); }

    .about-mv-icon {
        margin: 0;
        box-shadow: 0 4px 0 rgba(0, 0, 0, .18);
        transition: transform .35s cubic-bezier(.2, .7, .2, 1);
        animation: about-mv-float 5s ease-in-out infinite;
    }

    .about-mv-icon svg { transition: transform .35s; }

    .about-mv-card:hover .about-mv-icon {
        transform: rotate(-8deg) scale(1.08);
    }

    .about-mv-card:hover .about-mv-icon svg {
        transform: scale(1.12);
    }

    @keyframes about-mv-float {
        0%, 100% { translate: 0 0; }
        50%      { translate: 0 -4px; }
    }

    .about-mv-icon-navy {
        background: linear-gradient(135deg, #06215c, #0a328c);
        color: #fad517;
    }

    .about-mv-icon-gold {
        background: linear-gradient(135deg, #fad517, #ffe86b);
        color: #06215c;
        animation-delay: -2.5s;
    }

    .about-mv-card h3 {
        margin: 0;
        transition: color .3s;
    }

    .about-mv-card p {
        grid-column: 1 / -1;
        margin-top: 22px;
    }

    /* Animated gold underline under section headings */
    .wp-sec h2.text-4xl.text-primary::after {
        content: "";
        display: block;
        width: 64px;
        height: 4px;
        margin: 8px auto 0;
        border-radius: 4px;
        background: linear-gradient(90deg, #fad517, #fff4a8);
        animation: about-mv-grow .8s .35s cubic-bezier(.2, .7, .2, 1) both;
    }

    @keyframes about-mv-grow {
        from { transform: scaleX(0); }
        to   { transform: scaleX(1); }
    }

    @media (prefers-reduced-motion: reduce) {
        .about-mv-icon { animation: none; }
        .about-mv-card * { transition-duration: .01ms !important; }
        .wp-sec h2.text-4xl.text-primary::after { animation: none; }
    }

    /* =============================================================
       Population summary — full-width dark section
       ============================================================= */
    .pop-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    @media (min-width: 768px) {
        .pop-grid { grid-template-columns: repeat(4, 1fr); }
        .pop-grid-3 { grid-template-columns: repeat(3, 1fr); }
    }

    /* Base card */
    .pop-card {
        position: relative;
        overflow: hidden;
        padding: 24px 22px;
        border-radius: 16px;
        transition: transform .3s ease,
                    border-color .3s ease,
                    box-shadow .3s ease;
    }

    /* Dark variant — individual year levels */
    .pop-card-dark {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(4px);
    }

    .pop-card-dark .pop-num {
        color: #ffffff;
    }

    .pop-card-dark .pop-label {
        color: #a9b8d6;
    }

    .pop-card-dark:hover {
        transform: translateY(-4px);
        border-color: rgba(250, 213, 23, 0.55);
        box-shadow: 0 16px 32px -12px rgba(250, 213, 23, 0.2);
    }

    /* Primary variant — summary row */
    .pop-card-primary {
        background: linear-gradient(135deg, #06215c 0%, #0a328c 100%);
        border: 1px solid rgba(250, 213, 23, 0.25);
        box-shadow: 0 8px 24px -8px rgba(6, 33, 92, 0.55);
    }

    .pop-card-primary .pop-num {
        color: #fad517;
    }

    .pop-card-primary .pop-label {
        color: #cbd5e1;
    }

    .pop-card-primary:hover {
        transform: translateY(-4px);
        border-color: rgba(250, 213, 23, 0.6);
        box-shadow: 0 16px 32px -10px rgba(250, 213, 23, 0.35);
    }

    /* Gold top bar slides in on hover */
    .pop-card::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, #fad517, rgba(250, 213, 23, 0));
        transform: scaleX(0);
        transform-origin: left;
        transition: transform .5s cubic-bezier(.2, .7, .2, 1);
    }

    .pop-card:hover::before { transform: scaleX(1); }

    .pop-num {
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -0.02em;
        margin-bottom: 8px;
        font-variant-numeric: tabular-nums;
    }

    @media (min-width: 768px) {
        .pop-num { font-size: 3rem; }
    }

    .pop-label {
        font-size: 0.8rem;
        letter-spacing: 0.02em;
        line-height: 1.4;
    }

    /* Subheadings between groups */
    .pop-subhead {
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: #a9b8d6;
        margin: 36px 0 16px 0;
    }

    @media (prefers-reduced-motion: reduce) {
        .pop-card { transition: none; }
    }
</style>


<!-- ============================================================
     BINARY RAIN + CTA RIPPLE + COUNTERS
     ============================================================ -->
<script>
(function () {
    'use strict';

    var RM = matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ---------- Binary rain ---------- */
    function drawRain() {
        var canvases = document.querySelectorAll('canvas.rain');
        for (var i = 0; i < canvases.length; i++) {
            var c = canvases[i];

            if (!c.offsetParent) continue;

            var w = c.width  = c.clientWidth;
            var h = c.height = c.clientHeight;
            if (w === 0 || h === 0) continue;

            var f = 18;
            var g = c.getContext('2d');

            if (!c._h || c._w !== w) {
                c._w = w;
                c._h = [];
                var cols = Math.ceil(w / f);
                for (var n = 0; n < cols; n++) {
                    c._h.push(Math.random() * h / f);
                }
            }

            g.clearRect(0, 0, w, h);
            g.font = (f - 4) + 'px monospace';

            /* Respect data-rain attribute; fallback to .wp detection */
            var isLight;
            if (c.dataset.rain === 'dark') {
                isLight = false;
            } else if (c.dataset.rain === 'light') {
                isLight = true;
            } else {
                isLight = !!c.closest('.wp');
            }

            for (var col = 0; col < c._h.length; col++) {
                var y = c._h[col];

                for (var k = 0; k < 7; k++) {
                    var isGoldHead = (k === 0 && col % 9 === 0);

                    if (isGoldHead) {
                        g.fillStyle = 'rgba(250, 213, 23, ' + (isLight ? 0.45 : 0.5) + ')';
                    } else if (isLight) {
                        g.fillStyle = 'rgba(6, 33, 92, ' + (0.12 - k * 0.015) + ')';
                    } else {
                        g.fillStyle = 'rgba(190, 205, 235, ' + (0.3 - k * 0.04) + ')';
                    }

                    var digit = ((col * 7 + k + Math.floor(y)) % 3) ? 1 : 0;
                    g.fillText(digit, col * f, (y - k) * f);
                }

                if (!RM) {
                    c._h[col] = (y * f > h + 120)
                        ? -Math.random() * 20
                        : y + 0.35;
                }
            }
        }
    }

    /* ---------- CTA click ripple ---------- */
    function bindCtaRipples() {
        document.querySelectorAll('.cta-register').forEach(function (btn) {
            if (btn.dataset.rippleBound) return;
            btn.dataset.rippleBound = '1';

            btn.addEventListener('pointerdown', function (e) {
                var rect = btn.getBoundingClientRect();
                var size = Math.max(rect.width, rect.height) / 2;
                var ripple = document.createElement('span');
                ripple.className = 'cta-ripple';
                ripple.style.cssText =
                    'width:'  + size + 'px;' +
                    'height:' + size + 'px;' +
                    'left:'   + (e.clientX - rect.left - size / 2) + 'px;' +
                    'top:'    + (e.clientY - rect.top  - size / 2) + 'px;';
                btn.appendChild(ripple);
                setTimeout(function () { ripple.remove(); }, 600);
            });
        });
    }

    /* ---------- Population counters ---------- */
    function animateCounters() {
        var counters = document.querySelectorAll('.pop-num[data-target]');
        for (var i = 0; i < counters.length; i++) {
            (function (el) {
                if (el.dataset.animated) return;
                el.dataset.animated = '1';

                var target = parseInt(el.dataset.target, 10) || 0;
                var duration = 1500;
                var startTime = performance.now();

                function tick(now) {
                    var elapsed = now - startTime;
                    var progress = Math.min(elapsed / duration, 1);
                    var eased = 1 - Math.pow(1 - progress, 3);
                    var value = Math.round(target * eased);
                    el.textContent = value.toLocaleString();

                    if (progress < 1) {
                        requestAnimationFrame(tick);
                    } else {
                        el.textContent = target.toLocaleString();
                    }
                }
                requestAnimationFrame(tick);
            })(counters[i]);
        }
    }

    function setupPopObserver() {
        var section = document.querySelector('.pop-section');
        if (!section) return;

        if (!('IntersectionObserver' in window)) {
            animateCounters();
            return;
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.disconnect();
                }
            });
        }, { threshold: 0.15 });

        observer.observe(section);

        /* Safety net: if already visible on load */
        setTimeout(function () {
            var rect = section.getBoundingClientRect();
            if (rect.top < window.innerHeight && rect.bottom > 0) {
                animateCounters();
                observer.disconnect();
            }
        }, 400);
    }

    /* ---------- Boot ---------- */
    function start() {
        drawRain();
        setInterval(drawRain, 90);
        bindCtaRipples();
        setupPopObserver();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();
</script>