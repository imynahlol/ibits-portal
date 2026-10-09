<!-- ============================================================
     Public Navigation (guest view)
     ============================================================ -->
<header class="absolute w-full z-30 zd" id="public-nav">

    <div class="mx-auto max-w-6xl px-4 pt-3">

        <div class="glass-pill flex items-center justify-between
                    rounded-full px-5 py-2.5">

            <!-- ---------- Brand ---------- -->
            <div class="flex items-center gap-3 cursor-pointer"
                 onclick="navigate('public-home')">

                <img alt="iBITS logo"
                     class="logo-img w-9 h-9 rounded-full" />

                <span class="font-bold text-white">iBITS PUPLC</span>
            </div>


            <!-- ---------- Desktop nav ---------- -->
            <nav class="hidden md:flex items-center gap-7 text-sm text-white">

                <!-- About dropdown -->
                <div class="dd relative">
                    <button type="button" class="flex items-center gap-1">
                        About
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </button>
                    <div class="dd-m">
                        <a href="#" onclick="navigate('public-about')">Who We Are</a>
                        <a href="#" onclick="navigate('public-about')">Mission &amp; Vision</a>
                        <a href="#" onclick="navigate('public-org')">Org Structure</a>
                    </div>
                </div>

                <!-- Community dropdown -->
                <div class="dd relative">
                    <button type="button" class="flex items-center gap-1">
                        Community
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </button>
                    <div class="dd-m">
                        <a href="#" onclick="navigate('public-org')">Members</a>
                        <a href="#" onclick="navigate('public-faqs')">FAQs</a>
                        <a href="#" onclick="navigate('login')">Help Desk</a>
                    </div>
                </div>

                <!-- Events -->
                <a href="#" onclick="navigate('public-events')">Events</a>

                <!-- Get ID -->
                <button type="button"
                        class="tilt px-4 py-1.5 rounded-xl font-bold text-white"
                        onclick="navigate('register')"
                        style="background: #06215c; box-shadow: 0 4px 0 #031236;">
                    Get ID
                </button>

                <!-- Login (user icon) -->
                <button type="button"
                        aria-label="Log in"
                        class="w-9 h-9 rounded-full bg-white text-primary
                               flex items-center justify-center"
                        onclick="navigate('login')">
                    <i class="fa-solid fa-user"></i>
                </button>
            </nav>


            <!-- ---------- Mobile nav ---------- -->
            <div class="md:hidden flex gap-2">

                <button type="button"
                        class="text-sm font-semibold text-white"
                        onclick="navigate('login')">
                    Login
                </button>

                <button type="button"
                        class="px-3 py-1 rounded-lg text-sm font-bold text-white"
                        onclick="navigate('register')"
                        style="background: #06215c;">
                    Get ID
                </button>
            </div>

        </div>
    </div>
</header>