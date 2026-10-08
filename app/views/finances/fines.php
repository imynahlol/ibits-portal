<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iBITS Portal | Fines (preview)</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#06215c',
                        primaryLight: '#0a328c',
                        secondary: '#fad517',
                        secondaryHover: '#e5c215',
                        surface: '#ffffff',
                        textDark: '#1f2937',
                        textMuted: '#6b7280'
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <style>
        /* ---- Base ---- */
        body {
            background: #fff;
            color: #1f2937;
        }

        /* ---- Dark mode ---- */
        .dark body {
            background: #020a1c;
            color: #f1f5ff;
        }

        .dark .bg-surface {
            background: #081a45;
        }

        .dark .text-primary {
            color: #fff;
        }

        .dark .text-textMuted {
            color: #a9b8d6;
        }

        .dark .bg-white {
            background: rgba(255, 255, 255, .07);
        }

        .dark .bg-gray-50 {
            background: rgba(255, 255, 255, .05);
        }

        .dark .border-gray-100,
        .dark .border-gray-200 {
            border-color: rgba(255, 255, 255, .12);
        }

        .dark #sidebar {
            background: #010816;
            color: #f1f5ff;
        }

        .dark #sidebar .text-primary {
            color: #f1f5ff;
        }

        .dark input:not(.keep),
        .dark select {
            background: rgba(255, 255, 255, .1);
            color: #fff;
        }

        /* ---- White panels (modals stay light in dark mode) ---- */
        .wp,
        .dark .wp {
            background: #fff;
            color: #1f2937;
        }

        .dark .wp .text-primary {
            color: #06215c;
        }

        .dark .wp .text-textMuted {
            color: #6b7280;
        }

        .dark .wp input {
            background: #fff;
            color: #1f2937;
        }

        /* ---- Cards and buttons ---- */
        .hover-card {
            transition: all .3s;
        }

        .hover-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(6, 33, 92, .15);
        }

        .btn-primary {
            background: #06215c;
            color: #fff;
            box-shadow: 0 4px 0 #031236;
        }

        .btn-secondary {
            background: #fad517;
            color: #06215c;
            font-weight: 600;
            box-shadow: 0 4px 0 #bda000;
        }

        /* ---- Animations ---- */
        @keyframes up {
            from {
                opacity: 0;
                transform: translateY(18px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        .animate-slide-up {
            animation: up .6s ease-out forwards;
        }

        .modal-in {
            animation: up .25s ease-out;
        }
    </style>
</head>

<body class="font-sans antialiased">

    <!-- Preview-only controls -->
    <div class="bg-yellow-50 border-b border-yellow-200 text-xs text-yellow-900 px-4 py-2 flex flex-wrap items-center gap-2">
        <b>PREVIEW — view as:</b>
        <button data-role="member" class="rp px-3 py-1 rounded border">Member</button>
        <button data-role="viewer" class="rp px-3 py-1 rounded border">President / VP / Auditor</button>
        <button data-role="manager" class="rp px-3 py-1 rounded border">Treasurer / Asst. Treasurer / Secretary</button>
        <button onclick="document.documentElement.classList.toggle('dark')" class="ml-auto px-3 py-1 rounded border bg-white">Dark mode</button>
    </div>

    <div class="flex" style="height: calc(100vh - 37px)">

        <!-- Sidebar -->
        <aside id="sidebar" class="hidden md:flex w-64 bg-white text-primary flex-col border-r border-gray-200 shadow-sm">
            <div class="p-6 flex items-center gap-3 border-b border-gray-200">
                <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-secondary font-bold">&lt;/&gt;</div>
                <div>
                    <h2 class="text-xl font-bold tracking-wider">iBITS</h2>
                    <p class="text-xs text-secondaryHover font-medium">We Code as One</p>
                </div>
            </div>

            <div class="p-4 flex items-center gap-3 bg-slate-50">
                <div class="w-10 h-10 rounded-full border-2 border-secondary bg-secondary text-primary font-bold flex items-center justify-center text-xs">ME</div>
                <div>
                    <p class="text-sm font-semibold">Member Name</p>
                    <p id="role-label" class="text-xs text-secondaryHover font-semibold">Member</p>
                </div>
            </div>

            <nav class="flex-1 py-4 text-sm">
                <p class="px-6 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">General</p>
                <a class="block px-6 py-3 hover:bg-slate-100">Dashboard</a>
                <a id="nav-fines" class="block px-6 py-3 bg-primary text-white border-l-4 border-secondary">My Fines</a>
                <a class="block px-6 py-3 hover:bg-slate-100">Events / GPOA</a>
                <a class="block px-6 py-3 hover:bg-slate-100">Forms Hub</a>
                <a class="block px-6 py-3 hover:bg-slate-100">Help Desk</a>
            </nav>

            <div class="p-4 border-t border-gray-200">
                <button class="w-full py-2 bg-slate-100 text-primary rounded text-sm">Logout</button>
            </div>
        </aside>

        <!-- Main content -->
        <main class="flex-1 overflow-y-auto">
            <div class="w-full p-6 lg:p-10">

                <!-- Page header -->
                <div class="flex flex-wrap justify-between items-center gap-3 mb-8">
                    <div>
                        <h1 id="h-title" class="text-2xl font-bold text-primary">My Fines</h1>
                        <p id="h-sub" class="text-textMuted text-sm">Track and manage your organizational penalties.</p>
                    </div>
                    <button id="btn-issue" onclick="openM('m-fine')" class="hidden btn-secondary px-5 py-2 rounded-xl text-sm">+ Issue Fine</button>
                </div>

                <!-- Summary cards -->
                <div class="grid md:grid-cols-3 gap-6 mb-8">

                    <div class="bg-surface rounded-2xl p-6 shadow-sm border border-gray-100 hover-card animate-slide-up">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 rounded-xl bg-red-50 text-red-500 flex items-center justify-center text-xl">₱</div>
                            <span class="px-2 py-1 bg-red-100 text-red-600 text-xs font-bold rounded-md">Pending</span>
                        </div>
                        <h3 id="c1" class="text-gray-500 text-sm font-medium">Unpaid Balance</h3>
                        <div class="text-3xl font-bold text-primary mt-1">₱0.00</div>
                    </div>

                    <div class="bg-surface rounded-2xl p-6 shadow-sm border border-gray-100 hover-card animate-slide-up">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 rounded-xl bg-green-50 text-green-500 flex items-center justify-center text-xl">✓</div>
                            <span class="px-2 py-1 bg-green-100 text-green-600 text-xs font-bold rounded-md">Settled</span>
                        </div>
                        <h3 id="c2" class="text-gray-500 text-sm font-medium">Total Paid</h3>
                        <div class="text-3xl font-bold text-primary mt-1">₱0.00</div>
                    </div>

                    <div class="bg-surface rounded-2xl p-6 shadow-sm border border-gray-100 hover-card animate-slide-up">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center text-xl">#</div>
                            <span class="px-2 py-1 bg-blue-100 text-blue-600 text-xs font-bold rounded-md">Open</span>
                        </div>
                        <h3 class="text-gray-500 text-sm font-medium">Unpaid Fines</h3>
                        <div class="text-3xl font-bold text-primary mt-1">0</div>
                    </div>

                </div>

                <!-- Fines table -->
                <div class="bg-surface rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">

                    <div class="p-4 flex flex-wrap gap-2 items-center border-b border-gray-100">
                        <button class="px-4 py-1.5 rounded-lg text-sm border bg-secondary text-primary border-secondary font-semibold">All</button>
                        <button class="px-4 py-1.5 rounded-lg text-sm border bg-white text-textMuted border-gray-200">Unpaid</button>
                        <button class="px-4 py-1.5 rounded-lg text-sm border bg-white text-textMuted border-gray-200">Paid</button>
                        <button class="px-4 py-1.5 rounded-lg text-sm border bg-white text-textMuted border-gray-200">Waived</button>
                        <input id="search" type="search" placeholder="Search name, ID or reason"
                            class="hidden ml-auto w-full sm:w-64 px-3 py-1.5 border border-gray-200 rounded-lg text-sm">
                        <span id="count" class="ml-auto text-xs text-textMuted">0 records</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50">
                                <tr id="thead-row"></tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td id="empty-cell" colspan="5" class="p-10 text-center">
                                        <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-slate-100 text-textMuted flex items-center justify-center text-2xl">₱</div>
                                        <p class="font-semibold text-primary">No fines on record</p>
                                        <p id="empty-sub" class="text-xs text-textMuted mt-1">You are all clear. Keep it up!</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <!-- Issue fine modal -->
    <div id="m-fine" class="hidden fixed inset-0 z-50 bg-black/50 items-center justify-center p-4"
        onclick="if (event.target === this) closeM('m-fine')">
        <form onsubmit="event.preventDefault(); closeM('m-fine')"
            class="modal-in wp rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-3">

            <h3 class="text-lg font-bold text-primary">Issue Fine</h3>

            <label class="text-xs text-textMuted block">Member (type name or ID)
                <input class="keep w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Student ID">
            </label>

            <label class="text-xs text-textMuted block">Reason
                <input class="keep w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. Late to General Assembly">
            </label>

            <div class="grid grid-cols-2 gap-3">
                <label class="text-xs text-textMuted block">Amount (₱)
                    <input type="number" class="keep w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </label>
                <label class="text-xs text-textMuted block">Due date (optional)
                    <input type="date" class="keep w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </label>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeM('m-fine')" class="px-4 py-2 border border-gray-200 rounded-lg text-sm">Cancel</button>
                <button class="btn-primary px-5 py-2 rounded-lg text-sm">Issue Fine</button>
            </div>
        </form>
    </div>

    <script>
        const $ = s => document.querySelector(s);

        /* ---- Modal helpers ---- */
        const openM = id => {
            $('#' + id).classList.remove('hidden');
            $('#' + id).classList.add('flex');
        };

        const closeM = id => {
            $('#' + id).classList.add('hidden');
            $('#' + id).classList.remove('flex');
        };

        /* ---- Table header cell ---- */
        const TH = t => `<th class="p-4 font-medium text-gray-500 ${t[1] || ''}">${t[0]}</th>`;

        /* ---- Switch the preview between roles ---- */
        function setRole(r) {
            const all = r !== 'member';
            const mgr = r === 'manager';

            // Highlight the active role button
            document.querySelectorAll('.rp').forEach(b => {
                const active = b.dataset.role === r;
                b.className = 'rp px-3 py-1 rounded border ' +
                    (active ? 'bg-primary text-white border-primary' : 'bg-white');
            });

            // Table columns
            const cols = [['Date']];
            if (all) cols.push(['Member']);
            cols.push(['Reason'], ['Amount'], ['Due']);
            if (all) cols.push(['Issued By']);
            cols.push(['Status']);
            if (mgr) cols.push(['Actions', 'text-right']);

            $('#thead-row').innerHTML = cols.map(TH).join('');
            $('#empty-cell').colSpan = cols.length;

            // Titles and labels
            $('#h-title').textContent = all ? 'Fines Ledger' : 'My Fines';
            $('#h-sub').textContent = mgr
                ? 'Issue, edit and settle member fines.'
                : all
                    ? 'All organizational penalties issued to members (view only).'
                    : 'Track and manage your organizational penalties.';
            $('#nav-fines').textContent = all ? 'Fines Ledger' : 'My Fines';
            $('#c1').textContent = all ? 'Total Unpaid' : 'Unpaid Balance';
            $('#c2').textContent = all ? 'Total Collected' : 'Total Paid';

            // Role-specific controls
            $('#btn-issue').classList.toggle('hidden', !mgr);
            $('#search').classList.toggle('hidden', !all);
            $('#count').classList.toggle('hidden', all);

            $('#empty-sub').textContent = mgr
                ? 'Use “Issue Fine” to add the first one.'
                : all
                    ? 'Issued fines will appear here.'
                    : 'You are all clear. Keep it up!';

            $('#role-label').textContent = {
                member: 'Member',
                viewer: 'Vice President',
                manager: 'Treasurer'
            }[r];
        }

        document.querySelectorAll('.rp').forEach(b => {
            b.onclick = () => setRole(b.dataset.role);
        });

        setRole('manager');
    </script>
</body>

</html>
