<?php
/** @var string $currentView */
$currentView = $currentView ?? 'public-home';
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <?php view('layouts.head', ['title' => $title ?? null]); ?>
</head>
<body class="antialiased font-sans transition-colors duration-300">

<div id="app" class="flex h-screen overflow-hidden">
    <?php view('layouts.sidebar'); ?>

    <main class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <?php view('layouts.public-nav'); ?>

        <div id="content-viewport" class="flex-1 overflow-y-auto relative bg-background">
            <?php
            $map = [
                'public-home'   => 'public/home',
                'public-about'  => 'public/about',
                'public-org'    => 'public/org',
                'public-events' => 'public/events',
                'public-faqs'   => 'public/faqs',
                'login'         => 'auth/login',
                'register'      => 'auth/login',
                'dashboard'     => 'member/dashboard',
                'fines'         => 'member/fines',
                'events'        => 'member/events',
                'forms'         => 'member/forms',
                'helpdesk'      => 'member/helpdesk',
                'settings'      => 'member/settings',
                'admin-org'     => 'admin/org',
                'admin-cbl'     => 'admin/cbl',
            ];
            $partial = $map[$currentView] ?? 'public/home';
            view($partial, ['currentView' => $currentView]);
            ?>

            <?php view('layouts.footer'); ?>
        </div>
    </main>
</div>

<?php view('layouts.modals'); ?>

<script src="<?= asset('assets/js/icons.js') ?>"></script>
<script src="<?= asset('assets/js/data.js') ?>"></script>
<script src="<?= asset('assets/js/app.js') ?>"></script>
<script src="<?= asset('assets/js/effects.js') ?>"></script>
<script src="<?= asset('assets/js/dashboard.js') ?>"></script>
<script src="<?= asset('assets/js/calendar.js') ?>"></script>
<script src="<?= asset('assets/js/tickets.js') ?>"></script>
<script src="<?= asset('assets/js/chat.js') ?>"></script>
<script src="<?= asset('assets/js/auth.js') ?>"></script>
<script src="<?= asset('assets/js/boot.js') ?>"></script>
</body>
</html>