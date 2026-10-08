<div class="view-section w-full p-6 lg:p-10" id="view-dashboard">
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 animate-fade-in">
<div>
<h1 class="text-2xl font-bold text-primary dark:text-white">Dashboard Overview</h1>
<p class="text-textMuted text-sm">Welcome back, <span class="font-medium text-primary dark:text-gray-300" id="dash-greeting-name">Johahnie</span>!</p>
</div>
<div class="mt-4 md:mt-0 flex gap-3">
<button class="bg-surface border border-gray-200 p-2 rounded-lg text-textMuted hover:text-primary transition-colors shadow-sm">
<i class="fa-regular fa-bell"></i>
</button>
</div>
</div>
<!-- Widgets Grid -->
<div class="grid md:grid-cols-3 gap-6 mb-8">
<!-- Widget 1: Fines -->
<div class="bg-surface rounded-2xl p-6 shadow-sm border border-gray-100 hover-card tilt animate-slide-up" style="animation-delay: 0.1s;">
<div class="flex justify-between items-start mb-4">
<div class="w-12 h-12 rounded-xl bg-red-50 text-red-500 flex items-center justify-center text-xl">
<i class="fa-solid fa-file-invoice-dollar"></i>
</div>
<span class="px-2 py-1 bg-red-100 text-red-600 text-xs font-bold rounded-md">Pending</span>
</div>
<h3 class="text-gray-500 text-sm font-medium">My Total Fines</h3>
<div class="text-3xl font-bold text-primary mt-1" id="dash-fines">₱0.00</div>
<button class="text-xs text-primary font-medium mt-4 hover:underline flex items-center gap-1" onclick="navigate('fines')">View Details <i class="fa-solid fa-arrow-right"></i></button>
</div>
<!-- Widget 2: Events -->
<div class="bg-surface rounded-2xl p-6 shadow-sm border border-gray-100 hover-card tilt animate-slide-up" style="animation-delay: 0.2s;">
<div class="flex justify-between items-start mb-4">
<div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center text-xl">
<i class="fa-solid fa-calendar-check"></i>
</div>
<span class="px-2 py-1 bg-blue-100 text-blue-600 text-xs font-bold rounded-md" id="dash-event-badge">Upcoming</span>
</div>
<h3 class="text-gray-500 text-sm font-medium">Upcoming Events</h3>
<div class="text-xl font-bold text-primary mt-1 truncate" id="dash-event">No upcoming events</div><div class="text-xs text-textMuted" id="dash-event-date"></div>
<button class="text-xs text-primary font-medium mt-4 hover:underline flex items-center gap-1" onclick="navigate('events')">View Calendar <i class="fa-solid fa-arrow-right"></i></button>
</div>
<!-- Widget 3: Help Desk -->
<div class="bg-surface rounded-2xl p-6 shadow-sm border border-gray-100 hover-card tilt animate-slide-up" style="animation-delay: 0.3s;">
<div class="flex justify-between items-start mb-4">
<div class="w-12 h-12 rounded-xl bg-green-50 text-green-500 flex items-center justify-center text-xl">
<i class="fa-solid fa-ticket-alt"></i>
</div>
<span class="px-2 py-1 bg-green-100 text-green-600 text-xs font-bold rounded-md">Active</span>
</div>
<h3 class="text-gray-500 text-sm font-medium">Open Tickets</h3>
<div class="text-3xl font-bold text-primary mt-1" id="dash-tickets">0</div>
<button class="text-xs text-primary font-medium mt-4 hover:underline flex items-center gap-1" onclick="navigate('helpdesk')">Go to Help Desk <i class="fa-solid fa-arrow-right"></i></button>
</div>
</div>
<!-- Role Specific Banner (VP Simulation) -->
<div class="bg-gradient-to-r from-primary to-primaryLight rounded-2xl p-6 text-white shadow-lg mb-8 animate-slide-up flex flex-col md:flex-row justify-between items-center" id="admin-banner" style="animation-delay: 0.4s;">
<div class="flex items-center gap-4 mb-4 md:mb-0">
<div class="w-12 h-12 rounded-full bg-secondary flex items-center justify-center text-primary text-xl">
<i class="fa-solid fa-shield-halved"></i>
</div>
<div>
<h3 class="font-bold text-lg">Admin Access Active</h3>
<p class="text-sm text-blue-200">You have Vice President privileges. You can manage Org structure and verify CBL updates.</p>
</div>
</div>
<button class="bg-secondary text-primary px-5 py-2 rounded-lg text-sm font-bold shadow hover:bg-white transition-colors" onclick="navigate('admin-cbl')">Review Pending CBL</button>
</div>
</div>
