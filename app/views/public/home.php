<!-- ============================================================
     PUBLIC HOME / LANDING
     ============================================================ -->
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
                <div class="coin-wrap" style="width: min(360px, 76vw);">
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

                <a href="<?= asset('/register') ?>"
                   class="cta-register px-8 py-3 rounded-full text-lg font-semibold
                          inline-block text-center">
                    Join the Org
                </a>

                <a href="<?= asset('/login') ?>"
                   class="bg-white/10 hover:bg-white/20 text-white
                          border border-white/30
                          px-8 py-3 rounded-full text-lg font-medium
                          backdrop-blur-sm transition-all duration-200
                          inline-block text-center">
                    Member Login
                </a>
            </div>

        </div>
    </section>


    <!-- ========================================================
         WHO WE ARE · MISSION · VISION · EVENTS
         ======================================================== -->
    <div class="wp wp-sec relative overflow-hidden py-16">

        <canvas class="rain absolute inset-0 w-full h-full"></canvas>

        <div class="relative z-10 container mx-auto px-6 max-w-5xl">

            <!-- ---------- Who We Are ---------- -->
            <h2 class="text-4xl font-bold text-center mb-10 text-primary">
                Who We Are
            </h2>

            <div class="grid md:grid-cols-2 gap-10 items-center">

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

                <!-- Logo coin -->
                <div class="coin-wrap">
                    <div class="coin">
                        <img alt="iBITS Logo"
                             class="logo-img"
                             src="<?= asset('assets/images/ibits-logo.png') ?>" />
                    </div>
                </div>
            </div>


            <!-- ---------- Mission & Vision ---------- -->
            <h2 class="text-4xl font-bold text-center mt-20 mb-12 text-primary">
                Mission &amp; Vision
            </h2>

            <div class="grid md:grid-cols-2 gap-12">

                <!-- Mission -->
                <div class="about-mv-card bg-surface p-8 rounded-3xl shadow-sm
                            border border-gray-100 animate-slide-up"
                     style="animation-delay: 0.1s;">

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

                    <h3 class="text-2xl font-bold text-primary">Our Mission</h3>

                    <p class="text-textMuted leading-relaxed">
                        To foster a collaborative environment where IT students
                        can develop technical excellence, leadership skills, and
                        ethical professionalism through innovative projects and
                        community engagement.
                    </p>
                </div>

                <!-- Vision -->
                <div class="about-mv-card bg-surface p-8 rounded-3xl shadow-sm
                            border border-gray-100 animate-slide-up"
                     style="animation-delay: 0.2s;">

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

                    <h3 class="text-2xl font-bold text-primary">Our Vision</h3>

                    <p class="text-textMuted leading-relaxed">
                        To be the leading student organization that produces
                        globally competitive technology professionals who drive
                        digital transformation and social progress.
                    </p>
                </div>
            </div>


            <!-- ---------- Events ---------- -->
            <h2 class="text-4xl font-bold text-center mt-20 mb-8 text-primary">
                Events
            </h2>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5" id="home-events"></div>

        </div>
    </div>

</div>


<!-- ============================================================
     INLINE CSS — rain canvas + Mission/Vision cards
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

    /* Top gold bar slides in on hover */
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

    /* Diagonal shine sweep */
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

    /* Floating icon */
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

    /* Icon variants */
    .about-mv-icon-navy {
        background: linear-gradient(135deg, #06215c, #0a328c);
        color: #fad517;
    }

    .about-mv-icon-gold {
        background: linear-gradient(135deg, #fad517, #ffe86b);
        color: #06215c;
        animation-delay: -2.5s;
    }

    /* Title + paragraph layout */
    .about-mv-card h3 {
        margin: 0;
        transition: color .3s;
    }

    .about-mv-card:hover h3 { color: #06215c; }
    html.dark .about-mv-card:hover h3 { color: #fad517; }

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
        margin: 16px auto 0;
        border-radius: 4px;
        background: linear-gradient(90deg, #fad517, #fff4a8);
        animation: about-mv-grow .8s .35s cubic-bezier(.2, .7, .2, 1) both;
    }

    @keyframes about-mv-grow {
        from { transform: scaleX(0); }
        to   { transform: scaleX(1); }
    }

    /* Reduced motion */
    @media (prefers-reduced-motion: reduce) {
        .about-mv-icon { animation: none; }
        .about-mv-card * { transition-duration: .01ms !important; }
        .wp-sec h2.text-4xl.text-primary::after { animation: none; }
    }
</style>


<!-- ============================================================
     BINARY RAIN + CTA RIPPLE — inline scripts
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

            var isLight = !!c.closest('.wp');

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

    /* ---------- Boot ---------- */
    function start() {
        drawRain();
        setInterval(drawRain, 90);
        bindCtaRipples();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();
</script>