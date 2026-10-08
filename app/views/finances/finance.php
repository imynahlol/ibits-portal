<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>iBITS Portal | Finance</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --navy: #15264e;
            --navy2: #243c7a;
            --deep: #0c1733;
            --gold: #fad517;
            --golddk: #bda000;
            --bg: #fffdf7;
            --surface: #fff;
            --ink: #1f2937;
            --muted: #6b7280;
            --line: #ece7d6;
            --soft: #fff8d6;
            --ok: #1f9d57;
            --bad: #c8372d;
            --head: #15264e;
            --side: #15264e;
            box-sizing: border-box;
            padding-top: env(safe-area-inset-top, 0px);
            padding-bottom: env(safe-area-inset-bottom, 0px)
        }

        @media(prefers-color-scheme:dark) {
            :root:not([data-theme="light"]) {
                --bg: #020a1c;
                --surface: #081a45;
                --ink: #f1f5ff;
                --muted: #a9b8d6;
                --line: rgba(255, 255, 255, .12);
                --soft: rgba(250, 213, 23, .12);
                --head: #fff;
                --side: #010816;
                --ok: #46d98b;
                --bad: #ff8a80
            }
        }

        :root[data-theme="dark"] {
            --bg: #020a1c;
            --surface: #081a45;
            --ink: #f1f5ff;
            --muted: #a9b8d6;
            --line: rgba(255, 255, 255, .12);
            --soft: rgba(250, 213, 23, .12);
            --head: #fff;
            --side: #010816;
            --ok: #46d98b;
            --bad: #ff8a80
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box
        }

        html {
            scroll-padding-top: env(safe-area-inset-top, 0px)
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: 'Poppins', system-ui, -apple-system, 'Segoe UI', sans-serif;
            line-height: 1.6;
            overflow-x: hidden
        }

        a {
            color: inherit;
            text-decoration: none
        }

        :focus-visible {
            outline: 3px solid var(--gold);
            outline-offset: 3px;
            border-radius: 8px
        }

        .app {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh
        }

        /* sidebar */
        aside {
            background: var(--side);
            color: #fff;
            padding: 26px 0;
            position: sticky;
            top: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255, 255, 255, .08)
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 24px 26px
        }

        .mark {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            background: var(--gold);
            color: var(--navy);
            display: grid;
            place-items: center;
            font-weight: 700;
            font-size: 15px;
            box-shadow: 0 3px 0 var(--golddk)
        }

        .brand b {
            display: block;
            line-height: 1.1;
            letter-spacing: .04em
        }

        .brand small {
            font-size: 11px;
            color: var(--gold)
        }

        nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            font-size: .9rem;
            color: #c9d4ee;
            border-left: 4px solid transparent;
            transition: background .2s, color .2s, padding .25s
        }

        nav a svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round
        }

        nav a:hover {
            background: rgba(255, 255, 255, .07);
            color: #fff;
            padding-left: 28px
        }

        nav a.on {
            border-left-color: var(--gold);
            background: rgba(250, 213, 23, .12);
            color: #fff;
            font-weight: 600
        }

        nav a.on svg {
            color: var(--gold)
        }

        /* main */
        main {
            padding: 34px clamp(18px, 4vw, 44px) 60px;
            min-width: 0
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 18px;
            flex-wrap: wrap;
            margin-bottom: 28px
        }

        h1 {
            margin: 0;
            font-size: 1.7rem;
            color: var(--head);
            line-height: 1.2;
            padding-left: 14px;
            border-left: 4px solid var(--gold)
        }

        .top p {
            margin: 4px 0 0 18px;
            color: var(--muted);
            font-size: .9rem
        }

        .tools {
            display: flex;
            gap: 10px;
            align-items: center
        }

        .theme {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1px solid var(--line);
            background: var(--surface);
            color: var(--ink);
            cursor: pointer;
            display: grid;
            place-items: center;
            transition: transform .3s, border-color .2s
        }

        .theme:hover {
            transform: rotate(25deg);
            border-color: var(--gold)
        }

        .btn {
            font: 600 .9rem 'Poppins', sans-serif;
            border: 0;
            border-radius: 14px;
            padding: 12px 22px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
            background: var(--navy);
            color: #fff;
            box-shadow: 0 4px 0 var(--deep);
            transition: transform .2s, box-shadow .2s, background .2s
        }

        .btn:hover {
            background: var(--navy2);
            transform: translateY(-2px);
            box-shadow: 0 6px 0 var(--deep), 0 12px 24px rgba(21, 38, 78, .25)
        }

        .btn:active {
            transform: translateY(3px);
            box-shadow: 0 1px 0 var(--deep)
        }

        .rip {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, .45);
            transform: scale(0);
            animation: rp .6s ease-out forwards;
            pointer-events: none
        }

        @keyframes rp {
            to {
                transform: scale(4);
                opacity: 0
            }
        }

        /* summary */
        .sum {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 18px;
            margin-bottom: 22px
        }

        .k {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 22px;
            padding: 22px 24px;
            position: relative;
            overflow: hidden;
            transition: transform .3s, box-shadow .3s, border-color .3s
        }

        .k:hover {
            transform: translateY(-4px);
            border-color: var(--gold);
            box-shadow: 0 20px 36px -22px rgba(21, 38, 78, .5)
        }

        .k span {
            font-size: .82rem;
            color: var(--muted)
        }

        .k b {
            display: block;
            font-size: 1.7rem;
            font-weight: 700;
            color: var(--head);
            letter-spacing: -.01em;
            line-height: 1.3;
            margin-top: 2px;
            font-variant-numeric: tabular-nums
        }

        .k.feat {
            background: var(--navy);
            border-color: var(--navy);
            color: #fff
        }

        .k.feat span {
            color: #c9d4ee
        }

        .k.feat b {
            color: var(--gold)
        }

        .k i {
            position: absolute;
            right: 18px;
            top: 18px;
            width: 36px;
            height: 36px;
            border-radius: 11px;
            background: var(--soft);
            color: var(--head);
            display: grid;
            place-items: center
        }

        .k.feat i {
            background: rgba(255, 255, 255, .12);
            color: var(--gold)
        }

        .k i svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.9;
            stroke-linecap: round;
            stroke-linejoin: round
        }

        .bar {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 22px;
            padding: 20px 24px;
            margin-bottom: 22px
        }

        .bar .t {
            display: flex;
            justify-content: space-between;
            font-size: .86rem;
            margin-bottom: 10px;
            gap: 10px;
            flex-wrap: wrap
        }

        .bar .t span {
            color: var(--muted)
        }

        .bar .t b {
            color: var(--head)
        }

        .track {
            height: 12px;
            border-radius: 99px;
            background: var(--soft);
            overflow: hidden
        }

        .fill {
            height: 100%;
            width: 0;
            border-radius: 99px;
            background: linear-gradient(90deg, var(--navy), var(--navy2));
            transition: width 1.2s .35s cubic-bezier(.2, .7, .2, 1);
            position: relative
        }

        .fill::after {
            content: "";
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 6px;
            background: var(--gold);
            border-radius: 99px
        }

        .fill.over {
            background: var(--bad)
        }

        /* ledger */
        .panel {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 22px;
            overflow: hidden
        }

        .filters {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            padding: 18px 22px;
            border-bottom: 1px solid var(--line);
            align-items: center
        }

        .filters h2 {
            margin: 0 auto 0 0;
            font-size: 1.05rem;
            color: var(--head)
        }

        .in {
            font: 400 .88rem 'Poppins', sans-serif;
            padding: 10px 14px;
            border-radius: 12px;
            border: 1.5px solid var(--line);
            background: var(--bg);
            color: var(--ink);
            transition: border-color .2s, box-shadow .2s;
            min-width: 0
        }

        .in:focus {
            outline: 0;
            border-color: var(--navy);
            box-shadow: 0 0 0 4px rgba(250, 213, 23, .35)
        }

        .scroll {
            overflow-x: auto
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: .88rem;
            min-width: 900px
        }

        th {
            text-align: left;
            font-weight: 600;
            color: var(--muted);
            padding: 12px 22px;
            background: var(--soft);
            white-space: nowrap
        }

        th button {
            all: unset;
            cursor: pointer;
            display: inline-flex;
            gap: 6px;
            align-items: center
        }

        th button:hover {
            color: var(--head)
        }

        th button:focus-visible {
            outline: 3px solid var(--gold);
            outline-offset: 3px
        }

        td {
            padding: 15px 22px;
            border-top: 1px solid var(--line);
            vertical-align: top
        }

        tbody tr {
            transition: background .2s
        }

        tbody tr:hover {
            background: var(--soft)
        }

        td.n,
        th.n {
            text-align: right;
            font-variant-numeric: tabular-nums;
            white-space: nowrap
        }

        .ev {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 99px;
            background: var(--navy);
            color: #fff;
            font-size: .78rem;
            font-weight: 500
        }

        .note {
            color: var(--muted);
            max-width: 34ch
        }

        .out {
            color: var(--bad)
        }

        .inf {
            color: var(--ok);
            font-weight: 500
        }

        .who {
            display: flex;
            gap: 9px;
            align-items: center;
            white-space: nowrap
        }

        .av {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--gold);
            color: var(--navy);
            display: grid;
            place-items: center;
            font-size: .72rem;
            font-weight: 700
        }

        tbody tr.rin {
            animation: rowin .5s cubic-bezier(.2, .7, .2, 1) both;
            animation-delay: calc(var(--i)*40ms)
        }

        @keyframes rowin {
            from {
                opacity: 0;
                transform: translateY(8px)
            }

            to {
                opacity: 1;
                transform: none
            }
        }

        .foot {
            display: flex;
            justify-content: space-between;
            padding: 14px 22px;
            border-top: 1px solid var(--line);
            font-size: .84rem;
            color: var(--muted)
        }

        .empty {
            padding: 56px 22px;
            text-align: center
        }

        .empty b {
            display: block;
            color: var(--head);
            font-size: 1.05rem
        }

        .empty p {
            margin: 6px auto 0;
            max-width: 38ch;
            color: var(--muted);
            font-size: .9rem
        }

        .alert {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            padding: 18px 22px;
            border-radius: 18px;
            background: rgba(200, 55, 45, .08);
            border: 1px solid rgba(200, 55, 45, .3);
            margin-bottom: 22px;
            animation: rowin .4s ease-out
        }

        .alert b {
            display: block;
            color: var(--bad)
        }

        .alert p {
            margin: 2px 0 0;
            font-size: .88rem
        }

        /* actions + dialogs */
        .sr {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0 0 0 0)
        }

        .btn-gold {
            background: var(--gold);
            color: var(--navy);
            box-shadow: 0 4px 0 var(--golddk)
        }

        .btn-gold:hover {
            background: var(--gold);
            box-shadow: 0 6px 0 var(--golddk), 0 12px 24px rgba(250, 213, 23, .3)
        }

        .btn-line {
            background: transparent;
            color: var(--head);
            border: 1.5px solid var(--line);
            box-shadow: none
        }

        .btn-line:hover {
            background: var(--soft);
            border-color: var(--gold);
            box-shadow: none
        }

        .btn-bad {
            background: var(--bad);
            box-shadow: 0 4px 0 #8e241d;
            color: #fff
        }

        .btn-bad:hover {
            background: var(--bad);
            box-shadow: 0 6px 0 #8e241d
        }

        .bdg {
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 99px;
            background: var(--gold);
            color: var(--navy);
            font-size: .72rem;
            display: inline-grid;
            place-items: center
        }

        .ico {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: 0;
            background: none;
            color: var(--muted);
            cursor: pointer;
            display: inline-grid;
            place-items: center;
            transition: background .2s, color .2s, transform .2s
        }

        .ico:hover {
            background: rgba(200, 55, 45, .12);
            color: var(--bad);
            transform: scale(1.08)
        }

        dialog {
            border: 0;
            padding: 0;
            border-radius: 24px;
            background: var(--surface);
            color: var(--ink);
            width: min(520px, calc(100% - 28px));
            box-shadow: 0 40px 80px -20px rgba(12, 23, 51, .6);
            font-family: inherit
        }

        dialog::backdrop {
            background: rgba(12, 23, 51, .55);
            backdrop-filter: blur(3px)
        }

        dialog[open] {
            animation: pop .3s cubic-bezier(.2, .7, .2, 1)
        }

        @keyframes pop {
            from {
                opacity: 0;
                transform: translateY(16px) scale(.97)
            }

            to {
                opacity: 1;
                transform: none
            }
        }

        dialog.drawer {
            margin: 0 0 0 auto;
            height: 100%;
            max-height: 100%;
            width: min(430px, 100%);
            border-radius: 24px 0 0 24px
        }

        dialog.drawer[open] {
            animation: slide .35s cubic-bezier(.2, .7, .2, 1)
        }

        @keyframes slide {
            from {
                transform: translateX(40px);
                opacity: 0
            }

            to {
                transform: none;
                opacity: 1
            }
        }

        .dh {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 22px 26px 6px
        }

        .dh h2 {
            margin: 0;
            font-size: 1.2rem;
            color: var(--head);
            padding-left: 12px;
            border-left: 4px solid var(--gold)
        }

        .x {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 0;
            background: var(--soft);
            color: var(--head);
            cursor: pointer;
            font-size: 1.1rem
        }

        .db {
            padding: 14px 26px 26px
        }

        .g2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px
        }

        .fg {
            margin-bottom: 14px
        }

        .fg label {
            display: block;
            font-size: .8rem;
            font-weight: 500;
            margin-bottom: 6px;
            color: var(--muted)
        }

        .fg .in {
            width: 100%
        }

        .fg textarea {
            resize: vertical;
            min-height: 70px
        }

        .g3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px
        }

        .fe {
            min-height: 20px;
            color: var(--bad);
            font-size: .84rem;
            margin: 0 0 10px
        }

        .da {
            display: flex;
            justify-content: flex-end;
            gap: 10px
        }

        .hl {
            list-style: none;
            margin: 0;
            padding: 0
        }

        .hl li {
            display: flex;
            gap: 12px;
            padding: 14px 0;
            border-top: 1px solid var(--line);
            animation: rowin .4s both;
            animation-delay: calc(var(--i)*40ms)
        }

        .hl li:first-child {
            border-top: 0
        }

        .tag {
            align-self: flex-start;
            font-size: .72rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 99px
        }

        .tag.a {
            background: rgba(31, 157, 87, .15);
            color: var(--ok)
        }

        .tag.r {
            background: rgba(200, 55, 45, .12);
            color: var(--bad)
        }

        .tag.s {
            background: var(--soft);
            color: var(--head)
        }

        .hl b {
            display: block;
            font-size: .9rem;
            color: var(--head)
        }

        .hl small {
            color: var(--muted);
            display: block;
            font-size: .78rem
        }

        .hl .btn {
            padding: 6px 14px;
            font-size: .78rem;
            margin-top: 8px;
            box-shadow: none
        }

        .toast {
            position: fixed;
            left: 50%;
            bottom: calc(22px + env(safe-area-inset-bottom, 0px));
            transform: translate(-50%, 20px);
            background: var(--navy);
            color: #fff;
            padding: 11px 20px;
            border-radius: 12px;
            font-size: .86rem;
            opacity: 0;
            pointer-events: none;
            transition: all .3s;
            z-index: 50
        }

        .toast.on {
            opacity: 1;
            transform: translate(-50%, 0)
        }

        @media(max-width:900px) {
            .app {
                grid-template-columns: 1fr
            }

            aside {
                position: static;
                height: auto;
                flex-direction: row;
                align-items: center;
                padding: 12px 0;
                overflow-x: auto
            }

            .brand {
                padding: 0 18px
            }

            nav {
                display: flex
            }

            nav a {
                border-left: 0;
                border-bottom: 3px solid transparent;
                white-space: nowrap;
                padding: 10px 14px
            }

            nav a:hover {
                padding-left: 14px
            }

            nav a.on {
                border-bottom-color: var(--gold)
            }
        }

        @media(prefers-reduced-motion:reduce) {

            *,
            *::before,
            *::after {
                animation-duration: .01ms !important;
                animation-delay: 0s !important;
                transition-duration: .01ms !important
            }
        }
    </style>
