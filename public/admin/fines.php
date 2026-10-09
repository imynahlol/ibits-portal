<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
$u = require_feature('fines');

$canEdit = is_admin() && can_manage('fines');
if (is_post()) {
    require_manage('fines');
    csrf_check();
    $act = post('action'); $id = (int)post('id');
    if ($act === 'toggle') {
        $f = q_one('SELECT status FROM fines WHERE id = ?', [$id]);
        if ($f) q('UPDATE fines SET status = ? WHERE id = ?', [$f['status'] === 'paid' ? 'unpaid' : 'paid', $id]);
        flash('success', 'Fine status updated.');
    } elseif ($act === 'delete') {
        q('DELETE FROM fines WHERE id = ?', [$id]);
        flash('success', 'Fine deleted.');
    } else {
        $memberIdNumber = strtoupper(post('member_id_number'));
        $member = q_one("SELECT id, full_name FROM users WHERE id_number = ? AND account_type = 'member' AND status = 'active'", [$memberIdNumber]);
        $userId = (int)($member['id'] ?? 0);
        $amount = filter_var(post('amount'), FILTER_VALIDATE_FLOAT);
        $due = post('due_date'); $reason = trim(post('reason'));
        if (!$member) flash('error', 'No active member account matches that student ID number. Check the ID and try again.');
        elseif ($amount === false || $amount <= 0 || $amount > 100000) flash('error', 'Enter a valid amount.');
        elseif ($reason === '' || mb_strlen($reason) > 200) flash('error', 'Enter a reason (maximum 200 characters).');
        elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $due) || !strtotime($due)) flash('error', 'Enter a valid due date.');
        elseif ($act === 'update' && !q_val('SELECT 1 FROM fines WHERE id = ?', [$id])) flash('error', 'That fine could not be found.');
        else {
            if ($act === 'update') {
                q('UPDATE fines SET user_id=?, amount=?, reason=?, due_date=? WHERE id=?', [$userId, round($amount,2), $reason, $due, $id]);
                flash('success', 'Fine updated.');
            } else {
                q('INSERT INTO fines (user_id,amount,reason,due_date,created_by) VALUES (?,?,?,?,?)', [$userId, round($amount,2), $reason, $due, $u['id']]);
                flash('success', 'Fine issued.');
            }
        }
    }
    redirect('admin/fines.php');
}
$edit = ($canEdit && isset($_GET['edit'])) ? q_one('SELECT f.*, m.id_number AS member_id_number, m.full_name AS member_name FROM fines f JOIN users m ON m.id = f.user_id WHERE f.id = ?', [(int)$_GET['edit']]) : null;
if (($u['account_type'] ?? '') === 'member' || (!$canEdit && is_admin())) {
    // Members and read-only officers see only their own personal fines.
    $fines = q_all('SELECT f.*, m.full_name, m.id_number FROM fines f JOIN users m ON m.id = f.user_id WHERE f.user_id = ? ORDER BY f.id DESC', [$u['id']]);
} else {
    $fines = q_all('SELECT f.*, m.full_name, m.id_number FROM fines f JOIN users m ON m.id = f.user_id ORDER BY f.id DESC');
}
$unpaid = array_sum(array_map(fn($f) => $f['status'] === 'unpaid' ? (float)$f['amount'] : 0, $fines));
$paid   = array_sum(array_map(fn($f) => $f['status'] === 'paid'   ? (float)$f['amount'] : 0, $fines));
$canEdit = is_admin() && can_manage('fines');

dash_start('Fines', 'fines');
?>
<?php if (($u['account_type'] ?? '') === 'member' || !$canEdit): ?><p class="page-intro">View your personal fines and payment status.</p><?php endif; ?>
<?php if (!$canEdit && ($u['account_type'] ?? '') === 'admin'): ?><div class="alert alert-info">Your position can view portal features, but this page shows only your personal fines.</div><?php endif; ?>
<div class="stat">
  <div><b>₱<?= number_format($unpaid, 2) ?></b><small>Unpaid</small></div>
  <div><b>₱<?= number_format($paid, 2) ?></b><small>Collected</small></div>
  <div><b><?= count($fines) ?></b><small>Total fines</small></div>
</div>

<?php if ($canEdit): ?>
<section class="card">
  <h2><?= $edit ? 'Edit fine' : 'Issue a fine' ?></h2>
  <form method="post" class="form-light">
    <?= csrf_field() ?><input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
    <div class="grid2">
      <div class="field"><label for="member_id_number">Member student ID number</label>
        <input id="member_id_number" name="member_id_number" type="text" maxlength="15" minlength="15" pattern="[0-9]{4}-[0-9]{5}-LQ-[0-9]" placeholder="2024-12345-LQ-0" value="<?= e($edit['member_id_number'] ?? '') ?>" autocomplete="off" required>
        <small>Type the member's exact student ID. The fine will be linked to that member's active account.</small>
        <?php if (!empty($edit['member_name'])): ?><small>Linked account: <?= e($edit['member_name']) ?></small><?php endif; ?></div>
      <div class="field"><label>Amount (₱)</label><input name="amount" type="number" min="1" step="0.01" value="<?= e((string)($edit['amount'] ?? '')) ?>" required></div>
      <div class="field"><label>Reason</label><input name="reason" maxlength="200" value="<?= e($edit['reason'] ?? '') ?>" required></div>
      <div class="field"><label>Due date</label><input name="due_date" type="date" value="<?= e($edit['due_date'] ?? '') ?>" required></div>
    </div>
    <div class="actions"><button class="btn btn-navy" type="submit"><?= $edit ? 'Save changes' : 'Issue fine' ?></button><?php if ($edit): ?><a class="btn btn-line" href="<?= e(url('admin/fines.php')) ?>">Cancel</a><?php endif; ?></div>
  </form>
</section>
<?php endif; ?>

<section class="card">
  <h2><?= (($u['account_type'] ?? '') === 'member' || !$canEdit) ? 'My fines' : 'All fines' ?></h2>
  <?php if (!$fines): ?><p class="empty">No fines issued.</p><?php else: ?>
  <div class="tbl-wrap"><table>
    <thead><tr><?php if ($canEdit): ?><th>Member</th><?php endif; ?><th>Reason</th><th>Amount</th><th>Due</th><th>Status</th><?php if ($canEdit): ?><th></th><?php endif; ?></tr></thead>
    <tbody>
    <?php foreach ($fines as $f): ?>
      <tr>
        <?php if ($canEdit): ?><td><b><?= e($f['full_name']) ?></b><small><?= e($f['id_number']) ?></small></td><?php endif; ?>
        <td><?= e($f['reason']) ?></td>
        <td>₱<?= number_format((float)$f['amount'], 2) ?></td>
        <td><?= e(fmt_date($f['due_date'])) ?></td>
        <td><span class="badge badge-<?= e($f['status']) ?>"><?= e(ucfirst($f['status'])) ?></span></td>
        <?php if ($canEdit): ?><td><div class="actions">
          <a class="btn btn-line btn-sm" href="<?= e(url('admin/fines.php?edit='.(int)$f['id'])) ?>">Edit</a>
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
