<?php require __DIR__ . '/../layouts/header.php'; ?>

<h2>Welcome, <?= e($user_name) ?></h2>
<p>Your role: <strong><?= e($role) ?></strong></p>

<div class="card-grid">
    <div class="card">
        <h3>My Fines</h3>
        <p>View your personal fines and status.</p>
    </div>
    <div class="card">
        <h3>Events / GPOA</h3>
        <p>Check the organization calendar.</p>
    </div>
    <div class="card">
        <h3>Forms Hub</h3>
        <p>Excuse Letter, CBL, and more.</p>
    </div>
    <div class="card">
        <h3>Help Desk</h3>
        <p>Submit and track STRAW support tickets.</p>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>