</head>

<body>
    <div class="app">
        <aside>
            <div class="brand"><span class="mark">iB</span><span><b>iBITS</b><small>We Code as One</small></span></div>
            <nav aria-label="Dashboard">
                <a href="dashboard.php"><svg viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="9" rx="1.5" />
                        <rect x="14" y="3" width="7" height="5" rx="1.5" />
                        <rect x="14" y="12" width="7" height="9" rx="1.5" />
                        <rect x="3" y="16" width="7" height="5" rx="1.5" />
                    </svg>Dashboard</a>
                <a href="#"><svg viewBox="0 0 24 24">
                        <rect x="3" y="5" width="18" height="16" rx="3" />
                        <path d="M8 3v4M16 3v4M3 10h18" />
                    </svg>Events</a>
                <a href="#" class="on" aria-current="page"><svg viewBox="0 0 24 24">
                        <rect x="2" y="6" width="20" height="13" rx="3" />
                        <path d="M2 10h20M6 15h4" />
                    </svg>Finance</a>
                <a href="#"><svg viewBox="0 0 24 24">
                        <path d="M6 3h9l4 4v14H6z" />
                        <path d="M9 13h7M9 17h5" />
                    </svg>Fines</a>
                <a href="#"><svg viewBox="0 0 24 24">
                        <path d="M4 4h12l4 4v12H4z" />
                        <path d="M8 12h8M8 16h5" />
                    </svg>Forms</a>
            </nav>
        </aside>

        <main>
            <div class="top">
                <div>
                    <h1>Finance</h1>
                    <p>Budgets, expenses, and inflow for every event.</p>
                </div>
                <div class="tools">
                    <button class="btn btn-line" id="exp">Export CSV</button>
                    <button class="btn btn-line" id="hbtn" aria-haspopup="dialog"><svg width="16" height="16"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12a9 9 0 1 0 3-6.7L3 8" />
                            <path d="M3 3v5h5M12 7v5l3 2" />
                        </svg>History <span class="bdg" id="hc" hidden>0</span></button>
                    <button class="btn btn-gold" data-add aria-haspopup="dialog"><svg width="16" height="16"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"
                            stroke-linecap="round">
                            <path d="M12 5v14M5 12h14" />
                        </svg>Add record</button>
                    <button class="theme" id="theme" aria-label="Toggle dark mode"><svg width="17" height="17"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z" />
                        </svg></button>
                </div>
            </div>

            <div id="alert"></div>

            <section class="sum" aria-label="Summary">
                <div class="k"><i><svg viewBox="0 0 24 24">
                            <path d="M4 7h16v12H4zM4 7l3-3h10l3 3" />
                        </svg></i><span>Total budget</span><b data-k="total_budget">₱0.00</b></div>
                <div class="k"><i><svg viewBox="0 0 24 24">
                            <path d="M12 5v14M6 13l6 6 6-6" />
                        </svg></i><span>Total expenses</span><b data-k="total_expense">₱0.00</b></div>
                <div class="k"><i><svg viewBox="0 0 24 24">
                            <path d="M12 19V5M6 11l6-6 6 6" />
                        </svg></i><span>Total inflow</span><b data-k="total_inflow">₱0.00</b></div>
                <div class="k feat"><i><svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 7v5l3 2" />
                        </svg></i><span>Budget remaining</span><b id="rem">₱0.00</b></div>
            </section>

            <div class="bar">
                <div class="t"><span>Budget used</span><b id="pct">0%</b></div>
                <div class="track" role="progressbar" aria-label="Budget used" id="pb">
                    <div class="fill" id="fill"></div>
                </div>
            </div>

            <section class="panel">
                <div class="filters">
                    <h2>Ledger</h2>
                    <input class="in" id="q" type="search" placeholder="Search notes or creator"
                        aria-label="Search ledger">
                    <select class="in" id="evf" aria-label="Filter by event">
                        <option value="">All events</option>
                    </select>
                </div>
                <div class="scroll">
                    <table>
                        <thead>
                            <tr>
                                <th><button data-sort="date_" aria-label="Sort by date">Date <span
                                            id="sd">▼</span></button></th>
                                <th>Event</th>
                                <th>Notes</th>
                                <th class="n">Budget</th>
                                <th class="n">Expense</th>
                                <th class="n">Inflow</th>
                                <th>Recorded by</th>
                                <th class="n"><span class="sr">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody id="rows"></tbody>
                    </table>
                </div>
                <div id="empty" class="empty" hidden><b>No records yet</b>
                    <p>Finance entries for your events will appear here once they're added.</p>
                </div>
                <div class="foot"><span id="cnt"></span><span>Newest first</span></div>
            </section>
        </main>
    </div>


    <dialog id="dadd" aria-labelledby="ta">
        <div>
            <div class="dh">
                <h2 id="ta">Add record</h2><button class="x" data-close aria-label="Close">&times;</button>
            </div>
            <form class="db" id="fadd" novalidate>
                <div class="g2">
                    <div class="fg"><label for="fd">Date</label><input class="in" id="fd" type="date" required></div>
                    <div class="fg"><label for="fe">Event</label><input class="in" id="fe" list="evl"
                            placeholder="Event name" required><datalist id="evl"></datalist></div>
                </div>
                <div class="fg"><label for="fn">Notes</label><textarea class="in" id="fn"
                        placeholder="What was this for?"></textarea></div>
                <div class="g3">
                    <div class="fg"><label for="fb">Budget (₱)</label><input class="in" id="fb" type="number" min="0"
                            step="0.01" placeholder="0.00"></div>
                    <div class="fg"><label for="fx">Expense (₱)</label><input class="in" id="fx" type="number" min="0"
                            step="0.01" placeholder="0.00"></div>
                    <div class="fg"><label for="fi">Inflow (₱)</label><input class="in" id="fi" type="number" min="0"
                            step="0.01" placeholder="0.00"></div>
                </div>
                <p class="fe" id="ferr" role="alert"></p>
                <div class="da"><button type="button" class="btn btn-line" data-close>Cancel</button><button
                        type="submit" class="btn btn-gold">Save record</button></div>
            </form>
        </div>
    </dialog>
    <dialog id="ddel" aria-labelledby="td">
        <div>
            <div class="dh">
                <h2 id="td">Remove this record?</h2><button class="x" data-close aria-label="Close">&times;</button>
            </div>
            <div class="db">
                <p id="dtxt" style="margin:0 0 20px;color:var(--muted)"></p>
                <div class="da"><button class="btn btn-line" data-close>Keep it</button><button class="btn btn-bad"
                        id="dyes">Remove record</button></div>
            </div>
        </div>
    </dialog>
    <dialog id="dhis" class="drawer" aria-labelledby="th">
        <div>
            <div class="dh">
                <h2 id="th">History</h2><button class="x" data-close aria-label="Close">&times;</button>
            </div>
            <div class="db">
                <ul class="hl" id="hlist"></ul>
            </div>
        </div>
    </dialog>
    <div class="toast" id="toast" role="status"></div>

    <script>
        /* ---- Data starts empty. Records live in memory until you wire these actions to PHP:
           load:   const FINANCE = <?= json_encode(['finance_records' => $finance_records, 'database_error' => $database_error ?? null], JSON_HEX_TAG | JSON_HEX_AMP) ?>;
           add:    fetch('finance_add.php',{method:'POST',body:...})   remove: fetch('finance_delete.php',...) */
        var D = { finance_records: [], finance_summary: { total_budget: 0, total_expense: 0, total_inflow: 0 }, database_error: null }, asc = false, HIST = [], nextId = 1;
        function recompute() { var t = { total_budget: 0, total_expense: 0, total_inflow: 0 }; D.finance_records.forEach(function (r) { t.total_budget += +r.budget; t.total_expense += +r.expense; t.total_inflow += +r.inflow }); D.finance_summary = t }
        var $ = function (i) { return document.getElementById(i) };
        var peso = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' });
        var fmtDate = function (s) { return new Date(s + 'T00:00:00').toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' }) };
        var reduce = matchMedia('(prefers-reduced-motion:reduce)').matches;

        function count(el, to) {
            var from = el._v || 0; el._v = to;
            if (reduce || from === to) { el.textContent = peso.format(to); return }
            var t0 = performance.now(), dur = 1100;
            (function f(t) { var p = Math.min(1, (t - t0) / dur), e = 1 - Math.pow(1 - p, 3); el.textContent = peso.format(from + (to - from) * e); if (p < 1) requestAnimationFrame(f) })(t0);
        }
        function renderSummary() {
            var s = D.finance_summary;
            document.querySelectorAll('[data-k]').forEach(function (b) { count(b, s[b.dataset.k]) });
            var rem = s.total_budget - s.total_expense; count($('rem'), rem);
            var pct = s.total_budget > 0 ? s.total_expense / s.total_budget * 100 : 0;
            $('pct').textContent = Math.round(pct) + '% of ' + peso.format(s.total_budget);
            $('pb').setAttribute('aria-valuenow', Math.round(pct));
            var f = $('fill'); f.className = 'fill' + (pct > 100 ? ' over' : ''); f.style.width = '0';
            requestAnimationFrame(function () { requestAnimationFrame(function () { f.style.width = Math.min(100, pct) + '%' }) });
        }
        function esc(s) { var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML }
        function money(v, cls) { return v > 0 ? '<span class="' + cls + '">' + peso.format(v) + '</span>' : '<span style="color:var(--muted)">–</span>' }
        function ini(n) { return (n || '?').split(' ').map(function (x) { return x[0] }).slice(0, 2).join('').toUpperCase() }

        function renderRows(animate) {
            var q = $('q').value.trim().toLowerCase(), ev = $('evf').value;
            var rs = D.finance_records.filter(function (r) {
                return (!ev || r.event_title === ev) && (!q || (r.finance_notes + ' ' + r.creator_name).toLowerCase().indexOf(q) > -1);
            }).sort(function (a, b) { return asc ? a.date_.localeCompare(b.date_) : b.date_.localeCompare(a.date_) });
            $('rows').innerHTML = rs.map(function (r, i) {
                return '<tr class="' + (animate ? 'rin' : '') + '" style="--i:' + i + '"><td>' + fmtDate(r.date_) + '</td><td><span class="ev">' + esc(r.event_title) + '</span></td><td class="note">' + esc(r.finance_notes) + '</td><td class="n">' + money(+r.budget, '') + '</td><td class="n">' + money(+r.expense, 'out') + '</td><td class="n">' + money(+r.inflow, 'inf') + '</td><td><span class="who"><span class="av">' + esc(ini(r.creator_name)) + '</span>' + esc(r.creator_name) + '</span></td><td class="n"><button class="ico" data-del="' + r.id + '" aria-label="Remove record"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13M10 11v6M14 11v6"/></svg></button></td></tr>';
            }).join('');
            var none = D.finance_records.length === 0;
            $('empty').hidden = !none && rs.length > 0;
            if (!none && rs.length === 0) { $('empty').hidden = false; $('empty').innerHTML = '<b>No matches</b><p>Try a different search or choose another event.</p>' }
            else if (none) { $('empty').innerHTML = '<b>No records yet</b><p>Add your first budget, expense, or inflow to start the ledger.</p><button class="btn btn-gold" data-add style="margin-top:18px">Add record</button>' }
            $('cnt').textContent = rs.length + (rs.length === 1 ? ' record' : ' records');
        }
        function renderEvents() {
            var set = {}; D.finance_records.forEach(function (r) { set[r.event_title] = 1 });
            $('evf').innerHTML = '<option value="">All events</option>' + Object.keys(set).map(function (e) { return '<option>' + esc(e) + '</option>' }).join('');
        }
        function render() {
            recompute();
            $('alert').innerHTML = D.database_error ? '<div class="alert" role="alert"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--bad)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v6M12 16.5v.5"/></svg><div><b>Finance records didn\u2019t load</b><p>' + esc(D.database_error) + ' Refresh the page, or contact the helpdesk if it keeps happening.</p></div></div>' : '';
            renderSummary(); renderEvents(); renderRows(true);
        }

        $('q').addEventListener('input', function () { renderRows(false) });
        $('evf').addEventListener('change', function () { renderRows(true) });
        document.querySelector('[data-sort]').addEventListener('click', function () { asc = !asc; $('sd').textContent = asc ? '▲' : '▼'; renderRows(true) });

        $('exp').addEventListener('click', function () {
            if (!D.finance_records.length) { toast('Nothing to export yet'); return }
            var h = ['Date', 'Event', 'Notes', 'Budget', 'Expense', 'Inflow', 'Recorded by'];
            var q = function (v) { return '"' + String(v).replace(/"/g, '""') + '"' };
            var csv = [h.map(q).join(',')].concat(D.finance_records.map(function (r) { return [r.date_, r.event_title, r.finance_notes, r.budget, r.expense, r.inflow, r.creator_name].map(q).join(',') })).join('\n');
            var a = document.createElement('a'); a.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv' })); a.download = 'finance-ledger.csv'; a.click();
        });
        document.addEventListener('pointerdown', function (e) {
            var b = e.target.closest('.btn'); if (!b) return; var r = b.getBoundingClientRect(), s = Math.max(r.width, r.height) / 2, d = document.createElement('span');
            d.className = 'rip'; d.style.cssText = 'width:' + s + 'px;height:' + s + 'px;left:' + (e.clientX - r.left - s / 2) + 'px;top:' + (e.clientY - r.top - s / 2) + 'px'; b.appendChild(d); setTimeout(function () { d.remove() }, 600);
        });
        $('theme').addEventListener('click', function () {
            var r = document.documentElement, dk = r.dataset.theme === 'dark' || (!r.dataset.theme && matchMedia('(prefers-color-scheme:dark)').matches); r.dataset.theme = dk ? 'light' : 'dark';
        });
        /* ---- add / remove / history ---- */
        var $a = $('dadd'), $d = $('ddel'), $h = $('dhis'), pend = null, tt;
        function toast(m) { var t = $('toast'); t.textContent = m; t.classList.add('on'); clearTimeout(tt); tt = setTimeout(function () { t.classList.remove('on') }, 2000) }
        function log(type, r) { HIST.unshift({ type: type, r: r, at: new Date(), done: false }); $('hc').textContent = HIST.length; $('hc').hidden = false }
        function sumTxt(r) { var p = []; if (+r.budget) p.push('Budget ' + peso.format(r.budget)); if (+r.expense) p.push('Expense ' + peso.format(r.expense)); if (+r.inflow) p.push('Inflow ' + peso.format(r.inflow)); return p.join(' \u00b7 ') }
        function openAdd() {
            $('fadd').reset(); $('ferr').textContent = '';
            $('fd').value = new Date(Date.now() - new Date().getTimezoneOffset() * 6e4).toISOString().slice(0, 10);
            var set = {}; D.finance_records.forEach(function (r) { set[r.event_title] = 1 });
            $('evl').innerHTML = Object.keys(set).map(function (e) { return '<option value="' + esc(e) + '">' }).join('');
            $a.showModal(); $('fe').focus();
        }
        $('fadd').addEventListener('submit', function (e) {
            e.preventDefault();
            var b = +$('fb').value || 0, x = +$('fx').value || 0, i = +$('fi').value || 0, ev = $('fe').value.trim();
            if (!$('fd').value || !ev) { $('ferr').textContent = 'Enter a date and an event.'; return }
            if (b < 0 || x < 0 || i < 0 || (b + x + i) === 0) { $('ferr').textContent = 'Enter at least one amount greater than zero.'; return }
            var r = { id: nextId++, date_: $('fd').value, finance_notes: $('fn').value.trim(), budget: b, expense: x, inflow: i, event_title: ev, creator_name: 'You' };
            D.finance_records.push(r); log('Added', r); $a.close(); render(); toast('Record added');
        });
        function askDel(id) {
            pend = D.finance_records.filter(function (r) { return r.id === id })[0]; if (!pend) return;
            $('dtxt').textContent = pend.event_title + ' \u00b7 ' + fmtDate(pend.date_) + ' \u00b7 ' + sumTxt(pend) + '. You can restore it from History.';
            $d.showModal();
        }
        $('dyes').addEventListener('click', function () {
            if (!pend) return;
            D.finance_records = D.finance_records.filter(function (r) { return r.id !== pend.id });
            log('Removed', pend); pend = null; $d.close(); render(); toast('Record removed');
        });
        function renderHist() {
            $('hlist').innerHTML = HIST.length ? HIST.map(function (h, i) {
                var c = h.type === 'Added' ? 'a' : h.type === 'Removed' ? 'r' : 's';
                return '<li style="--i:' + i + '"><span class="tag ' + c + '">' + h.type + '</span><div><b>' + esc(h.r.event_title) + '</b><small>' + esc(sumTxt(h.r)) + '</small><small>' + h.at.toLocaleString('en-PH', { dateStyle: 'medium', timeStyle: 'short' }) + '</small>' + (h.type === 'Removed' && !h.done ? '<button class="btn btn-line" data-restore="' + i + '">Restore</button>' : '') + '</div></li>';
            }).join('') : '<li class="empty" style="display:block;padding:40px 0;text-align:center"><b>No activity yet</b><p style="margin:6px auto 0;color:var(--muted);font-size:.9rem">Added, removed, and restored records will show up here.</p></li>';
        }
        $('hbtn').addEventListener('click', function () { renderHist(); $h.showModal() });
        document.addEventListener('click', function (e) {
            var t = e.target;
            if (t.closest('[data-add]')) { openAdd(); return }
            var x = t.closest('[data-del]'); if (x) { askDel(+x.dataset.del); return }
            var c = t.closest('[data-close]'); if (c) { c.closest('dialog').close(); return }
            var rs = t.closest('[data-restore]');
            if (rs) { var h = HIST[+rs.dataset.restore]; if (h && !h.done) { h.done = true; D.finance_records.push(h.r); log('Restored', h.r); renderHist(); render(); toast('Record restored') } return }
            if (t.tagName === 'DIALOG') t.close();
        });
        render();
    </script>
</body>

</html>