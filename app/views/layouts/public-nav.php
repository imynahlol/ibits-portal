<header id="public-nav" class="absolute w-full z-30 zd">
    <div class="mx-auto max-w-6xl px-4 pt-3">
        <div class="glass-pill flex items-center justify-between rounded-full px-5 py-2.5">
            <div class="flex items-center gap-3 cursor-pointer" onclick="navigate('public-home')">
                <img class="logo-img w-9 h-9 rounded-full" alt="iBITS logo">
                <span class="font-bold text-white">iBITS PUPLC</span>
            </div>

            <nav class="hidden md:flex items-center gap-7 text-sm text-white">
                <div class="dd relative">
                    <button class="flex items-center gap-1">About <i class="fa-solid fa-chevron-down text-xs"></i></button>
                    <div class="dd-m">
                        <a href="#" onclick="navigate('public-about')">Who We Are</a>
                        <a href="#" onclick="navigate('public-about')">Mission &amp; Vision</a>
                        <a href="#" onclick="navigate('public-org')">Org Structure</a>
                    </div>
                </div>
                <div class="dd relative">
                    <button class="flex items-center gap-1">Community <i class="fa-solid fa-chevron-down text-xs"></i></button>
                    <div class="dd-m">
                        <a href="#" onclick="navigate('public-org')">Members</a>
                        <a href="#" onclick="navigate('public-faqs')">FAQs</a>
                        <a href="#" onclick="navigate('login')">Help Desk</a>
                    </div>
                </div>
                <a href="#" onclick="navigate('public-events')">Events</a>
                <button onclick="navigate('register')"
                        class="tilt px-4 py-1.5 rounded-xl font-bold text-white"
                        style="background:#06215c;box-shadow:0 4px 0 #031236">Get ID</button>
                <button onclick="navigate('login')"
                        class="w-9 h-9 rounded-full bg-white text-primary flex items-center justify-center"
                        aria-label="Log in">
                    <i class="fa-solid fa-user"></i>
                </button>
            </nav>

            <div class="md:hidden flex gap-2">
                <button onclick="navigate('login')" class="text-sm font-semibold text-white">Login</button>
                <button onclick="navigate('register')"
                        class="px-3 py-1 rounded-lg text-sm font-bold text-white"
                        style="background:#06215c">Get ID</button>
            </div>
        </div>
    </div>
</header>

<header id="auth-mobile-header" class="hidden md:hidden bg-surface shadow-sm px-4 py-3 flex justify-between items-center z-10">
    <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-secondary font-bold">
            <i class="fa-solid fa-code"></i>
        </div>
        <span class="font-bold text-primary">iBITS</span>
    </div>
    <button onclick="document.getElementById('sidebar').classList.toggle('hidden'); document.getElementById('sidebar').classList.toggle('flex');"
            class="text-primary p-2">
        <i class="fa-solid fa-bars text-xl"></i>
    </button>
</header>