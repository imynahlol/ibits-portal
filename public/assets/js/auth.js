/* =============================================================
   auth.js — iBITS Portal authentication pages
   Used by: app/views/auth/login.php, app/views/auth/register.php
   ============================================================= */
    (function () {
        'use strict';

        // Config passed from the view via <body data-*>
        var root = document.querySelector('[data-auth-app]');
        var LOGO = (root && root.dataset.logo) || 'assets/images/ibits-logo.png';

        /* ----------------------------------------------------------
        Helpers
        ---------------------------------------------------------- */
        function el(tag, className, html) {
            var node = document.createElement(tag);
            if (className) node.className = className;
            if (html != null) node.innerHTML = html;
            return node;
        }

        function markReady() {
            document.documentElement.classList.add('ui-ready');
        }

        var EYE_ICON =
            '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" ' +
            'stroke="currentColor" stroke-width="2" stroke-linecap="round" ' +
            'stroke-linejoin="round">' +
                '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/>' +
                '<circle cx="12" cy="12" r="3"/>' +
            '</svg>';

        /* ----------------------------------------------------------
        Build the page shell
        ---------------------------------------------------------- */
        function build() {
            // Login page uses <form class="form">, register uses <form class="form register">
            var form = document.querySelector('form.form');
            if (!form || document.querySelector('.ui-view')) {
                markReady();
                return;
            }

            var isRegister = form.classList.contains('form-register') ||
                            document.body.hasAttribute('data-page-register');

            var heading      = document.querySelector('h2');
            var alerts       = [].slice.call(document.querySelectorAll('.alert'));
            var bottomNote   = document.querySelector('p[data-auth-note]');
            var toggleGroup  = document.querySelector('[data-account-toggle]');
            var pwRulesBox   = document.querySelector('[data-pw-rules]');

            /* ----- Containers ----- */
            var viewEl = el('div', 'ui-view');
            var rain   = el('canvas', 'ui-rain');
            var grid   = el('div', 'ui-grid');
            var hero   = el('div', 'ui-hero ui-in');
            var card   = el('div', 'ui-card ui-in' + (isRegister ? ' ui-card-wide' : ''));
            var header = el('div', 'ui-hd');

            rain.setAttribute('aria-hidden', 'true');
            hero.style.setProperty('--d', '.05s');
            card.style.setProperty('--d', '.15s');

            /* ----- Hero ----- */
            hero.innerHTML =
                '<div class="ui-coin-wrap">' +
                    '<div class="ui-coin">' +
                        '<img alt="iBITS logo" src="' + LOGO + '">' +
                    '</div>' +
                '</div>' +
                '<h1>iBITS Portal</h1>' +
                '<p class="ui-tag">We Code as One</p>';

            hero.querySelector('img').addEventListener('error', function () {
                var wrap = hero.querySelector('.ui-coin-wrap');
                if (wrap) wrap.remove();
            });

            /* ----- Card header ----- */
            if (heading) header.appendChild(heading);
            var sub = document.querySelector('[data-auth-sub]');
            header.appendChild(el('p', '', sub ? sub.textContent : 'IDENTIFY YOURSELF TO PROCEED.'));

            /* ----- Assemble card ----- */
            card.appendChild(header);

            // Alerts (server-rendered)
            alerts.forEach(function (a) { card.appendChild(a); });

            // Account-type toggle (register only)
            if (toggleGroup) card.appendChild(toggleGroup);

            // The form itself
            card.appendChild(form);

            // Bottom note (register here / login here)
            if (bottomNote) card.appendChild(bottomNote);

            wrapFields(form);

            // Password rules box (register only)
            if (pwRulesBox) setupPasswordRules(form, pwRulesBox);

            /* ----- Mount ----- */
            grid.appendChild(hero);
            grid.appendChild(card);
            viewEl.appendChild(rain);
            viewEl.appendChild(grid);
            document.body.appendChild(viewEl);

            setupSubmit(form, card);
            startRain(rain);
            markReady();
        }

        /* ----------------------------------------------------------
        Wrap each label+input in .ui-fld, add eye toggle
        ---------------------------------------------------------- */
        function wrapFields(form) {
            [].slice.call(form.querySelectorAll('label')).forEach(function (label, i) {
                // Skip labels inside the account toggle (handled separately)
                if (label.closest('.ui-toggle')) return;

                var input = label.nextElementSibling;
                if (!input || (input.tagName !== 'INPUT' && input.tagName !== 'SELECT')) return;

                input.id = input.id || ('ui-f' + i);
                label.setAttribute('for', input.id);

                var group = el('div', 'ui-fld');
                form.insertBefore(group, label);
                group.appendChild(label);
                group.appendChild(input);

                // Add eye toggle for password inputs
                if (input.type === 'password') {
                    var eye = el('button', 'ui-eye', EYE_ICON);
                    eye.type = 'button';
                    eye.setAttribute('aria-label', 'Show password');

                    eye.addEventListener('click', function () {
                        var isHidden = input.type === 'password';
                        input.type = isHidden ? 'text' : 'password';
                        eye.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
                    });

                    group.appendChild(eye);
                }

                // Move any helper <small> inside the group
                var next = group.nextElementSibling;
                if (next && next.tagName === 'SMALL') {
                    group.appendChild(next);
                }
            });
        }

        /* ----------------------------------------------------------
        Live password rules (register only)
        ---------------------------------------------------------- */
        function setupPasswordRules(form, rulesBox) {
            var pwInput = form.querySelector('input[type=password]');
            if (!pwInput) return;

            var rules = [
                { test: function (v) { return v.length >= 8; },              label: 'At least 8 characters' },
                { test: function (v) { return /[A-Z]/.test(v); },            label: 'One uppercase letter' },
                { test: function (v) { return /[a-z]/.test(v); },            label: 'One lowercase letter' },
                { test: function (v) { return /\d/.test(v); },               label: 'One number' }
            ];

            rulesBox.className = 'ui-rules';
            rulesBox.innerHTML = rules.map(function (r) {
                return '<li>' + r.label + '</li>';
            }).join('');

            var items = rulesBox.querySelectorAll('li');

            function update() {
                var v = pwInput.value;
                rules.forEach(function (r, i) {
                    items[i].classList.toggle('ok', r.test(v));
                });
            }

            pwInput.addEventListener('input', update);
            update();
        }

        /* ----------------------------------------------------------
        Submit: loading state + ripple
        ---------------------------------------------------------- */
        function setupSubmit(form, card) {
            var button = form.querySelector('button[type=submit]');
            if (!button) return;

            var buttonLabel = button.textContent;

            form.addEventListener('submit', function (e) {
                // Basic client-side empty check
                var firstInput = form.querySelector('input[name]');
                var pwInput    = form.querySelector('input[type=password]');
                var empty      = false;

                [].slice.call(form.querySelectorAll('input[required], select[required]')).forEach(function (field) {
                    if (!field.value.trim()) empty = true;
                });

                if (empty) {
                    e.preventDefault();
                    showError(card, form, 'Please fill in all required fields.');
                    return;
                }

                // Start loading state — the form submits naturally
                button.innerHTML = '<span class="ui-spin"></span>' + buttonLabel;
                button.disabled = true;
            });

            // Click ripple
            button.addEventListener('pointerdown', function (e) {
                var rect = button.getBoundingClientRect();
                var size = Math.max(rect.width, rect.height) / 2;
                var ripple = el('span', 'ui-rip');

                ripple.style.cssText =
                    'width:'  + size + 'px;' +
                    'height:' + size + 'px;' +
                    'left:'   + (e.clientX - rect.left - size / 2) + 'px;' +
                    'top:'    + (e.clientY - rect.top  - size / 2) + 'px';

                button.appendChild(ripple);
                setTimeout(function () { ripple.remove(); }, 600);
            });
        }

        function showError(card, form, message) {
            var alertBox = el('div', 'alert alert-error',
                '<ul><li>' + message + '</li></ul>');
            var oldAlert = card.querySelector('.alert-error');
            if (oldAlert) oldAlert.remove();
            card.insertBefore(alertBox, form);
        }

        /* ----------------------------------------------------------
        Binary rain background
        ---------------------------------------------------------- */
        function startRain(canvas) {
            var reducedMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
            var ctx = canvas.getContext('2d');
            var fontSize = 18;
            var trailLength = 7;
            var columns;

            function draw() {
                if (document.hidden) return;

                var w = canvas.width  = canvas.clientWidth;
                var h = canvas.height = canvas.clientHeight;

                if (!columns || canvas._w !== w) {
                    canvas._w = w;
                    columns = Array.from({ length: Math.ceil(w / fontSize) }, function () {
                        return Math.random() * h / fontSize;
                    });
                }

                ctx.clearRect(0, 0, w, h);
                ctx.font = (fontSize - 4) + 'px monospace';

                columns.forEach(function (y, i) {
                    for (var k = 0; k < trailLength; k++) {
                        var isGoldHead = k === 0 && i % 9 === 0;

                        ctx.fillStyle = isGoldHead
                            ? 'rgba(250, 213, 23, .5)'
                            : 'rgba(190, 205, 235, ' + (0.3 - k * 0.04) + ')';

                        var digit = ((i * 7 + k + Math.floor(y)) % 3) ? 1 : 0;
                        ctx.fillText(digit, i * fontSize, (y - k) * fontSize);
                    }

                    if (!reducedMotion) {
                        columns[i] = (y * fontSize > h + 120)
                            ? -Math.random() * 20
                            : y + 0.35;
                    }
                });
            }

            draw();
            setInterval(draw, 90);
        }

        /* ----------------------------------------------------------
        Init
        ---------------------------------------------------------- */
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', build);
        } else {
            build();
        }

        // Safety net: never leave the page hidden
        setTimeout(markReady, 1500);
    })();