<!--HOME/LANDING PAGE--->

<div class="view-section active w-full min-h-full" id="view-public-home">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;800&display=swap" rel="stylesheet">
    <section class="zd relative overflow-hidden text-center pt-40 pb-24"
             style="background: #020a1c;">

        <canvas class="rain absolute inset-0 w-full h-full"></canvas>

        <div class="relative z-10 px-6 max-w-4xl mx-auto flex flex-col items-center">

            <!-- LOGO -->
            <div class="relative mb-6 animate-slide-up">
                <img src="<?= asset('assets/images/ibits-logo.png') ?>"
                     alt="iBITS Logo - Institute of Brilliant Information Technology Students"
                     class="w-44 md:w-72 h-auto rounded-full relative z-10"
                     style="filter: drop-shadow(0 18px 30px rgba(10, 36, 99, 0.25));">
                <!-- soft floor shadow under the logo -->
                <div class="absolute left-1/2 -bottom-3 -translate-x-1/2 w-3/4 h-5 rounded-full"
                     style="background: radial-gradient(ellipse at center, rgba(10,36,99,0.25), transparent 70%);
                            transform: translateX(-50%);"></div>
            </div>

            <!-- BADGE -->
            <span class="px-4 py-1 rounded-full
                         bg-white bg-opacity-10
                         text-secondary text-sm font-semibold tracking-wider
                         mb-6
                         border border-white border-opacity-20
                         animate-slide-up">
                OFFICIAL PORTAL
            </span>

            <!-- iBITS MOTTO -->
            <h1 class="text-5xl md:text-7xl font-light text-white mb-6 leading-tight animate-slide-up"
                style="animation-delay: 0.1s;">
                <b class="font-bold">WE CODE AS ONE.</b>
            </h1>

            <!-- DESCRIPTION -->
            <p class="text-sm md:text-base text-slate-300 mb-9 max-w-xl mx-auto animate-slide-up"
               style="animation-delay: 0.2s;">
                At iBITS, we unite brilliant minds and passionate builders under
                a shared passion for technology to forge innovation together as
                a single, unified force.
            </p>

            <!-- CTA buttons -->
            <div class="flex gap-4 animate-slide-up"
                 style="animation-delay: 0.3s;">
                <button onclick="navigate('register')"
                        class="btn-secondary px-8 py-3 rounded-full text-lg shadow-lg">
                    Join the Org
                </button>

                <button onclick="navigate('login')"
                        class="bg-white bg-opacity-10 hover:bg-opacity-20
                               text-white border border-white border-opacity-20
                               px-8 py-3 rounded-full text-lg font-medium transition-all">
                    Member Login
                </button>
            </div>

        </div>
    </section>

<!-- Statistics / Counters -->
<div class="zd py-14 border-t border-white/10" style="background:#030c24"><div class="container mx-auto px-6"><h2 class="text-2xl font-bold text-white text-center mb-8">iBITS Population</h2><div class="space-y-6" id="pop-stats"></div></div></div>
<div class="wp wp-sec relative overflow-hidden py-16">
<canvas class="rain absolute inset-0 w-full h-full"></canvas>
<div class="relative z-10 container mx-auto px-6 max-w-5xl">
<h2 class="text-4xl font-bold text-center mb-10 text-primary">Who We Are</h2>
<div class="grid md:grid-cols-2 gap-10 items-center">
<div class="tilt navy-card p-8"><h3 class="text-2xl font-bold mb-1">We Are More Than an Academic Organization</h3><p class="text-secondary font-semibold mb-4">A Unified Force for Tech Innovators.</p><p class="text-sm leading-relaxed text-blue-100">At the <b>Institute of Brilliant Information Technology Students</b>, we unite brilliant minds and passionate builders under a shared love for technology to forge innovation together as a single, unified force.</p><p class="text-sm mt-3 text-blue-100">We create spaces where students don't just study technology, <b>we code as one.</b></p></div>
<div class="coin-wrap"><div class="coin"><img alt="iBITS logo" class="logo-img"/></div></div>
</div>
<div class="grid md:grid-cols-2 gap-10 mt-20">
<div><h3 class="text-4xl font-bold text-primary mb-4">Mission</h3><div class="tilt navy-card p-8 min-h-[190px]"><p class="text-sm leading-relaxed text-blue-100">To foster a collaborative environment where IT students can develop technical excellence, leadership skills, and ethical professionalism through innovative projects and community engagement.</p></div></div>
<div class="md:mt-16"><h3 class="text-4xl font-bold text-primary mb-4 text-center">Vision</h3><div class="tilt yellow-card p-8 min-h-[190px]"><p class="text-sm leading-relaxed">To be the leading student organization that produces globally competitive technology professionals who drive digital transformation and social progress.</p></div></div>
</div>
<h2 class="text-4xl font-bold text-center mt-20 mb-8 text-primary">Events</h2>
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5" id="home-events"></div>
</div>
</div>
</div>
