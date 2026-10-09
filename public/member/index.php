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
  <?php foreach (user_features() as $key => $level):
    if ($key === 'helpdesk') { $page = 'member/helpdesk.php'; $label = 'Help Desk'; $desc = 'Submit concerns and follow replies from officers.'; }
    else { [$label, $page] = FEATURES[$key]; $desc = 'View ' . strtolower($label) . ' information. Changes are managed by authorized officers.'; }
  ?>
  <a class="tile" href="<?= e(url($page)) ?>">
    <span class="ti"><?= icon($key) ?></span>
    <b><?= e($label) ?></b>
    <span><?= e($desc) ?></span>
  </a>
  <?php endforeach; ?>
</div>
<?php dash_end(); ?>
