<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
$u = require_feature('finance');
$canEdit = is_admin() && can_manage('finance');

if (is_post()) {
    require_manage('finance');
    csrf_check();
    $act = post('action'); $id = (int)post('id');
    if ($act === 'delete') {
        q('DELETE FROM finances WHERE id = ?', [$id]);
        flash('success', 'Finance record deleted.');
        redirect('admin/finance.php');
    }
    $date = post('date_'); $eventId = (int)post('event_id');
    $budget = filter_var(post('budget'), FILTER_VALIDATE_FLOAT);
    $expense = filter_var(post('expense'), FILTER_VALIDATE_FLOAT);
    $inflow = filter_var(post('inflow'), FILTER_VALIDATE_FLOAT);
    $notes = trim(post('event_'));
    $event = q_one('SELECT id FROM events WHERE id = ?', [$eventId]);
    if (!$event) flash('error', 'Choose an event.');
    elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !strtotime($date)) flash('error', 'Enter a valid date.');
    elseif ($budget === false || $expense === false || $inflow === false || min($budget, $expense, $inflow) < 0) flash('error', 'Amounts must be zero or greater.');
    elseif ($notes === '') flash('error', 'Add a short description or note.');
    elseif ($act === 'update' && !q_val('SELECT 1 FROM finances WHERE id = ?', [$id])) flash('error', 'That finance record could not be found.');
    else {
        $values = [$date, $notes, $eventId, round($budget,2), round($expense,2), round($inflow,2)];
        if ($act === 'update') {
            q('UPDATE finances SET date_=?, event_=?, event_id=?, budget=?, expense=?, inflow=?, updated_at=CURRENT_TIMESTAMP WHERE id=?', [...$values, $id]);
            flash('success', 'Finance record updated.');
        } else {
            q('INSERT INTO finances (date_,event_,event_id,budget,expense,inflow,created_by) VALUES (?,?,?,?,?,?,?)', [...$values, $u['id']]);
            flash('success', 'Finance record added.');
        }
    }
    redirect('admin/finance.php');
}
$events = q_all('SELECT id,title FROM events ORDER BY start_datetime DESC');
$edit = isset($_GET['edit']) ? q_one('SELECT * FROM finances WHERE id = ?', [(int)$_GET['edit']]) : null;
if ($edit && !$canEdit) $edit = null;
$records = q_all('SELECT f.*, e.title AS event_title, u.full_name AS created_name FROM finances f JOIN events e ON e.id=f.event_id JOIN users u ON u.id=f.created_by ORDER BY f.date_ DESC, f.id DESC');
$totalBudget = array_sum(array_map(fn($r)=>(float)$r['budget'],$records));
$totalExpense = array_sum(array_map(fn($r)=>(float)$r['expense'],$records));
$totalInflow = array_sum(array_map(fn($r)=>(float)$r['inflow'],$records));
dash_start('Finances', 'finance');
?>
<div class="stat"><div><b>₱<?= number_format($totalBudget,2) ?></b><small>Total budget</small></div><div><b>₱<?= number_format($totalExpense,2) ?></b><small>Total expenses</small></div><div><b>₱<?= number_format($totalInflow,2) ?></b><small>Total income / inflow</small></div><div><b>₱<?= number_format($totalInflow-$totalExpense,2) ?></b><small>Net balance</small></div></div>
<?php if ($canEdit): ?>
<section class="card"><h2><?= $edit ? 'Edit finance record' : 'Add a finance record' ?></h2>
<?php if (!$events): ?><p class="alert alert-info">Add an event first before recording its budget and expenses.</p><?php else: ?>
<form method="post" class="form-light"><?= csrf_field() ?><input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
<div class="grid2"><div class="field"><label for="date_">Date</label><input id="date_" name="date_" type="date" value="<?= e($edit['date_'] ?? date('Y-m-d')) ?>" required></div>
<div class="field"><label for="event_id">Event</label><select id="event_id" name="event_id" required><option value="">Select event</option><?php foreach ($events as $ev): ?><option value="<?= (int)$ev['id'] ?>" <?= (int)($edit['event_id'] ?? 0)===(int)$ev['id']?'selected':'' ?>><?= e($ev['title']) ?></option><?php endforeach; ?></select></div>
<div class="field"><label for="budget">Budget (₱)</label><input id="budget" name="budget" type="number" min="0" step="0.01" value="<?= e((string)($edit['budget'] ?? '0')) ?>" required></div>
<div class="field"><label for="expense">Expense (₱)</label><input id="expense" name="expense" type="number" min="0" step="0.01" value="<?= e((string)($edit['expense'] ?? '0')) ?>" required></div>
<div class="field"><label for="inflow">Income / inflow (₱)</label><input id="inflow" name="inflow" type="number" min="0" step="0.01" value="<?= e((string)($edit['inflow'] ?? '0')) ?>" required></div>
<div class="field"><label for="event_">Notes / description</label><input id="event_" name="event_" maxlength="500" value="<?= e($edit['event_'] ?? '') ?>" required></div></div>
<div class="actions"><button class="btn btn-navy" type="submit"><?= $edit ? 'Save changes' : 'Add record' ?></button><?php if ($edit): ?><a class="btn btn-line" href="<?= e(url('admin/finance.php')) ?>">Cancel</a><?php endif; ?></div></form><?php endif; ?></section>
<?php else: ?><div class="alert alert-info">Read-only access for your position.</div><?php endif; ?>
<section class="card"><h2>Finance ledger</h2><?php if (!$records): ?><p class="empty">No finance records have been added.</p><?php else: ?><div class="tbl-wrap"><table><thead><tr><th>Date</th><th>Event / Notes</th><th>Budget</th><th>Expense</th><th>Income</th><th>Net</th><?php if ($canEdit): ?><th>Actions</th><?php endif; ?></tr></thead><tbody>
<?php foreach ($records as $r): ?><tr><td><?= e(fmt_date($r['date_'])) ?></td><td><b><?= e($r['event_title']) ?></b><small><?= e($r['event_']) ?></small></td><td>₱<?= number_format((float)$r['budget'],2) ?></td><td>₱<?= number_format((float)$r['expense'],2) ?></td><td>₱<?= number_format((float)$r['inflow'],2) ?></td><td>₱<?= number_format((float)$r['inflow']-(float)$r['expense'],2) ?></td>
<?php if ($canEdit): ?><td><div class="actions"><a class="btn btn-line btn-sm" href="<?= e(url('admin/finance.php?edit='.(int)$r['id'])) ?>">Edit</a><form method="post" onsubmit="return confirm('Delete this finance record?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn btn-danger btn-sm" type="submit">Delete</button></form></div></td><?php endif; ?></tr><?php endforeach; ?>
</tbody></table></div><?php endif; ?></section><?php dash_end(); ?>
