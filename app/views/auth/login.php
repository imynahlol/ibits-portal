<?php
$errors   = array_filter([flash('error')]);
$success  = flash('success');

$old      = $_SESSION['old']['id_number'] ?? '';
$oldRole  = $_SESSION['old']['role'] ?? '';
unset($_SESSION['old']);

$startRole = in_array($oldRole, ['member', 'admin'], true)
    ? $oldRole
    : (($errors || $success) ? 'member' : '');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Login | iBITS Portal</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
          rel="stylesheet">

    <noscript>
        <style>html:not(.ui-ready) body { visibility: visible; }</style>
    </noscript>
</head>

<body data-auth-app
      data-logo="<?= e(asset('images/ibits-logo.png')) ?>"
      data-start-role="<?= e($startRole) ?>">

    <canvas id="rain" aria-hidden="true"></canvas>


    <!-- =========================================================
         NAV
         ========================================================= -->
    <header class="nav">

        <a class="nav-brand" href="<?= e(url('')) ?>">
            <img src="<?= e(asset('images/ibits-logo.png')) ?>" alt="iBITS logo">
            <span>iBITS PUPLC</span>
        </a>

        <nav class="nav-links" aria-label="Main">

            <div class="dd">
                <button type="button" class="dd-btn" aria-expanded="false">
                    About
                    <svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div class="dd-menu">
                    <a href="<?= e(url('about')) ?>">Who we are</a>
                    <a href="<?= e(url('officers')) ?>">Officers</a>
                </div>
            </div>

            <div class="dd">
                <button type="button" class="dd-btn" aria-expanded="false">
                    Community
                    <svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div class="dd-menu">
                    <a href="<?= e(url('members')) ?>">Members</a>
                    <a href="<?= e(url('gallery')) ?>">Gallery</a>
                </div>
            </div>

            <a href="<?= e(url('events')) ?>">Events</a>

            <a class="btn-id" href="<?= e(url('get-id')) ?>">Get ID</a>

            <a class="nav-user" href="<?= e(url('login')) ?>" aria-label="Account">
                <svg viewBox="0 0 24 24">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M4 21c0-4 3.6-6 8-6s8 2 8 6"/>
                </svg>
            </a>
        </nav>
    </header>


    <!-- =========================================================
         STAGE: hero + card
         ========================================================= -->
    <main class="stage">

        <section class="hero">
            <div class="logo-wrap">
                <span class="ring"></span>
                <img class="logo"
                     src="<?= e(asset('images/ibits-logo.png')) ?>"
                     alt="iBITS">
            </div>
            <h1 class="title">iBITS Portal</h1>
            <p class="tag">We Code as One</p>
        </section>


        <section class="card" aria-live="polite">

            <!-- ---------- Step 1: gateway ---------- -->
            <div class="panel" data-panel="gateway">

                <div class="head">
                    <h2>Secure Access</h2>
                    <p>Identify yourself to proceed.</p>
                </div>

                <button type="button" class="choice" data-role="member">
                    <span class="ico">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M4 21c0-4 3.6-6 8-6s8 2 8 6"/>
                        </svg>
                    </span>
                    <span>
                        <b>Member Access</b>
                        <small>Fines, events, forms and help desk</small>
                    </span>
                </button>

                <button type="button" class="choice" data-role="admin">
                    <span class="ico">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 3 4 6v6c0 4.5 3.4 8 8 9 4.6-1 8-4.5 8-9V6l-8-3Z"/>
                            <path d="m8.5 12 2.5 2.5 4.5-5"/>
                        </svg>
                    </span>
                    <span>
                        <b>Admin Console</b>
                        <small>Officers and advisers</small>
                    </span>
                </button>
            </div>


            <!-- ---------- Step 2: credentials ---------- -->
            <div class="panel" data-panel="login" hidden>

                <div class="head">
                    <h2 data-title>Member Access</h2>
                    <p>Enter your credentials to proceed.</p>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success" role="status">
                        <?= e($success) ?>
                    </div>
                <?php endif; ?>

                <?php if ($errors): ?>
                    <div class="alert alert-error" role="alert">
                        <?php foreach ($errors as $er): ?>
                            <div><?= e($er) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= e(url('login')) ?>" class="form">

                    <input type="hidden"
                           name="role"
                           id="user_role"
                           value="<?= e($startRole) ?>">
                    <?= csrf_field() ?>

                    <label for="id_number" data-id-label>
                        Student / Adviser Number
                    </label>
                    <input type="text"
                           id="id_number"
                           name="id_number"
                           value="<?= e($old) ?>"
                           placeholder="Student ID / ID number"
                           autocomplete="username"
                           required>

                    <label for="password">Password</label>
                    <div class="pw">
                        <input type="password"
                               id="password"
                               name="password"
                               placeholder="Password"
                               autocomplete="current-password"
                               required>
                        <button type="button"
                                class="eye"
                                aria-label="Show password">
                            <svg viewBox="0 0 24 24">
                                <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>

                    <div class="row">
                        <label class="chk">
                            <input type="checkbox" name="remember" value="1">
                            Keep Session Active
                        </label>
                        <a href="<?= e(url('forgot-password')) ?>">Forgot password?</a>
                    </div>

                    <button type="submit" class="signin">Sign In</button>
                </form>

                <button type="button" class="back" data-back>
                    &larr; Return to dashboard
                </button>

                <p class="join" data-auth-note>
                    Don't have an account yet?
                    <a href="<?= e(url('register')) ?>">Join iBITS</a>
                </p>
            </div>
        </section>
    </main>


