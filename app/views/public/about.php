    <!-- ========================================================
         OUR POPULATION (full-width dark section)
         ======================================================== -->
    <section class="pop-section relative overflow-hidden py-20"
             style="background: #020a1c;">

        <canvas class="rain absolute inset-0 w-full h-full" data-rain="dark"></canvas>

        <div class="relative z-10 container mx-auto px-6 max-w-5xl">

            <h2 class="text-3xl md:text-4xl font-bold text-center mb-3"
                style="color: #ffffff;">
                Our Population
            </h2>

            <p class="text-center text-sm mb-12" style="color: #a9b8d6;">
                Live counts across every section and year level.
            </p>

            <?php
            $bsit = [
                'BSIT 1' => 48,
                'BSIT 2' => 50,
                'BSIT 3' => 49,
                'BSIT 4' => 48,
            ];
            $dit = [
                'DIT 1' => 44,
                'DIT 2' => 40,
                'DIT 3' => 32,
            ];
            $officers      = 38;
            $bsitTotal     = array_sum($bsit);
            $ditTotal      = array_sum($dit);
            $studentsTotal = $bsitTotal + $ditTotal;
            $overallTotal  = $studentsTotal + $officers;
            ?>

            <!-- Summary row -->
            <div class="pop-grid">
                <div class="pop-card pop-card-primary">
                    <div class="pop-num" data-target="<?= $overallTotal ?>">0</div>
                    <div class="pop-label">Total iBITS Members</div>
                </div>
                <div class="pop-card pop-card-primary">
                    <div class="pop-num" data-target="<?= $bsitTotal ?>">0</div>
                    <div class="pop-label">Total BSIT Students</div>
                </div>
                <div class="pop-card pop-card-primary">
                    <div class="pop-num" data-target="<?= $ditTotal ?>">0</div>
                    <div class="pop-label">Total DIT Students</div>
                </div>
                <div class="pop-card pop-card-primary">
                    <div class="pop-num" data-target="<?= $officers ?>">0</div>
                    <div class="pop-label">iBITS Org Officers</div>
                </div>
            </div>

            <!-- BSIT breakdown -->
            <h3 class="pop-subhead">BSIT · By Year Level</h3>
            <div class="pop-grid">
                <?php foreach ($bsit as $label => $count): ?>
                    <div class="pop-card pop-card-dark">
                        <div class="pop-num" data-target="<?= (int)$count ?>">0</div>
                        <div class="pop-label"><?= htmlspecialchars($label) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- DIT breakdown -->
            <h3 class="pop-subhead">DIT · By Year Level</h3>
            <div class="pop-grid pop-grid-3">
                <?php foreach ($dit as $label => $count): ?>
                    <div class="pop-card pop-card-dark">
                        <div class="pop-num" data-target="<?= (int)$count ?>">0</div>
                        <div class="pop-label"><?= htmlspecialchars($label) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </section>


    <!-- ========================================================
         MISSION · VISION · EVENTS (light section)
         ======================================================== -->
    <div class="wp wp-sec relative overflow-hidden py-16">

        <canvas class="rain absolute inset-0 w-full h-full"></canvas>

        <div class="relative z-10 container mx-auto px-6 max-w-5xl">

            <!-- ---------- MISSION & VISION ---------- -->
            <h2 class="text-4xl font-bold text-center mb-12 text-primary">
                Mission &amp; Vision
            </h2>

            <div class="grid md:grid-cols-2 gap-12">

                <!-- Mission -->
                <div class="about-mv-card p-8 rounded-3xl shadow-sm
                            border border-gray-100 animate-slide-up"
                     style="animation-delay: 0.1s;
                            background: #ffffff;
                            color: #1f2937;">

                    <div class="about-mv-icon about-mv-icon-navy
                                w-14 h-14 rounded-xl
                                flex items-center justify-center">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9"/>
                            <circle cx="12" cy="12" r="5"/>
                            <circle cx="12" cy="12" r="1.5"/>
                        </svg>
                    </div>

                    <h3 style="color: #06215c; font-weight: 700; font-size: 1.875rem;">
                        Our Mission
                    </h3>

                    <p style="color: #475569; line-height: 1.7;">
                        To foster a collaborative environment where IT students can
                        develop technical excellence, leadership skills, and ethical
                        professionalism through innovative projects and community
                        engagement.
                    </p>
                </div>

                <!-- Vision -->
                <div class="about-mv-card p-8 rounded-3xl shadow-sm
                            border border-gray-100 animate-slide-up"
                     style="animation-delay: 0.2s;
                            background: #ffffff;
                            color: #1f2937;">

                    <div class="about-mv-icon about-mv-icon-gold
                                w-14 h-14 rounded-xl
                                flex items-center justify-center">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </div>

                    <h3 style="color: #06215c; font-weight: 700; font-size: 1.875rem;">
                        Our Vision
                    </h3>

                    <p style="color: #475569; line-height: 1.7;">
                        To be the leading student organization that produces globally
                        competitive technology professionals who drive digital
                        transformation and social progress.
                    </p>
                </div>
            </div>