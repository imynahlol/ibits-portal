<?php
    $rightSideClass = ' wide';
    $showTopNav     = true;
    $activeNav      = 'events';
    require __DIR__ . '/../layouts/header.php';
?>

<div class="card wide">
    <div class="header-row">
        <div>
            <h2 style="text-align:left; margin-bottom:4px;">Events / GPOA</h2>
            <p class="subtitle" style="text-align:left; margin:0;">
                Organization calendar — view-only
            </p>
        </div>
        <a class="btn btn-secondary btn-sm"
           href="<?= e($_ENV['APP_URL']) ?>/dashboard">← Back to Dashboard</a>
    </div>

    <!-- filters -->
    <div class="filter-bar">
        <a href="<?= e($_ENV['APP_URL']) ?>/my-events"
           class="<?= $category === '' ? 'active' : '' ?>">All</a>
        <a href="<?= e($_ENV['APP_URL']) ?>/my-events?category=past"
           class="<?= $category === 'past' ? 'active' : '' ?>">Past</a>
        <a href="<?= e($_ENV['APP_URL']) ?>/my-events?category=current"
           class="<?= $category === 'current' ? 'active' : '' ?>">Current</a>
        <a href="<?= e($_ENV['APP_URL']) ?>/my-events?category=ongoing"
           class="<?= $category === 'ongoing' ? 'active' : '' ?>">Ongoing</a>
        <a href="<?= e($_ENV['APP_URL']) ?>/my-events?category=future"
           class="<?= $category === 'future' ? 'active' : '' ?>">Future</a>
    </div>

    <!-- events grid -->
    <?php if (empty($events)): ?>
        <p class="muted">No events to show.</p>
    <?php else: ?>
        <div class="event-grid">
            <?php foreach ($events as $ev): ?>
                <div class="event-card">
                    <span class="badge badge-<?= e($ev['category']) ?>">
                        <?= e(ucfirst($ev['category'])) ?>
                    </span>
                    <h3><?= e($ev['title']) ?></h3>
                    <p class="meta">
                        <?= e(date('M d, Y · g:i A', strtotime($ev['start_datetime']))) ?>
                        <?php if (!empty($ev['end_datetime'])): ?>
                            → <?= e(date('M d, Y · g:i A', strtotime($ev['end_datetime']))) ?>
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($ev['location'])): ?>
                        <p class="meta">📍 <?= e($ev['location']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($ev['description'])): ?>
                        <p><?= nl2br(e($ev['description'])) ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>