<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
$u = require_feature('helpdesk');

if (is_post()) {
    require_manage('helpdesk');
    csrf_check();
    $id = (int)post('id');
    if (!q_val('SELECT 1 FROM tickets WHERE id = ?', [$id])) redirect('admin/helpdesk.php');
    if (post('action') === 'status' && isset(TICKET_STATUS[post('status')])) {
        q('UPDATE tickets SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?', [post('status'), $id]);
        flash('success', 'Status updated.');
    } elseif (post('action') === 'reply') {
        $msg = post('message');
        if (mb_strlen($msg) < 2 || mb_strlen($msg) > 4000) flash('error', 'Write a reply first.');
        else {
            q('INSERT INTO ticket_replies (ticket_id,user_id,message) VALUES (?,?,?)', [$id, $u['id'], $msg]);
            q("UPDATE tickets SET status = CASE WHEN status = 'open' THEN 'in_progress' ELSE status END, updated_at = CURRENT_TIMESTAMP WHERE id = ?", [$id]);
            flash('success', 'Reply sent.');
        }
    }
    redirect('admin/helpdesk.php' . (isset($_GET['status']) ? '?status=' . urlencode((string)$_GET['status']) : '') . '#t' . $id);
}
$filter = (string)($_GET['status'] ?? '');
$tickets = tickets_all($filter);

dash_start('Help Desk', 'helpdesk');
?>
<p class="page-intro">Concerns submitted by members.</p>
<div class="filters">
  <a href="<?= e(url('admin/helpdesk.php')) ?>" class="<?= $filter === '' ? 'on' : '' ?>">All</a>
  <?php foreach (TICKET_STATUS as $k => $label): ?><a href="<?= e(url('admin/helpdesk.php?status=' . $k)) ?>" class="<?= $filter === $k ? 'on' : '' ?>"><?= e($label) ?></a><?php endforeach; ?>
</div>
<?php if (!$tickets): ?><p class="empty">Nothing here.</p><?php endif; ?>
<?php foreach ($tickets as $t): ?>
  <details class="ticket" id="t<?= (int)$t['id'] ?>" <?= ($_GET['open'] ?? '') == $t['id'] ? 'open' : '' ?>>
    <summary>
      <b><?= e($t['subject']) ?></b>
      <small><?= e($t['full_name']) ?> · <?= e($t['category']) ?> · <?= e(fmt_date($t['created_at'])) ?></small>
      <?= status_badge($t['status']) ?>
    </summary>
    <div class="ticket-body">
      <p><?= e($t['description']) ?></p>
      <?php foreach ($t['replies'] as $r): ?>
        <div class="reply staff"><small><?= e($r['full_name']) ?> · <?= e($r['position'] ?? 'Officer') ?> · <?= e(fmt_date($r['created_at'], 'M j, g:i A')) ?></small><?= e($r['message']) ?></div>
      <?php endforeach; ?>
      <form method="post" class="form-light" style="margin-top:14px">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$t['id'] ?>"><input type="hidden" name="action" value="reply">
        <div class="field"><textarea name="message" style="min-height:70px" placeholder="Write a reply…" required></textarea></div>
        <button class="btn btn-navy btn-sm">Send reply</button>
      </form>
      <form method="post" class="form-light actions" style="margin-top:10px">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$t['id'] ?>"><input type="hidden" name="action" value="status">
        <div class="field" style="margin:0"><select name="status"><?php foreach (TICKET_STATUS as $k => $label): ?><option value="<?= e($k) ?>" <?= $t['status'] === $k ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></div>
        <button class="btn btn-line btn-sm">Update status</button>
      </form>
    </div>
  </details>
<?php endforeach; ?>
<?php dash_end(); ?>
