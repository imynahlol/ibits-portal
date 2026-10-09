<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
$u = require_admin();
$features = user_features();

dash_start('Dashboard', 'index');
?>
<p class="page-intro">Signed in as <b><?= e($u['position']) ?></b>. These are the features your position can use.</p>
<div class="tiles">
  <?php foreach ($features as $key => $level): [$label, $page, $desc] = FEATURES[$key]; ?>
    <a class="tile" href="<?= e(url($page)) ?>">
      <span class="ti"><?= icon($key) ?></span>
      <b><?= e($label) ?> <?php if ($level === 'view'): ?><span class="badge badge-view">View only</span><?php endif; ?></b>
      <span><?= e($desc) ?></span>
    </a>
  <?php endforeach; ?>
</div>
<?php dash_end(); ?>
