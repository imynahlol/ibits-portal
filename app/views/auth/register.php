<?php
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

$v     = fn($k) => e($old[$k] ?? '');
$sel   = fn($k, $val) => (($old[$k] ?? '') === (string)$val) ? 'selected' : '';
$oldPY = trim(($old['program'] ?? '') . ' ' . ($old['year_level'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Register | iBITS Portal</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
          rel="stylesheet">

    <style>
        /* =========================================================
           Base
           ========================================================= */
        :root {
            --navy:  #020a1c;
            --royal: #06205f;
            --gold:  #fad517;
            --ink:   #f4f1e4;
            --muted: #b9bfd0;
            --ok:    #22c55e;
            --bad:   #ff6b6b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body { min-height: 100%; }

        body {
            font-family: 'Poppins', system-ui, sans-serif;
            background: radial-gradient(1200px 700px at 30% 40%, #071a45 0, var(--navy) 60%);
            color: var(--ink);
            overflow-x: hidden;
            padding: calc(110px + env(safe-area-inset-top, 0px)) 24px 48px;
        }

        #rain {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        /* =========================================================
           Navbar
           ========================================================= */
        .nav {
            position: fixed;
            top: calc(12px + env(safe-area-inset-top, 0px));
            left: 50%;
            transform: translateX(-50%);
            width: min(1400px, calc(100% - 32px));
            height: 72px;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            background: rgba(38, 44, 62, .82);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 999px;
            animation: drop .7s cubic-bezier(.2, .8, .2, 1) both;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .brand img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .links {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .links a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: color .2s;
        }

        .links a:hover { color: var(--gold); }

        .links svg {
            width: 12px;
            height: 12px;
        }

        .links .getid {
            background: #07236b;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 700;
            box-shadow: 0 4px 0 #03143f;
            transition: transform .15s, box-shadow .15s;
        }

        .links .getid:hover {
            color: #fff;
            transform: translateY(2px);
            box-shadow: 0 2px 0 #03143f;
        }

        .links .user {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .12);
            justify-content: center;
        }

        .links .user svg {
            width: 18px;
            height: 18px;
        }

        /* =========================================================
           Layout
           ========================================================= */
        .stage {
            position: relative;
            z-index: 1;
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr minmax(380px, 560px);
            gap: 48px;
            align-items: center;
            min-height: calc(100vh - 170px);
        }

        .hero { text-align: center; }

        .hero .logo {
            width: min(320px, 70vw);
            aspect-ratio: 1;
            border-radius: 50%;
            object-fit: cover;
            box-shadow:
                0 0 0 3px rgba(255, 255, 255, .85),
                0 0 60px rgba(250, 213, 23, .28);
            animation:
                float 5s ease-in-out infinite,
                pop 1s cubic-bezier(.2, 1.2, .3, 1) both;
        }

        .hero h1 {
            margin-top: 22px;
            font-size: clamp(2.6rem, 6vw, 4.6rem);
            font-weight: 700;
            line-height: 1.05;
            background: linear-gradient(90deg, #f3dc7c, #fff 75%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: rise .9s .25s both;
        }

        .hero p {
            margin-top: 14px;
            font-size: 1.25rem;
            color: var(--muted);
            font-weight: 300;
            animation: rise .9s .4s both;
        }

        /* =========================================================
           Card
           ========================================================= */
        .card {
            position: relative;
            padding: 40px;
            border-radius: 44px 44px 36px 36px;
            background: linear-gradient(160deg,
                rgba(250, 213, 23, .34),
                rgba(250, 213, 23, .22));
            border: 1px solid rgba(250, 213, 23, .28);
            box-shadow: 0 30px 80px rgba(0, 0, 0, .45);
            backdrop-filter: blur(8px);
            animation: cardIn .9s .15s cubic-bezier(.2, .8, .2, 1) both;
            overflow: hidden;
        }

        .card::after {
            content: "";
            position: absolute;
            top: 0;
            left: -60%;
            width: 40%;
            height: 100%;
            background: linear-gradient(100deg,
                transparent,
                rgba(255, 255, 255, .12),
                transparent);
            transform: skewX(-20deg);
            animation: sheen 1.6s 1s ease-out 1 both;
        }

        .head {
            border-left: 4px solid var(--gold);
            padding-left: 22px;
            margin-bottom: 30px;
        }

        .head h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -.5px;
            line-height: 1.1;
        }

        .head span {
            display: block;
            margin-top: 4px;
            font-size: .88rem;
            font-weight: 500;
            letter-spacing: 2px;
            color: #fff;
            text-transform: uppercase;
        }

        .alert {
            background: rgba(255, 107, 107, .15);
            border: 1px solid var(--bad);
            color: #ffd6d6;
            border-radius: 14px;
            padding: 12px 18px;
            margin-bottom: 18px;
            font-size: .9rem;
        }

        .alert ul { padding-left: 18px; }

        /* =========================================================
           Form fields
           ========================================================= */
        .fld {
            position: relative;
            margin-bottom: 16px;
            animation: rise .6s both;
        }

        .fld:nth-of-type(1) { animation-delay: .35s; }
        .fld:nth-of-type(2) { animation-delay: .42s; }
        .fld:nth-of-type(3) { animation-delay: .49s; }
        .fld:nth-of-type(4) { animation-delay: .56s; }
        .fld:nth-of-type(5) { animation-delay: .63s; }
        .fld:nth-of-type(6) { animation-delay: .70s; }

        .fld input,
        .fld select {
            width: 100%;
            height: 58px;
            padding: 0 54px 0 20px;
            font: 500 1.05rem 'Poppins', sans-serif;
            color: #fff;
            background: rgba(255, 255, 255, .1);
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 16px;
            outline: 0;
            transition: border-color .25s,
                        background .25s,
                        box-shadow .25s,
                        transform .25s;
            appearance: none;
        }

        .fld select {
            background-image:
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='none' stroke='%23e8e2b8' stroke-width='2'%3E%3Cpath d='M2 4l5 5 5-5'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 22px center;
        }

        .fld select option { color: #111; }
        .fld input::placeholder { color: #d9d3ae; }
        .fld select:invalid { color: #d9d3ae; }

        .fld input:focus,
        .fld select:focus {
            border-color: var(--gold);
            background: rgba(255, 255, 255, .16);
            box-shadow: 0 0 0 4px rgba(250, 213, 23, .18);
            transform: translateY(-1px);
        }

        .fld.err input,
        .fld.err select { border-color: var(--bad); }

        .hint {
            display: block;
            margin: -8px 4px 14px;
            font-size: .78rem;
            color: #e6dfb4;
        }

        .eye {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: 0;
            color: #e8e2b8;
            cursor: pointer;
            padding: 8px;
            display: grid;
            place-items: center;
            transition: color .2s;
        }

        .eye:hover { color: #fff; }
        .eye svg { width: 20px; height: 20px; }

        /* =========================================================
           Password rules
           ========================================================= */
        .rules {
            list-style: none;
            margin: 4px 0 20px;
            font-size: .88rem;
            color: #e6dfb4;
        }

        .rules li {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 2px 0;
            transition: color .3s, transform .3s;
        }

        .rules li::before {
            content: "○";
            font-size: .7rem;
        }

        .rules li.ok {
            color: var(--ok);
            transform: translateX(4px);
        }

        .rules li.ok::before {
            content: "✓";
            font-size: .9rem;
        }

        /* =========================================================
           Submit button
           ========================================================= */
        .btn {
            width: 100%;
            height: 62px;
            border: 0;
            border-radius: 16px;
            background: var(--gold);
            color: #07236b;
            font: 700 1.15rem 'Poppins', sans-serif;
            cursor: pointer;
            box-shadow: 0 5px 0 #c9a700;
            transition: transform .15s, box-shadow .15s, filter .2s;
            position: relative;
            overflow: hidden;
        }

        .btn:hover {
            filter: brightness(1.06);
            transform: translateY(-2px);
            box-shadow: 0 7px 0 #c9a700;
        }

        .btn:active {
            transform: translateY(3px);
            box-shadow: 0 2px 0 #c9a700;
        }

        .btn[disabled] {
            opacity: .7;
            cursor: wait;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 22px;
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            transition: letter-spacing .25s, color .25s;
        }

        .back:hover {
            color: var(--gold);
            letter-spacing: .5px;
        }

        /* =========================================================
           Keyframes
           ========================================================= */
        @keyframes drop {
            from { opacity: 0; transform: translate(-50%, -30px); }
            to   { opacity: 1; transform: translate(-50%, 0); }
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(22px); }
            to   { opacity: 1; transform: none; }
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateX(60px) scale(.96); }
            to   { opacity: 1; transform: none; }
        }

        @keyframes pop {
            from { opacity: 0; transform: scale(.6) rotate(-12deg); }
            to   { opacity: 1; }
        }

        @keyframes float {
            50% { transform: translateY(-12px); }
        }

        @keyframes sheen {
            to { left: 140%; }
        }

        @keyframes shake {
            20%, 60% { transform: translateX(-8px); }
            40%, 80% { transform: translateX(8px); }
        }

        .shake { animation: shake .45s; }

        /* =========================================================
           Responsive
           ========================================================= */
        @media (max-width: 980px) {
            .stage {
                grid-template-columns: 1fr;
                gap: 32px;
                min-height: 0;
            }
            .hero .logo { width: 200px; }
            .links a:not(.getid):not(.user) { display: none; }
            .nav { padding: 0 18px; }
        }

        @media (max-width: 520px) {
            body { padding-inline: 14px; }
            .card { padding: 28px 20px; }
            .brand span { font-size: 1rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::after {
                animation: none !important;
                transition: none !important;
            }
        }
    </style>
</head>

<body>

    <canvas id="rain" aria-hidden="true"></canvas>


    <!-- =========================================================
         NAV
         ========================================================= -->
    <header class="nav">

        <a class="brand" href="<?= e(url('/')) ?>">
            <img src="<?= e(asset('images/ibits-logo.png')) ?>" alt="iBITS logo">
            <span>iBITS PUPLC</span>
        </a>

        <nav class="links">
            <a href="<?= e(url('/')) ?>#about">
                About
                <svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path d="M2 4l4 4 4-4"/>
                </svg>
            </a>
            <a href="<?= e(url('/')) ?>#community">
                Community
                <svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path d="M2 4l4 4 4-4"/>
                </svg>
            </a>
            <a href="<?= e(url('/')) ?>#events">Events</a>
            <a class="getid" href="<?= e(url('register')) ?>">Get ID</a>
            <a class="user" href="<?= e(url('login')) ?>" aria-label="Sign in">
                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>
                </svg>
            </a>
        </nav>
    </header>


    <!-- =========================================================
         STAGE: hero + card
         ========================================================= -->
    <main class="stage">

        <section class="hero">
            <img class="logo"
                 src="<?= e(asset('images/ibits-logo.png')) ?>"
                 alt="iBITS">
            <h1>iBITS Portal</h1>
            <p>We Code as One</p>
        </section>


        <section class="card">

            <div class="head">
                <h2>REGISTER ACCESS</h2>
                <span>Create your member account.</span>
            </div>

            <?php if ($errors): ?>
                <div class="alert" role="alert">
                    <ul>
                        <?php foreach ($errors as $er): ?>
                            <li><?= e($er) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>


            <form method="POST"
                  action="<?= e(url('register')) ?>"
                  id="registerForm"
                  novalidate>

                <?= csrf_field() ?>

                <!-- Full Name -->
                <div class="fld">
                    <input type="text"
                           name="full_name"
                           placeholder="Full Name"
                           value="<?= $v('full_name') ?>"
                           maxlength="100"
                           autocomplete="name"
                           required>
                </div>

                <!-- ID Number -->
                <div class="fld">
                    <input type="text"
                           name="id_number"
                           id="id_number"
                           placeholder="Student ID / ID number"
                           value="<?= $v('id_number') ?>"
                           pattern="(\d{4}-\d{5}-LQ-\d)|(\d{5})"
                           title="Students: 20XX-XXXXX-LQ-X · Advisers: XXXXX"
                           inputmode="text"
                           required>
                </div>

                <!-- Sex -->
                <div class="fld">
                    <select name="sex" required>
                        <option value="" disabled <?= ($old['sex'] ?? '') === '' ? 'selected' : '' ?>>
                            Sex
                        </option>
                        <option value="Female"     <?= $sel('sex', 'Female') ?>>Female</option>
                        <option value="Male"       <?= $sel('sex', 'Male') ?>>Male</option>
                        <option value="Non-Binary" <?= $sel('sex', 'Non-Binary') ?>>Non-Binary</option>
                    </select>
                </div>

                <!-- Program & Year (single visible input + hidden fields) -->
                <div class="fld" id="pyWrap">
                    <input type="text"
                           id="py"
                           placeholder="Program &amp; Year (e.g. BSIT 2)"
                           value="<?= e($oldPY) ?>"
                           autocomplete="off"
                           maxlength="12">
                    <input type="hidden" name="program"    id="program"    value="<?= $v('program') ?>">
                    <input type="hidden" name="year_level" id="year_level" value="<?= $v('year_level') ?>">
                </div>
                <small class="hint" id="pyHint" hidden>Use BSIT 1–4 or DIT 1–3.</small>

                <!-- Password -->
                <div class="fld">
                    <input type="password"
                           name="password"
                           id="pw"
                           placeholder="Password"
                           autocomplete="new-password"
                           minlength="8"
                           required>
                    <button type="button"
                            class="eye"
                            data-eye="pw"
                            aria-label="Show password">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>

                <!-- Live password rules -->
                <ul class="rules" id="rules">
                    <li data-r="len">At least 8 characters</li>
                    <li data-r="up">At least one uppercase letter (A-Z)</li>
                    <li data-r="lo">At least one lowercase letter (a-z)</li>
                    <li data-r="num">At least one number (0-9)</li>
                    <li data-r="sp">At least one special character (!@#$%^&amp;*)</li>
                    <li data-r="ns" class="ok">No spaces in between</li>
                </ul>

                <!-- Confirm password -->
                <div class="fld">
                    <input type="password"
                           name="password_confirm"
                           id="pw2"
                           placeholder="Confirm Password"
                           autocomplete="new-password"
                           minlength="8"
                           required>
                    <button type="button"
                            class="eye"
                            data-eye="pw2"
                            aria-label="Show password">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>

                <button type="submit" class="btn" id="submitBtn">Register</button>
            </form>

            <a class="back" href="<?= e(url('login')) ?>">&larr; Back to sign in</a>
        </section>
    </main>


    <!-- =========================================================
         JS
         ========================================================= -->
    <script>
    (function () {
        'use strict';

        /* ------------------------------------------------------
           Binary rain
           ------------------------------------------------------ */
        var cv = document.getElementById('rain');
        var cx = cv.getContext('2d');
        var cols, drops, fs = 18;

        function size() {
            cv.width  = innerWidth;
            cv.height = innerHeight;
            cols  = Math.ceil(cv.width / (fs * 1.6));
            drops = Array.from({ length: cols }, function () {
                return Math.random() * -40;
            });
        }

        size();
        addEventListener('resize', size);

        var still = matchMedia('(prefers-reduced-motion: reduce)').matches;

        function rain() {
            cx.clearRect(0, 0, cv.width, cv.height);
            cx.font = fs + 'px monospace';

            for (var i = 0; i < cols; i++) {
                var y    = drops[i] * fs;
                var gold = Math.random() < .015;

                cx.fillStyle = gold
                    ? 'rgba(250,213,23,.85)'
                    : 'rgba(120,160,255,.16)';

                cx.fillText(Math.random() < .5 ? '0' : '1', i * fs * 1.6, y);

                if (y > cv.height && Math.random() > .975) drops[i] = 0;
                drops[i] += .35;
            }

            if (!still) requestAnimationFrame(rain);
        }

        rain();


        /* ------------------------------------------------------
           Show / hide password
           ------------------------------------------------------ */
        document.querySelectorAll('[data-eye]').forEach(function (b) {
            b.addEventListener('click', function () {
                var i = document.getElementById(b.dataset.eye);
                var show = i.type === 'password';

                i.type = show ? 'text' : 'password';
                b.style.color = show ? '#fad517' : '';
                b.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            });
        });


        /* ------------------------------------------------------
           Live password rules
           ------------------------------------------------------ */
        var pw   = document.getElementById('pw');
        var pw2  = document.getElementById('pw2');

        var rules = {
            len: function (s) { return s.length >= 8; },
            up:  function (s) { return /[A-Z]/.test(s); },
            lo:  function (s) { return /[a-z]/.test(s); },
            num: function (s) { return /\d/.test(s); },
            sp:  function (s) { return /[^A-Za-z0-9\s]/.test(s); },
            ns:  function (s) { return !/\s/.test(s); }
        };

        function check() {
            var all = true;

            document.querySelectorAll('#rules li').forEach(function (li) {
                var ok = rules[li.dataset.r](pw.value);
                li.classList.toggle('ok', ok);
                if (!ok) all = false;
            });

            pw.setCustomValidity(all ? '' : 'Password does not meet all requirements.');
            pw2.setCustomValidity(
                pw2.value && pw2.value !== pw.value
                    ? 'Passwords do not match.'
                    : ''
            );
        }

        pw.addEventListener('input', check);
        pw2.addEventListener('input', check);
        check();


        /* ------------------------------------------------------
           Program & Year → hidden inputs
           Advisers (5-digit ID) skip program/year entirely.
           ------------------------------------------------------ */
        var YEARS = { BSIT: [1, 2, 3, 4], DIT: [1, 2, 3] };

        var py   = document.getElementById('py');
        var prog = document.getElementById('program');
        var yr   = document.getElementById('year_level');
        var idf  = document.getElementById('id_number');
        var wrap = document.getElementById('pyWrap');
        var hint = document.getElementById('pyHint');

        function parse() {
            var m  = py.value.trim().toUpperCase().match(/^(BSIT|DIT)\s*[-\s]?\s*(\d)$/);
            var ok = false;

            prog.value = '';
            yr.value   = '';

            if (m && YEARS[m[1]].indexOf(+m[2]) > -1) {
                prog.value = m[1];
                yr.value   = m[2];
                ok = true;
            }

            py.setCustomValidity(
                ok || py.required === false
                    ? ''
                    : 'Enter a valid program and year, e.g. BSIT 2.'
            );

            hint.hidden = ok || !py.value;
            wrap.classList.toggle('err', !ok && !!py.value);
        }

        function mode() {
            var adv = /^\d{5}$/.test(idf.value.trim());

            py.required = !adv;
            wrap.style.display = adv ? 'none' : '';

            if (adv) {
                prog.value  = '';
                yr.value    = '';
                hint.hidden = true;
            } else {
                parse();
            }
        }

        py.addEventListener('input', parse);
        idf.addEventListener('input', mode);
        mode();


        /* ------------------------------------------------------
           Submit: validate, shake on error, lock button
           ------------------------------------------------------ */
        var f   = document.getElementById('registerForm');
        var btn = document.getElementById('submitBtn');

        f.addEventListener('submit', function (e) {
            check();
            if (!/^\d{5}$/.test(idf.value.trim())) parse();

            if (!f.checkValidity()) {
                e.preventDefault();
                f.reportValidity();

                var c = document.querySelector('.card');
                c.classList.remove('shake');
                void c.offsetWidth;
                c.classList.add('shake');
                return;
            }

            btn.disabled = true;
            btn.textContent = 'Creating account…';
        });
    })();
    </script>

</body>

</html>