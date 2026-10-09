<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
$u = require_feature('fines');

if (is_post()) {
    require_manage('fines');
    csrf_check();
    $act = post('action');
    if ($act === 'toggle') {
        $f = q_one('SELECT status FROM fines WHERE id = ?', [(int)post('id')]);
        if ($f) q('UPDATE fines SET status = ? WHERE id = ?', [$f['status'] === 'paid' ? 'unpaid' : 'paid', (int)post('id')]);
        flash('success', 'Fine updated.');
    } elseif ($act === 'delete') {
        q('DELETE FROM fines WHERE id = ?', [(int)post('id')]);
        flash('success', 'Fine deleted.');
    } else {
        $amount = filter_var(post('amount'), FILTER_VALIDATE_FLOAT);
        $due = post('due_date');
        if (!q_val("SELECT 1 FROM users WHERE id = ? AND account_type = 'member'", [(int)post('user_id')])) flash('error', 'Choose a member.');
        elseif ($amount === false || $amount <= 0 || $amount > 100000) flash('error', 'Enter a valid amount.');
        elseif (post('reason') === '')                                  flash('error', 'Enter the reason for the fine.');
        elseif (!strtotime($due))                                       flash('error', 'Enter a due date.');
        else {
            q('INSERT INTO fines (user_id,amount,reason,due_date,created_by) VALUES (?,?,?,?,?)',
              [(int)post('user_id'), round($amount, 2), post('reason'), date('Y-m-d', strtotime($due)), $u['id']]);
            flash('success', 'Fine issued.');
        }
    }
    redirect('admin/fines.php');
}
$fines = q_all('SELECT f.*, m.full_name, m.id_number FROM fines f JOIN users m ON m.id = f.user_id ORDER BY f.id DESC');
$unpaid = array_sum(array_map(fn($f) => $f['status'] === 'unpaid' ? (float)$f['amount'] : 0, $fines));
$paid   = array_sum(array_map(fn($f) => $f['status'] === 'paid'   ? (float)$f['amount'] : 0, $fines));
$canEdit = can_manage('fines');

dash_start('Fines', 'fines');
?>
<?php if (!$canEdit): ?><div class="alert alert-info">Your position (<?= e($u['position']) ?>) can view fines but not change them.</div><?php endif; ?>
<div class="stat">
  <div><b>₱<?= number_format($unpaid, 2) ?></b><small>Unpaid</small></div>
  <div><b>₱<?= number_format($paid, 2) ?></b><small>Collected</small></div>
  <div><b><?= count($fines) ?></b><small>Total fines</small></div>
</div>

<?php if ($canEdit): ?>
<section class="card">
  <h2>Issue a fine</h2>
  <form method="post" class="form-light">
    <?= csrf_field() ?>
    <div class="grid2">
      <div class="field"><label>Member</label>
        <select name="user_id" required><option value="">Select a member</option>
          <?php foreach (members_list() as $m): ?><option value="<?= (int)$m['id'] ?>"><?= e($m['full_name']) ?> (<?= e($m['id_number']) ?>)</option><?php endforeach; ?>
        </select></div>
      <div class="field"><label>Amount (₱)</label><input name="amount" type="number" min="1" step="0.01" required></div>
      <div class="field"><label>Reason</label><input name="reason" maxlength="200" required></div>
      <div class="field"><label>Due date</label><input name="due_date" type="date" required></div>
    </div>
    <button class="btn btn-navy" type="submit">Issue fine</button>
  </form>
</section>
<?php endif; ?>

<section class="card">
  <h2>All fines</h2>
  <?php if (!$fines): ?><p class="empty">No fines issued.</p><?php else: ?>
  <div class="tbl-wrap"><table>
    <thead><tr><th>Member</th><th>Reason</th><th>Amount</th><th>Due</th><th>Status</th><?php if ($canEdit): ?><th></th><?php endif; ?></tr></thead>
    <tbody>
    <?php foreach ($fines as $f): ?>
      <tr>
        <td><b><?= e($f['full_name']) ?></b><small><?= e($f['id_number']) ?></small></td>
        <td><?= e($f['reason']) ?></td>
        <td>₱<?= number_format((float)$f['amount'], 2) ?></td>
        <td><?= e(fmt_date($f['due_date'])) ?></td>
        <td><span class="badge badge-<?= e($f['status']) ?>"><?= e(ucfirst($f['status'])) ?></span></td>
        <?php if ($canEdit): ?><td><div class="actions">
          <form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= (int)$f['id'] ?>"><button class="btn btn-line btn-sm"><?= $f['status'] === 'paid' ? 'Mark unpaid' : 'Mark paid' ?></button></form>
          <form method="post" class="inline" onsubmit="return confirm('Delete this fine?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$f['id'] ?>"><button class="btn btn-danger btn-sm">Delete</button></form>
        </div></td><?php endif; ?>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?php endif; ?>
</section>
<?php dash_end(); ?>
