<aside class="hidden w-64 bg-white text-primary flex-col border-r border-gray-200 shadow-sm z-20 transition-all duration-300" id="sidebar">
<div class="p-6 flex items-center gap-3 border-b border-gray-200">
<div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-secondary font-bold text-xl">
<i class="fa-solid fa-code"></i>
</div>
<div>
<h2 class="text-xl font-bold tracking-wider">iBITS</h2>
<p class="text-xs text-secondaryHover font-medium">We Code as One</p>
</div>
</div>
<div class="p-4 flex items-center gap-3 bg-slate-50">
<img alt="Profile" class="w-10 h-10 rounded-full border-2 border-secondary" src="https://placehold.co/100x100/fad517/06215c?text=VP"/>
<div class="overflow-hidden">
<p class="text-sm font-semibold truncate" id="auth-user-name">Johahnie Gilbuena</p>
<p class="text-xs text-secondaryHover font-semibold truncate" id="auth-user-role">Vice President</p>
</div>
</div>
<nav class="flex-1 overflow-y-auto py-4">
<p class="px-6 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">General</p>
<a class="nav-link block px-6 py-3 text-sm hover:bg-slate-100 hover:text-primary transition-colors border-l-4 border-transparent active-nav" href="#" onclick="navigate('dashboard')"><i class="fa-solid fa-house w-6"></i> Dashboard</a>
<a class="nav-link block px-6 py-3 text-sm hover:bg-slate-100 hover:text-primary transition-colors border-l-4 border-transparent" href="#" onclick="navigate('fines')"><i class="fa-solid fa-file-invoice-dollar w-6"></i> My Fines</a>
<a class="nav-link block px-6 py-3 text-sm hover:bg-slate-100 hover:text-primary transition-colors border-l-4 border-transparent" href="#" onclick="navigate('events')"><i class="fa-solid fa-calendar-alt w-6"></i> Events / GPOA</a>
<a class="nav-link block px-6 py-3 text-sm hover:bg-slate-100 hover:text-primary transition-colors border-l-4 border-transparent" href="#" onclick="navigate('forms')"><i class="fa-solid fa-file-alt w-6"></i> Forms Hub</a>
<a class="nav-link block px-6 py-3 text-sm hover:bg-slate-100 hover:text-primary transition-colors border-l-4 border-transparent" href="#" onclick="navigate('helpdesk')"><i class="fa-solid fa-headset w-6"></i> Help Desk</a>
<div class="mt-6" id="admin-menu">
<p class="px-6 text-xs font-semibold text-secondaryHover uppercase tracking-wider mb-2">Admin Tools</p>
<a class="nav-link block px-6 py-3 text-sm hover:bg-slate-100 hover:text-primary transition-colors border-l-4 border-transparent" href="#" onclick="navigate('admin-org')"><i class="fa-solid fa-sitemap w-6"></i> Manage Org</a>
<a class="nav-link block px-6 py-3 text-sm hover:bg-slate-100 hover:text-primary transition-colors border-l-4 border-transparent" href="#" onclick="navigate('admin-cbl')"><i class="fa-solid fa-scale-balanced w-6"></i> CBL Workflow</a>
</div>
<p class="px-6 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 mt-6">Settings</p>
<a class="nav-link block px-6 py-3 text-sm hover:bg-slate-100 hover:text-primary transition-colors border-l-4 border-transparent" href="#" onclick="navigate('settings')"><i class="fa-solid fa-cog w-6"></i> Preferences</a>
</nav>
<div class="p-4 border-t border-gray-200">
<button class="w-full py-2 px-4 bg-slate-100 text-primary hover:bg-red-600 hover:text-white rounded text-sm transition-colors flex items-center justify-center gap-2" onclick="logout()">
<i class="fa-solid fa-sign-out-alt"></i> Logout
                </button>
</div>
</aside>
