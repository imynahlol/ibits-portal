<?php
    $rightSideClass = ' wide';
    $showTopNav     = true;
    $activeNav      = 'dashboard';
    require __DIR__ . '/../layouts/header.php';

$roleLabel = [
    'member'         => 'Member',
    'president'      => 'President',
    'vice_president' => 'Vice President',
    'secretary'      => 'Secretary',
    'treasurer'      => 'Treasurer',
    'auditor'        => 'Auditor',
    'adviser'        => 'Adviser',
]
    [$role] ?? ucfirst($role);
?>

<div class="card wide">
    <div class="header-row">
        <div>
            <h2 style="text-align:left; margin-bottom:4px;">
                Welcome, <?= e($user_name) ?>
            </h2>
            <p class="subtitle" style="text-align:left; margin:0;">
                Signed in as <span class="badge"><?= e($roleLabel) ?></span>
            </p>
        </div>
    </div>

    <!-- KPI cards -->
    <div class="kpi-grid">
        <div class="kpi <?= $fine_summary['unpaid_count'] > 0 ? 'kpi-danger' : 'kpi-ok' ?>">
            <div class="kpi-label">Unpaid Fines</div>
            <div class="kpi-value">
                <?= $fine_summary['unpaid_count'] > 0
                    ? '₱' . number_format($fine_summary['unpaid'], 2)
                    : 'None' ?>
            </div>
        </div>

        <div class="kpi kpi-warn">
            <div class="kpi-label">Upcoming Events</div>
            <div class="kpi-value"><?= count($upcoming) ?></div>
        </div>

        <div class="kpi <?= $ticket_count > 0 ? 'kpi-warn' : 'kpi-ok' ?>">
            <div class="kpi-label">Open Tickets</div>
            <div class="kpi-value"><?= (int)$ticket_count ?></div>
        </div>
    </div>

    <!-- quick access -->
    <section class="section">
        <h2>Quick Access</h2>
        <div class="grid-3">
            <div class="card">
                <h2>My Fines</h2>
                <p class="muted">View your personal fines and status.</p>
                <a class="btn btn-sm" href="<?= e($_ENV['APP_URL']) ?>/fines">Open</a>
            </div>
            <div class="card">
                <h2>Events / GPOA</h2>
                <p class="muted">Check the organization calendar.</p>
                <a class="btn btn-sm" href="<?= e($_ENV['APP_URL']) ?>/my-events">Open</a>
            </div>
            <div class="card">
                <h2>Forms Hub</h2>
                <p class="muted">Excuse Letter, CBL, and more.</p>
                <a class="btn btn-sm" href="#" onclick="alert('Coming soon'); return false;">Open</a>
            </div>
            <div class="card">
                <h2>Help Desk</h2>
                <p class="muted">Submit &amp; track STRAW support tickets.</p>
                <a class="btn btn-sm" href="#" onclick="alert('Coming soon'); return false;">Open</a>
            </div>
        </div>
    </section>

    <!-- upcoming events preview -->
    <?php if (!empty($upcoming)): ?>
        <section class="section">
            <h2>Next Events</h2>
            <div class="event-grid">
                <?php foreach ($upcoming as $ev): ?>
                    <div class="event-card">
                        <span class="badge badge-<?= e($ev['category']) ?>">
                            <?= e(ucfirst($ev['category'])) ?>
                        </span>
                        <h3><?= e($ev['title']) ?></h3>
                        <p class="meta">
                            <?= e(date('M d, Y · g:i A', strtotime($ev['start_datetime']))) ?>
                            <?php if (!empty($ev['location'])): ?>
                                · <?= e($ev['location']) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>