/* =========================================================
   icons.js — inline SVG icon system
   Font Awesome CDN is not guaranteed, so every <i class="fa-*">
   is swapped for an inline SVG path.
   ========================================================= */

const P_ = {
    house:'M3 11l9-8 9 8M5 10v10h5v-6h4v6h5V10',
    code:'M8 8l-5 4 5 4M16 8l5 4-5 4M14 5l-4 14',
    file:'M6 3h8l4 4v14H6zM14 3v4h4M9 12h6M9 16h6',
    calendar:'M3 6h18v15H3zM3 10h18M8 3v4M16 3v4',
    'calendar-check':'M3 6h18v15H3zM3 10h18M8 3v4M16 3v4M9 15l2 2 4-4',
    headset:'M4 14v-2a8 8 0 0116 0v2M4 14h3v5H4zM17 14h3v5h-3zM17 19c0 1.5-2 2-5 2',
    sitemap:'M10 3h4v4h-4zM3 15h4v4H3zM17 15h4v4h-4zM12 7v4M12 11H5v4M12 11h7v4',
    scale:'M12 3v18M5 21h14M5 7h14M5 7l-3 7a3 3 0 006 0zM19 7l-3 7a3 3 0 006 0z',
    cog:'M12 9a3 3 0 100 6 3 3 0 000-6M12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2',
    signout:'M9 4H4v16h5M16 8l4 4-4 4M20 12H9',
    bars:'M3 6h18M3 12h18M3 18h18',
    bell:'M6 16v-5a6 6 0 0112 0v5l2 2H4zM10 20a2 2 0 004 0',
    ticket:'M3 7h18v4a2 2 0 000 2v4H3v-4a2 2 0 000-2zM14 7v10',
    'arrow-right':'M5 12h14M13 6l6 6-6 6',
    'arrow-left':'M19 12H5M11 6l-6 6 6 6',
    shield:'M12 3l8 3v6c0 5-4 8-8 9-4-1-8-4-8-9V6zM9 12l2 2 4-4',
    lock:'M6 11h12v10H6zM8 11V7a4 4 0 018 0v4',
    bullseye:'M12 3a9 9 0 100 18 9 9 0 000-18M12 8a4 4 0 100 8 4 4 0 000-8',
    eye:'M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12zM12 9a3 3 0 100 6 3 3 0 000-6',
    'eye-slash':'M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12zM12 9a3 3 0 100 6 3 3 0 000-6M4 4l16 16',
    spinner:'M12 3a9 9 0 019 9',
    user:'M12 4a4 4 0 100 8 4 4 0 000-8M4 21c0-4 4-6 8-6s8 2 8 6',
    plus:'M12 5v14M5 12h14',
    xmark:'M6 6l12 12M18 6L6 18',
    minus:'M5 12h