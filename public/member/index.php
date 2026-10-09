<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
$u = require_member();

$tickets = tickets_for_user($u['id']);
$count = fn(string $s) => count(array_filter($tickets, fn($t) => $t['status'] === $s));

dash_start('Welcome, ' . explode(' ', $u['full_name'])[0], 'index');
?>
<p class="page-intro"><?= e($u['program'] ?? '') ?> <?= e($u['year_level'] ?? '') ?> · <?= e($u['id_number']) ?></p>

<div class="stat">
  <div><b><?= $count('open') ?></b><small>Open concerns</small></div>
  <div><b><?= $count('in_progress') ?></b><small>In progress</small></div>
  <div><b><?= $count('resolved') ?></b><small>Resolved</small></div>
</div>

<div class="tiles">
  <a class="tile" href="<?= e(url('member/helpdesk.php')) ?>">
    <span class="ti"><?= icon('helpdesk') ?></span>
    <b>Help Desk</b>
    <span>View your concerns and submit a new one to the officers.</span>
  </a>
</div>
<?php dash_end(); ?>