<!-- =============================================================
     CSS (inlined)
     ============================================================= -->
<style>
    :root {
        --navy: #020a1c;
        --navy2: #08215f;
        --gold: #fad517;
        --cream: #fff6d6;
        --card: rgba(88, 74, 20, .92);
        --card2: rgba(255, 255, 255, .08);
        --ink: #fff;
        --mute: #c9cfdd;
        --ease: cubic-bezier(.2, .8, .2, 1);
    }

    * { box-sizing: border-box; margin: 0; }
    html, body { min-height: 100%; }

    body {
        font-family: 'Poppins', system-ui, sans-serif;
        background: radial-gradient(1200px 700px at 30% 40%, #071a44 0, var(--navy) 60%);
        color: var(--ink);
        overflow-x: hidden;
        padding-top: env(safe-area-inset-top, 0);
    }

    #rain {
        position: fixed;
        inset: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        opacity: .55;
    }

    a { color: inherit; text-decoration: none; }

    svg {
        width: 1em;
        height: 1em;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    /* ---------- Nav ---------- */
    .nav {
        position: relative;
        z-index: 5;
        display: flex;
        align-items: center;
        justify-content: space-between;
        max-width: 1400px;
        margin: 8px auto 0;
        padding: 10px 20px;
        background: rgba(36, 42, 60, .88);
        border: 1px solid rgba(255, 255, 255, .1);
        border-radius: 999px;
        backdrop-filter: blur(10px);
        animation: drop .8s var(--ease) both;
    }

    .nav-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 700;
        font-size: 1.05rem;
    }

    .nav-brand img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .nav-links {
        display: flex;
        align-items: center;
        gap: 26px;
        font-weight: 500;
        font-size: 1rem;
    }

    .nav-links > a:hover,
    .dd-btn:hover { color: var(--gold); }

    .dd { position: relative; }

    .dd-btn {
        all: unset;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .dd-btn svg { font-size: .9rem; transition: transform .25s; }
    .dd.open .dd-btn svg { transform: rotate(180deg); }

    .dd-menu {
        position: absolute;
        top: 150%;
        left: -12px;
        min-width: 170px;
        background: #1c2335;
        border: 1px solid rgba(255, 255, 255, .1);
        border-radius: 14px;
        padding: 8px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-6px);
        transition: .25s var(--ease);
    }

    .dd.open .dd-menu {
        opacity: 1;
        visibility: visible;
        transform: none;
    }

    .dd-menu a {
        display: block;
        padding: 9px 12px;
        border-radius: 8px;
        font-size: .92rem;
    }

    .dd-menu a:hover {
        background: rgba(250, 213, 23, .12);
        color: var(--gold);
    }

    .btn-id {
        background: #0a2a86;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 700;
        box-shadow: 0 4px 0 #061a57;
        transition: transform .15s, box-shadow .15s;
    }

    .btn-id:hover {
        transform: translateY(2px);
        box-shadow: 0 2px 0 #061a57;
        color: #fff !important;
    }

    .nav-user {
        display: grid;
        place-items: center;
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .1);
        font-size: 1.15rem;
    }

    /* ---------- Stage ---------- */
    .stage {
        position: relative;
        z-index: 2;
        display: grid;
        grid-template-columns: 1fr 1fr;
        align-items: center;
        gap: 40px;
        max-width: 1300px;
        margin: 0 auto;
        padding: 30px 24px 60px;
        min-height: calc(100vh - 100px);
    }

    .hero { text-align: center; }

    .logo-wrap {
        position: relative;
        width: clamp(220px, 24vw, 320px);
        aspect-ratio: 1;
        margin: 0 auto 14px;
        animation: float 6s ease-in-out infinite;
    }

    .logo {
        position: relative;
        z-index: 1;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        filter: drop-shadow(0 18px 40px rgba(250, 213, 23, .18));
        animation: pop 1s var(--ease) both;
    }

    .ring {
        position: absolute;
        inset: -14px;
        border-radius: 50%;
        border: 2px solid rgba(250, 213, 23, .55);
        animation: pulse 3s ease-out infinite;
    }

    .ring::after {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 50%;
        border: 2px solid rgba(250, 213, 23, .35);
        animation: pulse 3s ease-out 1.5s infinite;
    }

    .title {
        font-size: clamp(2.6rem, 5.5vw, 4.4rem);
        font-weight: 800;
        letter-spacing: -.02em;
        line-height: 1.1;
        background: linear-gradient(100deg,
            #e8c94a 0, #fff6d6 45%, #fff 70%, #e8c94a 100%);
        background-size: 220% 100%;
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        animation: shine 6s linear infinite, rise .9s .2s var(--ease) both;
    }

    .tag {
        margin-top: 6px;
        color: #b7bfe0;
        font-size: 1.25rem;
        letter-spacing: .02em;
        animation: rise .9s .35s var(--ease) both;
    }

    /* ---------- Card ---------- */
    .card {
        width: 100%;
        max-width: 560px;
        justify-self: center;
        background: var(--card);
        border: 1px solid rgba(250, 213, 23, .28);
        border-radius: 34px;
        padding: 38px 40px;
        box-shadow:
            0 30px 70px rgba(0, 0, 0, .45),
            inset 0 1px 0 rgba(255, 255, 255, .12);
        backdrop-filter: blur(12px);
        animation: cardIn .9s .15s var(--ease) both;
    }

    .panel[hidden] { display: none; }
    .panel { animation: swap .45s var(--ease) both; }

    .head {
        border-left: 5px solid var(--gold);
        padding-left: 20px;
        margin-bottom: 26px;
    }

    .head h2 {
        font-size: 1.9rem;
        font-weight: 700;
        text-transform: uppercase;
        line-height: 1.1;
    }

    .head p {
        margin-top: 6px;
        font-size: .9rem;
        font-weight: 500;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: #f1ecd0;
    }

    .choice {
        all: unset;
        box-sizing: border-box;
        width: 100%;
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 22px 26px;
        margin-bottom: 20px;
        cursor: pointer;
        background: var(--card2);
        border-radius: 20px;
        box-shadow:
            0 4px 10px rgba(0, 0, 0, .3),
            inset 0 1px 0 rgba(255, 255, 255, .1);
        transition: transform .25s var(--ease),
                    background .25s,
                    box-shadow .25s;
    }

    .choice:last-child { margin-bottom: 0; }

    .choice:hover,
    .choice:focus-visible {
        transform: translateY(-4px);
        background: rgba(255, 255, 255, .14);
        box-shadow:
            0 14px 26px rgba(0, 0, 0, .4),
            0 0 0 1px rgba(250, 213, 23, .5);
    }

    .choice:active { transform: scale(.98); }

    .choice .ico {
        display: grid;
        place-items: center;
        flex: none;
        width: 60px;
        height: 60px;
        border-radius: 16px;
        background: rgba(255, 255, 255, .1);
        color: var(--gold);
        font-size: 1.6rem;
        transition: transform .35s var(--ease);
    }

    .choice:hover .ico { transform: rotate(-8deg) scale(1.08); }

    .choice b { display: block; font-size: 1.2rem; font-weight: 700; }

    .choice small {
        display: block;
        margin-top: 4px;
        color: #e9e3c0;
        font-size: .95rem;
    }

    /* ---------- Form ---------- */
    .form label {
        display: block;
        font-weight: 600;
        font-size: 1.05rem;
        margin: 0 0 8px;
    }

    .form input[type=text],
    .form input[type=password] {
        width: 100%;
        font: inherit;
        color: #fff;
        padding: 16px 20px;
        margin-bottom: 20px;
        background: rgba(255, 255, 255, .1);
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 14px;
        outline: 0;
        transition: border-color .2s,
                    box-shadow .2s,
                    background .2s;
    }

    .form input::placeholder { color: #b9b49a; }

    .form input:focus {
        border-color: var(--gold);
        background: rgba(255, 255, 255, .14);
        box-shadow: 0 0 0 4px rgba(250, 213, 23, .18);
    }

    .pw { position: relative; }
    .pw input { padding-right: 54px !important; }

    .eye {
        all: unset;
        cursor: pointer;
        position: absolute;
        right: 18px;
        top: 17px;
        font-size: 1.25rem;
        color: #d9d4b8;
    }

    .eye:hover,
    .eye.on { color: var(--gold); }

    .row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: -4px 0 18px;
        font-size: .92rem;
        font-weight: 600;
    }

    .chk {
        display: flex !important;
        align-items: center;
        gap: 10px;
        margin: 0 !important;
        font-size: .92rem !important;
        cursor: pointer;
    }

    .chk input {
        width: 18px;
        height: 18px;
        accent-color: var(--gold);
        margin: 0;
    }

    .row a:hover { color: var(--gold); }

    .signin {
        position: relative;
        overflow: hidden;
        width: 100%;
        font: inherit;
        font-weight: 700;
        font-size: 1.15rem;
        color: #0a1a52;
        padding: 16px;
        border: 0;
        border-radius: 16px;
        cursor: pointer;
        background: var(--gold);
        box-shadow: 0 5px 0 #c9a90a;
        transition: transform .15s, box-shadow .15s;
    }

    .signin::after {
        content: "";
        position: absolute;
        top: 0;
        left: -60%;
        width: 40%;
        height: 100%;
        background: linear-gradient(100deg,
            transparent, rgba(255, 255, 255, .55), transparent);
        transform: skewX(-20deg);
        animation: sweep 3.5s ease-in-out infinite;
    }

    .signin:hover {
        transform: translateY(2px);
        box-shadow: 0 3px 0 #c9a90a;
    }

    .signin:active {
        transform: translateY(5px);
        box-shadow: none;
    }

    .signin.busy {
        pointer-events: none;
        opacity: .8;
    }

    .back {
        all: unset;
        display: block;
        margin: 18px auto 0;
        cursor: pointer;
        font-weight: 500;
        text-align: center;
        transition: color .2s;
    }

    .back:hover { color: var(--gold); }

    .join {
        margin-top: 14px;
        text-align: center;
        font-weight: 500;
    }

    .join a { font-weight: 700; }
    .join a:hover { color: var(--gold); }

    .alert {
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 18px;
        font-size: .92rem;
        font-weight: 500;
    }

    .alert-error {
        background: rgba(190, 30, 45, .25);
        border: 1px solid rgba(255, 110, 110, .6);
        animation: shake .4s;
    }

    .alert-success {
        background: rgba(40, 160, 90, .25);
        border: 1px solid rgba(110, 230, 160, .6);
    }

    :focus-visible {
        outline: 2px solid var(--gold);
        outline-offset: 3px;
    }

    /* ---------- Keyframes ---------- */
    @keyframes drop {
        from { opacity: 0; transform: translateY(-30px); }
    }

    @keyframes rise {
        from { opacity: 0; transform: translateY(24px); }
    }

    @keyframes cardIn {
        from { opacity: 0; transform: translateX(50px) scale(.96); }
    }

    @keyframes swap {
        from { opacity: 0; transform: translateX(24px); }
    }

    @keyframes pop {
        from { opacity: 0; transform: scale(.7) rotate(-12deg); }
    }

    @keyframes float {
        50% { transform: translateY(-12px); }
    }

    @keyframes pulse {
        0%   { transform: scale(.97); opacity: .9; }
        100% { transform: scale(1.22); opacity: 0; }
    }

    @keyframes shine {
        to { background-position: -220% 0; }
    }

    @keyframes sweep {
        0%, 60% { left: -60%; }
        100%    { left: 130%; }
    }

    @keyframes shake {
        25% { transform: translateX(-6px); }
        75% { transform: translateX(6px); }
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 900px) {
        .stage {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .nav-links > a:not(.btn-id):not(.nav-user),
        .dd {
            display: none;
        }

        .card {
            padding: 28px 22px;
            border-radius: 26px;
        }

        .head h2 { font-size: 1.5rem; }
    }

    @media (prefers-reduced-motion: reduce) {
        *,
        *::before,
        *::after {
            animation: none !important;
            transition: none !important;
        }
        #rain { display: none; }
    }
</style>


<!-- =============================================================
     JS (inlined)
     ============================================================= -->
<script>
(function () {
    'use strict';

    var root = document.documentElement;
    root.classList.add('ui-ready');

    var $  = function (s, c) { return (c || document).querySelector(s); };
    var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

    /* ---------- Binary rain ---------- */
    var cv = $('#rain');
    if (cv && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
        var ctx = cv.getContext('2d');
        var cols = [], size = 18, W, H;

        var resize = function () {
            var d = window.devicePixelRatio || 1;
            W = cv.width  = innerWidth  * d;
            H = cv.height = innerHeight * d;
            ctx.setTransform(d, 0, 0, d, 0, 0);
            cols = [];
            for (var i = 0; i < Math.ceil(innerWidth / size); i++) {
                cols.push(Math.random() * -50);
            }
        };

        resize();
        addEventListener('resize', resize);

        var last = 0;
        (function draw(t) {
            requestAnimationFrame(draw);
            if (t - last < 70) return;
            last = t;

            ctx.clearRect(0, 0, innerWidth, innerHeight);
            ctx.font = '14px monospace';

            cols.forEach(function (y, i) {
                for (var k = 0; k < 14; k++) {
                    var yy = (y - k) * size;
                    if (yy < 0 || yy > innerHeight) continue;

                    ctx.fillStyle = k === 0
                        ? 'rgba(250,213,23,.9)'
                        : 'rgba(170,185,230,' + (0.32 - k * 0.022).toFixed(3) + ')';

                    ctx.fillText(Math.random() > .5 ? '1' : '0', i * size, yy);
                }

                cols[i] = (y > innerHeight / size + 14 && Math.random() > .96)
                    ? Math.random() * -20
                    : y + 0.5;
            });
        })(0);
    }

    /* ---------- Nav dropdowns ---------- */
    $$('.dd-btn').forEach(function (b) {
        b.addEventListener('click', function (e) {
            e.stopPropagation();
            var dd = b.parentNode;
            var open = !dd.classList.contains('open');

            $$('.dd').forEach(function (x) {
                x.classList.remove('open');
                $('.dd-btn', x).setAttribute('aria-expanded', 'false');
            });

            if (open) {
                dd.classList.add('open');
                b.setAttribute('aria-expanded', 'true');
            }
        });
    });

    document.addEventListener('click', function () {
        $$('.dd').forEach(function (x) { x.classList.remove('open'); });
    });

    /* ---------- Gateway → login transition ---------- */
    var gateway  = $('[data-panel="gateway"]');
    var login    = $('[data-panel="login"]');
    var roleInput = $('#user_role');
    var title    = $('[data-title]');
    var idLabel  = $('[data-id-label]');
    var idInput  = $('#id_number');

    var copy = {
        member: {
            title: 'Member Access',
            label: 'Student / Adviser Number',
            ph:    'Student ID / ID number'
        },
        admin: {
            title: 'Admin Console',
            label: 'Officer / Adviser Number',
            ph:    'Officer ID / Adviser number'
        }
    };

    function show(role) {
        if (!role) {
            login.hidden = true;
            gateway.hidden = false;
            roleInput.value = '';
            return;
        }

        var c = copy[role];
        roleInput.value = role;
        title.textContent   = c.title;
        idLabel.textContent = c.label;
        idInput.placeholder = c.ph;

        gateway.hidden = true;
        login.hidden = false;

        setTimeout(function () {
            (idInput.value ? $('#password') : idInput).focus({ preventScroll: true });
        }, 250);
    }

    $$('[data-role]').forEach(function (b) {
        b.addEventListener('click', function () { show(b.dataset.role); });
    });

    $('[data-back]').addEventListener('click', function () { show(''); });

    show(document.body.dataset.startRole || '');

    /* ---------- Password toggle ---------- */
    var eye = $('.eye');
    var pw  = $('#password');

    eye.addEventListener('click', function () {
        var on = pw.type === 'password';
        pw.type = on ? 'text' : 'password';
        eye.classList.toggle('on', on);
        eye.setAttribute('aria-label', on ? 'Hide password' : 'Show password');
    });

    /* ---------- Submit feedback ---------- */
    var form = $('.form');
    form.addEventListener('submit', function () {
        var b = $('.signin', form);
        b.classList.add('busy');
        b.textContent = 'Signing in…';
    });

    addEventListener('pageshow', function () {
        var b = $('.signin', form);
        b.classList.remove('busy');
        b.textContent = 'Sign In';
    });
})();
</script>

</body>

</html>