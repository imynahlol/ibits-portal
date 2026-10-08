/* =============================================================
   iBITS Portal — app.js
   Reads user/session data from <body data-*> set by PHP.
   ============================================================= */

(function () {
    'use strict';

    /* ---------------------------------------------------------
       1. Boot config — read from <body data-*>
       --------------------------------------------------------- */
    const boot = document.body.dataset;

    const APP = {
        isLoggedIn: boot.loggedIn === '1',
        user: {
            id:       boot.userId       || '',
            name:     boot.userName     || '',
            fullName: boot.userFullName || '',
            role:     boot.userRole     || 'Member',
            program:  boot.userProgram  || '',
        },
        perms: (boot.userPerms || '')
                    .split(',')
                    .map(s => s.trim())
                    .filter(Boolean),
        currentView: boot.initialView || 'public-home',
    };

    const has = p => APP.perms.includes(p);

    /* ---------------------------------------------------------
       2. DOM shortcuts
       --------------------------------------------------------- */
    const $  = s => document.querySelector(s);
    const $$ = s => [...document.querySelectorAll(s)];
    const esc = s => String(s ?? '').replace(/[&<>"]/g, c => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;'
    }[c]));

    const sidebar          = $('#sidebar');
    const publicNav        = $('#public-nav');
    const authMobileHeader = $('#auth-mobile-header');
    const footer           = $('#app-footer');
    const adminMenu        = $('#admin-menu');
    const views            = $$('.view-section');
    const navLinks         = $$('.nav-link');

    /* ---------------------------------------------------------
       3. UI state — driven by APP, not hardcoded
       --------------------------------------------------------- */
    function updateUIState() {
        if (APP.isLoggedIn) {
            sidebar && sidebar.classList.remove('hidden');
            sidebar && sidebar.classList.add('flex');
            publicNav && publicNav.classList.add('hidden');
            authMobileHeader && authMobileHeader.classList.remove('hidden');
            footer && footer.classList.add('hidden');

            const isExec = ['President', 'Vice President'].includes(APP.user.role);
            if (adminMenu) adminMenu.style.display = isExec ? 'block' : 'none';
        } else {
            sidebar && sidebar.classList.add('hidden');
            sidebar && sidebar.classList.remove('flex');
            publicNav && publicNav.classList.remove('hidden');
            authMobileHeader && authMobileHeader.classList.add('hidden');
            footer && footer.classList.remove('hidden');
        }
    }

    /* ---------------------------------------------------------
       4. Navigation
       --------------------------------------------------------- */
    function navigate(viewId) {
        views.forEach(v => {
            v.classList.remove('active');
            v.querySelectorAll('.animate-slide-up, .animate-fade-in').forEach(el => {
                el.style.animation = 'none';
                el.offsetHeight; /* reflow */
                el.style.animation = null;
            });
        });

        const target = document.getElementById('view-' + viewId);
        if (target) {
            target.classList.add('active');
            APP.currentView = viewId;
            const vp = $('#content-viewport');
            if (vp) vp.scrollTop = 0;
        }

        navLinks.forEach(link => {
            link.classList.remove('bg-primary', 'text-white', 'border-secondary');
            link.classList.add('border-transparent');
            const oc = link.getAttribute('onclick') || '';
            if (oc.includes(`'${viewId}'`)) {
                link.classList.add('bg-primary', 'text-white', 'border-secondary');
                link.classList.remove('border-transparent');
            }
        });

        if (window.innerWidth < 768 && APP.isLoggedIn && sidebar) {
            sidebar.classList.add('hidden');
            sidebar.classList.remove('flex');
        }
    }

    function logout() {
        // Real logout goes through PHP — this just redirects
        window.location.href = (window.__APP_URL__ || '') + '/logout';
    }

    function toggleDarkMode() {
        document.documentElement.classList.toggle('dark');
    }

    /* ---------------------------------------------------------
       5. Custom modal
       --------------------------------------------------------- */
    const modal = $('#custom-modal');
    const modalContent = $('#modal-content');
    let confirmCallback = null;

    function showModal(title, body, isPrimary = true, onConfirm = null) {
        $('#modal-title').innerText = title;
        $('#modal-body').innerText = body;
        const btn = $('#modal-confirm-btn');
        if (isPrimary) {
            btn.className = 'px-4 py-2 btn-primary rounded-lg text-sm font-medium';
        } else {
            btn.className = 'px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700';
        }
        confirmCallback = onConfirm || (() => closeModal());
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modalContent.classList.remove('scale-95');
        modalContent.classList.add('scale-100');
    }

    function closeModal() {
        modal.classList.add('opacity-0', 'pointer-events-none');
        modalContent.classList.add('scale-95');
        modalContent.classList.remove('scale-100');
        confirmCallback = null;
    }

    function confirmModal() {
        if (confirmCallback) confirmCallback();
        closeModal();
    }

    /* ---------------------------------------------------------
       6. iBITS feature layer (unchanged from your v4 additions)
       --------------------------------------------------------- */
    const LOGO = 'assets/images/ibits-logo.png';
    const RM = matchMedia('(prefers-reduced-motion:reduce)').matches;
    const AI_CONFIG = { endpoint: '' };

    /* ---- Data (replace with API fetch when backend is ready) ---- */
    const DATA = window.__PORTAL_DATA__ || {
        population: {
            program: { BSIT: 850, DIT: 390 },
            year: { '1st Year': 380, '2nd Year': 330, '3rd Year': 300, '4th Year': 230 },
            section: { 'Section A': 320, 'Section B': 310, 'Section C': 300, 'Section D': 310 },
            officers: 15
        },
        fines: [{ amt: 100, paid: false }, { amt: 50, paid: false }],
        events: [],
        tickets: []
    };

    function loadPortalData() {
        /* TODO: fetch('/api/portal-data') then Object.assign(DATA, json); renderAll(); */
    }

    /* ---- Icons ---- */
    const P_ = {
        house: 'M3 11l9-8 9 8M5 10v10h5v-6h4v6h5V10',
        /* ... keep the rest of your icon paths unchanged ... */
    };
    const FA = { /* ... unchanged ... */ };

    function iconize(root = document) {
        root.querySelectorAll('i[class*="fa-"]').forEach(i => {
            /* ... unchanged ... */
        });
    }

        /* ---- Binary rain ---- */
    function rain() {
        $$('canvas.rain').forEach(function (c) {
            if (!c.offsetParent) return;

            const w = c.width  = c.clientWidth;
            const h = c.height = c.clientHeight;
            const f = 18;
            const g = c.getContext('2d');

            if (!c._h || c._w !== w) {
                c._w = w;
                c._h = Array.from({ length: Math.ceil(w / f) }, function () {
                    return Math.random() * h / f;
                });
            }

            g.clearRect(0, 0, w, h);
            g.font = (f - 4) + 'px monospace';

            c._h.forEach(function (y, i) {
                for (let k = 0; k < 7; k++) {
                    const L = !!c.closest('.wp');   // light section
                    g.fillStyle = (k === 0 && i % 9 === 0)
                        ? 'rgba(250,213,23,' + (L ? .45 : .5) + ')'
                        : (L ? 'rgba(6,33,92,' : 'rgba(190,205,235,') + ((L ? .12 : .3) - k * .04) + ')';

                    const digit = ((i * 7 + k + Math.floor(y)) % 3) ? 1 : 0;
                    g.fillText(digit, i * f, (y - k) * f);
                }
                if (!RM) {
                    c._h[i] = (y * f > h + 120) ? -Math.random() * 20 : y + .35;
                }
            });
        });
    }
    setInterval(rain, 90);

    /* ---- 3D tilt ---- */
    function tilt() {
        if (RM || matchMedia('(pointer:coarse)').matches) return;
        $$('.tilt:not([data-t])').forEach(function (el) {
            el.dataset.t = 1;
            el.addEventListener('mousemove', function (e) {
                const r = el.getBoundingClientRect();
                const x = (e.clientX - r.left) / r.width  - .5;
                const y = (e.clientY - r.top)  / r.height - .5;
                el.style.transform = 'perspective(800px) rotateX(' + (-y*8) + 'deg) rotateY(' + (x*10) + 'deg)';
            });
            el.addEventListener('mouseleave', function () {
                el.style.transform = '';
            });
        });
    }

    /* ---- Feature layer: population stats, calendar, tickets, chat ---- */
    function renderStats()      { /* your existing implementation */ }
    function renderPublic()     { /* your existing implementation */ }
    function renderCal()        { /* your existing implementation */ }
    function renderTickets()    { /* your existing implementation */ }
    function renderDash()       { /* your existing implementation */ }
    function renderAll() {
        renderStats();
        renderPublic();
        renderCal();
        renderTickets();
        renderDash();
        iconize();
        tilt();
    }

    /* ---- Icon shim (SVG fallback) ---- */
    function iconize(root) {
        root = root || document;
        /* your existing implementation */
    }
    
    /* ---- 3D tilt ---- */
    function tilt() {
        /* ... unchanged ... */
    }

    /* ---- Population stats, calendar, dashboard, tickets,
           AI chat, login mode, etc. — all unchanged ---- */

    /* ---------------------------------------------------------
       7. Wrapper functions that need APP instead of state
       --------------------------------------------------------- */
    function afterUI() {
        const u = APP.user;
        const adm = has('admin_org') || has('admin_cbl');
        if (adminMenu) adminMenu.style.display = adm ? 'block' : 'none';
        const banner = $('#admin-banner');
        if (banner) banner.style.display = adm ? 'flex' : 'none';
        const uName = $('#auth-user-name'); if (uName) uName.textContent = u.name;
        const uRole = $('#auth-user-role'); if (uRole) uRole.textContent = u.role;
        const greet = $('#dash-greeting-name');
        if (greet) greet.textContent = (u.name.split(' ')[0]) || 'there';

        const p = $('#admin-banner p');
        if (p && adm) {
            p.textContent = 'You have ' + u.role + ' privileges. ' +
                (has('admin_org') ? 'You can manage Org structure and verify CBL updates.'
                                  : 'You can verify CBL updates.');
        }

        const addBtn = $('#add-event-btn');
        if (addBtn) addBtn.classList.toggle('hidden', !has('admin_events'));

        const fab = $('#chat-fab');
        if (fab) fab.classList.toggle('hidden', !APP.isLoggedIn);

        if (!APP.isLoggedIn && typeof toggleChat === 'function') toggleChat(false);

        if (typeof renderAll === 'function') renderAll();
    }

    /* ---------------------------------------------------------
       8. Public API — expose to window for inline onclick=
       --------------------------------------------------------- */
    Object.assign(window, {
        APP,               // read-only access for templates
        has,
        navigate,
        logout,
        toggleDarkMode,
        showModal,
        closeModal,
        confirmModal,
        // expose your other functions too:
        // selectYear, setYear, selDay, calMove, calToday,
        // showEvent, openEventForm, saveEvent, delEvent,
        // openTicketForm, saveTicket, setTk,
        // toggleChat, sendChat,
        // loginMode, togglePw, checkPw,
        // doLogin, doRegister
    });

    /* ---------------------------------------------------------
       9. Init
       --------------------------------------------------------- */
    function initApp() {
        updateUIState();
        afterUI();
        // Activate the view PHP decided based on URL
        const startId = APP.currentView.replace(/^view-/, '');
        navigate(startId);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initApp);
    } else {
        initApp();
    }
})();

