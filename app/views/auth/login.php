<div class="view-section w-full min-h-screen relative overflow-hidden zd pt-24 pb-10 px-4"
     id="view-login">

    <canvas class="rain absolute inset-0 w-full h-full"></canvas>

    <div class="relative z-10 max-w-6xl mx-auto grid md:grid-cols-2 gap-10 items-center min-h-[70vh]">

        <!-- ---------- LEFT: LOGO ---------- -->
        <div class="text-center login-in" style="--d:.05s">
            <div class="coin-wrap">
                <div class="coin">
                    <img alt="iBITS logo" class="logo-img" src="<?= asset('assets/images/ibits-logo.png') ?>" />
                </div>
            </div>

            <h1 class="text-5xl md:text-6xl font-bold gt">iBITS Portal</h1>
            <p class="text-textMuted mt-2">We Code as One</p>
        </div>

        <!-- ---------- RIGHT: Form Panel ---------- -->
        <div class="olive p-8 max-w-md w-full mx-auto login-in" style="--d:.15s">

            <!-- Panel header -->
            <div class="border-l-4 border-secondary pl-4 mb-6">
                <h2 class="text-2xl font-bold text-primary" id="lg-title">SECURE ACCESS</h2>
                <p class="text-xs tracking-widest text-textMuted" id="lg-sub">
                    IDENTIFY YOURSELF TO PROCEED.
                </p>
            </div>

            <!-- ---------- Panel: mode choice ---------- -->
            <div class="space-y-4" data-panel="choice">

                <button class="tilt w-full flex items-center gap-4 p-5 rounded-2xl
                               border border-gray-200 bg-white text-left
                               hover:border-primary glass-btn"
                        onclick="loginMode('member')">
                    <span class="w-12 h-12 rounded-xl bg-primary text-secondary
                                 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <span>
                        <b class="text-primary block">Member Access</b>
                        <span class="text-xs text-textMuted">
                            Fines, events, forms and help desk
                        </span>
                    </span>
                </button>

                <button class="tilt w-full flex items-center gap-4 p-5 rounded-2xl
                               border border-gray-200 bg-white text-left
                               hover:border-primary glass-btn"
                        onclick="loginMode('admin')">
                    <span class="w-12 h-12 rounded-xl bg-secondary text-primary
                                 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-shield-halved"></i>
                    </span>
                    <span>
                        <b class="text-primary block">Admin Console</b>
                        <span class="text-xs text-textMuted">Officers and advisers</span>
                    </span>
                </button>
            </div>

            <!-- ---------- Panel: login form ---------- -->
            <form class="hidden space-y-4"
                  data-panel="member admin"
                  onsubmit="doLogin(event)">

                <!-- Admin-only: designation -->
                <div class="hidden" data-only="admin">
                    <label class="block text-sm font-medium mb-1">Designation</label>
                    <select class="w-full px-3 py-3 border border-gray-200 rounded-xl
                                   text-sm bg-white"
                            id="lg-role">
                        <option>President</option>
                        <option selected>Vice President</option>
                        <option>Secretary</option>
                        <option>Treasurer</option>
                        <option>Assistant Treasurer</option>
                        <option>Auditor</option>
                        <option>Assistant Auditor</option>
                        <option>Institutional</option>
                        <option>Adviser</option>
                    </select>
                </div>

                <!-- ID -->
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Student / Adviser Number
                    </label>
                    <input autocomplete="username"
                           class="w-full px-4 py-3 border border-gray-200
                                  rounded-xl text-sm focus:ring-2 focus:ring-primary"
                           id="lg-id"
                           placeholder="Student ID / ID number"
                           required />
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium mb-1">Password</label>
                    <div class="relative">
                        <input autocomplete="current-password"
                               class="w-full px-4 py-3 pr-12 border border-gray-200
                                      rounded-xl text-sm focus:ring-2 focus:ring-primary"
                               id="lg-pw"
                               placeholder="Password"
                               required
                               type="password" />
                        <button aria-label="Show password"
                                class="absolute right-3 top-3 text-textMuted"
                                onclick="togglePw('lg-pw',this)"
                                type="button">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Options row -->
                <div class="flex justify-between text-xs">
                    <label class="flex items-center gap-2 text-textMuted">
                        <input class="rounded" type="checkbox" />
                        Keep Session Active
                    </label>
                    <a class="text-primary font-medium hover:underline"
                       href="#"
                       onclick="showModal('Forgot password','Password reset needs the backend. For now, file an Account Concern ticket after contacting an officer.');return false">
                        Forgot password?
                    </a>
                </div>

                <!-- Submit -->
                <button class="w-full btn-secondary py-3 rounded-xl text-base"
                        id="lg-btn"
                        type="submit">
                    Sign In
                </button>

                <!-- Back link -->
                <div class="text-center text-sm">
                    <a class="text-textMuted hover:text-primary"
                       href="#"
                       onclick="loginMode('choice');return false">
                        <i class="fa-solid fa-arrow-left"></i> Return to dashboard
                    </a>
                </div>

                <!-- Register link (member only) -->
                <p class="text-center text-sm text-textMuted" data-only="member">
                    Don't have an account yet?
                    <a class="text-primary font-semibold hover:underline"
                       href="#"
                       onclick="loginMode('register');return false">
                        Join iBITS
                    </a>
                </p>
            </form>

            <!-- ---------- Panel: register form ---------- -->
            <form class="hidden space-y-3"
                  data-panel="register"
                  onsubmit="doRegister(event)">

                <input class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm"
                       id="rg-name"
                       placeholder="Full Name"
                       required />

                <input class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm"
                       id="rg-id"
                       placeholder="Student ID / ID number"
                       required />

                <input class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm"
                       id="rg-prog"
                       placeholder="Program &amp; Year (e.g. BSIT 2)"
                       required />

                <div class="relative">
                    <input class="w-full px-4 py-3 pr-12 border border-gray-200
                                  rounded-xl text-sm"
                           id="rg-pw"
                           oninput="checkPw()"
                           placeholder="Password"
                           required
                           type="password" />
                    <button aria-label="Show password"
                            class="absolute right-3 top-3 text-textMuted"
                            onclick="togglePw('rg-pw',this)"
                            type="button">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>

                <!-- Password rules list (populated by JS) -->
                <ul class="text-xs space-y-1 text-textMuted" id="pw-rules"></ul>

                <!-- Submit -->
                <button class="w-full btn-secondary py-3 rounded-xl" type="submit">
                    Register
                </button>

                <!-- Back link -->
                <div class="text-center text-sm">
                    <a class="text-textMuted hover:text-primary"
                       href="#"
                       onclick="loginMode('member');return false">
                        <i class="fa-solid fa-arrow-left"></i> Back to sign in
                    </a>
                </div>
            </form>

        </div><!-- /.olive -->
    </div><!-- /.grid -->
</div><!-- /#view-login -->