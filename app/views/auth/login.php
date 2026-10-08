<!-- View: Login / Register -->
<div id="view-login" class="view-section <?= in_array($currentView, ['login', 'register'], true) ? 'active' : '' ?> w-full min-h-screen relative overflow-hidden zd pt-24 pb-10 px-4">
    <canvas class="rain absolute inset-0 w-full h-full"></canvas>

    <div class="relative z-10 max-w-6xl mx-auto grid md:grid-cols-2 gap-10 items-center min-h-[70vh]">

        <div class="text-center login-in" style="--d:.05s">
            <div class="coin-wrap"><div class="coin"><img class="logo-img" alt="iBITS logo"></div></div>
            <h1 class="text-5xl md:text-6xl font-bold gt">iBITS Portal</h1>
            <p class="text-textMuted mt-2">We Code as One</p>
        </div>

        <div class="olive p-8 max-w-md w-full mx-auto login-in" style="--d:.15s">
            <div class="border-l-4 border-secondary pl-4 mb-6">
                <h2 id="lg-title" class="text-2xl font-bold text-primary">SECURE ACCESS</h2>
                <p id="lg-sub" class="text-xs tracking-widest text-textMuted">IDENTIFY YOURSELF TO PROCEED.</p>
            </div>

            <!-- Choice panel -->
            <div data-panel="choice" class="space-y-4">
                <button onclick="loginMode('member')"
                        class="tilt w-full flex items-center gap-4 p-5 rounded-2xl border border-gray-200 bg-white text-left hover:border-primary glass-btn">
                    <span class="w-12 h-12 rounded-xl bg-primary text-secondary flex items-center justify-center text-xl">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <span>
                        <b class="text-primary block">Member Access</b>
                        <span class="text-xs text-textMuted">Fines, events, forms and help desk</span>
                    </span>
                </button>
                <button onclick="loginMode('admin')"
                        class="tilt w-full flex items-center gap-4 p-5 rounded-2xl border border-gray-200 bg-white text-left hover:border-primary glass-btn">
                    <span class="w-12 h-12 rounded-xl bg-secondary text-primary flex items-center justify-center text-xl">
                        <i class="fa-solid fa-shield-halved"></i>
                    </span>
                    <span>
                        <b class="text-primary block">Admin Console</b>
                        <span class="text-xs text-textMuted">Officers and advisers</span>
                    </span>
                </button>
            </div>

            <!-- Login form -->
            <form data-panel="member admin" class="hidden space-y-4" onsubmit="doLogin(event)">
                <div data-only="admin" class="hidden">
                    <label class="block text-sm font-medium mb-1">Designation</label>
                    <select id="lg-role" class="w-full px-3 py-3 border border-gray-200 rounded-xl text-sm bg-white">
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
                <div>
                    <label class="block text-sm font-medium mb-1">Student / Adviser Number</label>
                    <input id="lg-id" required autocomplete="username" placeholder="Student ID / ID number"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Password</label>
                    <div class="relative">
                        <input id="lg-pw" type="password" required autocomplete="current-password" placeholder="Password"
                               class="w-full px-4 py-3 pr-12 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary">
                        <button type="button" onclick="togglePw('lg-pw', this)"
                                class="absolute right-3 top-3 text-textMuted" aria-label="Show password">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="flex justify-between text-xs">
                    <label class="flex items-center gap-2 text-textMuted">
                        <input type="checkbox" class="rounded"> Keep Session Active
                    </label>
                    <a href="#" onclick="showModal('Forgot password','Password reset needs the backend. For now, file an Account Concern ticket after contacting an officer.');return false"
                       class="text-primary font-medium hover:underline">Forgot password?</a>
                </div>
                <button id="lg-btn" type="submit" class="w-full btn-secondary py-3 rounded-xl text-base">Sign In</button>
                <div class="text-center text-sm">
                    <a href="#" onclick="loginMode('choice');return false" class="text-textMuted hover:text-primary">
                        <i class="fa-solid fa-arrow-left"></i> Return to dashboard
                    </a>
                </div>
                <p data-only="member" class="text-center text-sm text-textMuted">
                    Don't have an account yet?
                    <a href="#" onclick="loginMode('register');return false" class="text-primary font-semibold hover:underline">Join iBITS</a>
                </p>
            </form>

            <!-- Register form -->
            <form data-panel="register" class="hidden space-y-3" onsubmit="doRegister(event)">
                <input id="rg-name" required placeholder="Full Name"
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm">
                <input id="rg-id" required placeholder="Student ID / ID number"
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm">
                <input id="rg-prog" required placeholder="Program & Year (e.g. BSIT 2)"
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm">
                <div class="relative">
                    <input id="rg-pw" type="password" required oninput="checkPw()" placeholder="Password"
                           class="w-full px-4 py-3 pr-12 border border-gray-200 rounded-xl text-sm">
                    <button type="button" onclick="togglePw('rg-pw', this)"
                            class="absolute right-3 top-3 text-textMuted" aria-label="Show password">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                <ul id="pw-rules" class="text-xs space-y-1 text-textMuted"></ul>
                <button type="submit" class="w-full btn-secondary py-3 rounded-xl">Register</button>
                <div class="text-center text-sm">
                    <a href="#" onclick="loginMode('member');return false" class="text-textMuted hover:text-primary">
                        <i class="fa-solid fa-arrow-left"></i> Back to sign in
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>