<aside id="sidebar"
       class="hidden w-64 bg-white text-primary flex-col border-r border-gray-200 shadow-sm z-20 transition-all duration-300">
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
        <img src="https://placehold.co/100x100/fad517/06215c?text=VP"
             alt="Profile" class="w-10 h-10 rounded-full border-2 border-secondary">
        <div class="overflow-hidden">
            <p id="auth-user-name" class="text-sm font-semibold truncate">Johahnie Gilbuena</p>
            <p id="auth-user-role" class="text-xs text-secondaryHover font-semibold truncate">Vice President</p>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto py-4">
        <p class="px-6 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">General</p>
        <a href="#" onclick="navigate('dashboard')" class="nav-link block px-6 py-3 text-sm hover:bg-slate-100 hover:text-primary transition-colors border-l-4 border-transparent active-nav"><i class="fa-solid fa-house w-6"></i> Dashboard</a>
        <a href="#" onclick="navigate('fines')"     class="nav-link block px-6 py-3 text-sm hover:bg-slate-100 hover:text-primary transition-colors border-l-4 border-transparent"><i class="fa-solid fa-file-invoice-dollar w-6"></i> My Fines</a>
        <a href="#" onclick="navigate('events')"    class="nav-link block px-6 py-3 text-sm hover:bg-slate-100 hover:text-primary transition-colors border-l-4 border-transparent"><i class="fa-solid fa-calendar-alt w-6"></i> Events / GPOA</a>
        <a href="#" onclick="navigate('forms')"     class="nav-link block px-6 py-3 text-sm hover:bg-slate-100 hover:text-primary transition-colors border-l-4 border-transparent"><i class="fa-solid fa-file-alt w-6"></i> Forms Hub</a>
        <a href="#" onclick="navigate('helpdesk')"  class="nav-link block px-6 py-3 text-sm hover:bg-slate-100 hover:text-primary transition-colors border-l-4 border-transparent"><i class="fa-solid fa-headset w-6"></i> Help Desk</a>

        <div id="admin-menu" class="mt-6">
            <p class="px-6 text-xs font-semibold text-secondaryHover uppercase tracking-wider mb-2">Admin Tools</p>
            <a href="#" onclick="navigate('admin-org')" class="nav-link block px-6 py-3 text-sm hover:bg-slate-100 hover:text-primary transition-colors border-l-4 border-transparent"><i class="fa-solid fa-sitemap w-6"></i> Manage Org</a>
            <a href="#" onclick="navigate('admin-cbl')" class="nav-link block px-6 py-3 text-sm hover:bg-slate-100 hover:text-primary transition-colors border-l-4 border-transparent"><i class="fa-solid fa-scale-balanced w-6"></i> CBL Workflow</a>
        </div>

        <p class="px-6 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 mt-6">Settings</p>
        <a href="#" onclick="navigate('settings')" class="nav-link block px-6 py-3 text-sm hover:bg-slate-100 hover:text-primary transition-colors border-l-4 border-transparent"><i class="fa-solid fa-cog w-6"></i> Preferences</a>
    </nav>

    <div class="p-4 border-t border-gray-200">
        <button onclick="logout()"
                class="w-full py-2 px-4 bg-slate-100 text-primary hover:bg-red-600 hover:text-white rounded text-sm transition-colors flex items-center justify-center gap-2">
            <i class="fa-solid fa-sign-out-alt"></i> Logout
        </button>
    </div>
</aside>