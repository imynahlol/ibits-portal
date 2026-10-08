<?php
    $rightSideClass = ' wide';
    $showTopNav     = true;
    $activeNav      = 'dashboard';
    require __DIR__ . '/../layouts/header.php';
?>

<div class="card wide">
    <div class="header-row">
        <div>
            <h2 style="text-align:left; margin-bottom:4px;">My Fines</h2>
            <p class="subtitle" style="text-align:left; margin:0;">
                Your personal fines record
            </p>
        </div>
        <a class="btn btn-secondary btn-sm"
           href="<?= e($_ENV['APP_URL']) ?>/dashboard">← Back to Dashboard</a>
    </div>

    <!-- Summary KPIs -->
    <div class="kpi-grid">
        <div class="kpi">
            <div class="kpi-label">Total Fines</div>
            <div class="kpi-value">₱<?= number_format($summary['total'], 2) ?></div>
        </div>
        <div class="kpi <?= $summary['unpaid'] > 0 ? 'kpi-danger' : 'kpi-ok' ?>">
            <div class="kpi-label">Unpaid Balance</div>
            <div class="kpi-value">₱<?= number_format($summary['unpaid'], 2) ?></div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Record Count</div>
            <div class="kpi-value"><?= (int)$summary['count'] ?></div>
        </div>
    </div>

    <!-- fines table -->
    <?php if (empty($fines)): ?>
        <p class="muted" style="margin-top:20px;">
            You have no fines on record. 🎉
        </p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Reason</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Issued</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fines as $i => $fine): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= e($fine['reason']) ?></td>
                            <td>₱<?= number_format((float)$fine['amount'], 2) ?></td>
                            <td>
                                <?php if ($fine['status'] === 'paid'): ?>
                                    <span class="status-paid">Paid</span>
                                <?php else: ?>
                                    <span class="status-unpaid">Unpaid</span>
                                <?php endif; ?>
                            </td>
                            <td><?= e(date('M d, Y', strtotime($fine['due_date']))) ?></td>
                            <td><?= e(date('M d, Y', strtotime($fine['created_at']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="nav-footer">
        Disputes? Submit a ticket via the Help Desk.
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>