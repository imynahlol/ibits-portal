<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
$u = require_feature('events');

$edit = null;
if (is_post()) {
    require_manage('events');
    csrf_check();
    $act = post('action');
    if ($act === 'delete') {
        q('DELETE FROM events WHERE id = ?', [(int)post('id')]);
        flash('success', 'Event deleted.');
        redirect('admin/events.php');
    }
    if ($act === 'save') {
        $id = (int)post('id');
        $start = str_replace('T', ' ', post('start')); $end = str_replace('T', ' ', post('end'));
        $title = post('title');
        $err = [];
        if ($title === '' || mb_strlen($title) > 150)                        $err[] = 'Enter an event title.';
        if (!strtotime($start))                                              $err[] = 'Enter a valid start date and time.';
        if ($end !== '' && (!strtotime($end) || strtotime($end) < strtotime($start))) $err[] = 'End must be after the start.';
        if ($err) { foreach ($err as $x) flash('error', $x); }
        else {
            $row = [$title, post('description'), post('category'), date('Y-m-d H:i:s', strtotime($start)),
                    $end ? date('Y-m-d H:i:s', strtotime($end)) : null, post('location'), post('is_public') === '1' ? 1 : 0];
            if ($id) q('UPDATE events SET title=?,description=?,category=?,start_datetime=?,end_datetime=?,location=?,is_public=? WHERE id=?', [...$row, $id]);
            else     q('INSERT INTO events (title,description,category,start_datetime,end_datetime,location,is_public,created_by) VALUES (?,?,?,?,?,?,?,?)', [...$row, $u['id']]);
            flash('success', $id ? 'Event updated.' : 'Event added.');
            redirect('admin/events.php');
        }
    }
}
if (isset($_GET['edit'])) $edit = q_one('SELECT * FROM events WHERE id = ?', [(int)$_GET['edit']]);
$events = q_all('SELECT * FROM events ORDER BY start_datetime DESC');
$canEdit = can_manage('events');
$dt = fn(?string $s) => $s ? str_replace(' ', 'T', substr($s, 0, 16)) : '';

dash_start('Events', 'events');
?>
<p class="page-intro">Events marked <b>public</b> appear on the homepage.</p>

<?php if ($canEdit): ?>
<section class="card">
  <h2><?= $edit ? 'Edit event' : 'Add an event' ?></h2>
  <form method="post" class="form-light">
    <?= csrf_field() ?><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
    <div class="grid2">
      <div class="field"><label>Title</label><input name="title" maxlength="150" value="<?= e($edit['title'] ?? '') ?>" required></div>
      <div class="field"><label>Category</label><input name="category" maxlength="40" placeholder="Technology, Academic…" value="<?= e($edit['category'] ?? '') ?>"></div>
      <div class="field"><label>Starts</label><input type="datetime-local" name="start" value="<?= e($dt($edit['start_datetime'] ?? null)) ?>" required></div>
      <div class="field"><label>Ends <small>(optional)</small></label><input type="datetime-local" name="end" value="<?= e($dt($edit['end_datetime'] ?? null)) ?>"></div>
      <div class="field"><label>Location</label><input name="location" maxlength="150" value="<?= e($edit['location'] ?? '') ?>"></div>
      <div class="field"><label>Visibility</label>
        <select name="is_public"><option value="1" <?= ($edit['is_public'] ?? 1) ? 'selected' : '' ?>>Public (shown on homepage)</option><option value="0" <?= isset($edit) && !$edit['is_public'] ? 'selected' : '' ?>>Members only</option></select></div>
    </div>
    <div class="field"><label>Description</label><textarea name="description"><?= e($edit['description'] ?? '') ?></textarea></div>
    <div class="actions">
      <button class="btn btn-navy" type="submit"><?= $edit ? 'Save changes' : 'Add event' ?></button>
      <?php if ($edit): ?><a class="btn btn-line" href="<?= e(url('admin/events.php')) ?>">Cancel</a><?php endif; ?>
    </div>
  </form>
</section>
<?php endif; ?>

<section class="card">
  <h2>All events <small>(<?= count($events) ?>)</small></h2>
  <?php if (!$events): ?><p class="empty">No events yet.</p><?php else: ?>
  <div class="tbl-wrap"><table>
    <thead><tr><th>Event</th><th>When</th><th>Where</th><th>Status</th><th>Visibility</th><?php if ($canEdit): ?><th></th><?php endif; ?></tr></thead>
    <tbody>
    <?php foreach ($events as $ev): $st = event_status($ev); ?>
      <tr>
        <td><b><?= e($ev['title']) ?></b><small><?= e($ev['category']) ?></small></td>
        <td><?= e(fmt_date($ev['start_datetime'], 'M j, Y g:i A')) ?></td>
        <td><?= e($ev['location']) ?></td>
        <td><span class="badge badge-<?= e($st) ?>"><?= e($st) ?></span></td>
        <td><?= $ev['is_public'] ? 'Public' : 'Members only' ?></td>
        <?php if ($canEdit): ?><td><div class="actions">
          <a class="btn btn-line btn-sm" href="<?= e(url('admin/events.php?edit=' . (int)$ev['id'])) ?>">Edit</a>
          <form method="post" class="inline" onsubmit="return confirm('Delete this event?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$ev['id'] ?>"><button class="btn btn-danger btn-sm">Delete</button></form>
        </div></td><?php endif; ?>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?php endif; ?>
</section>
<?php dash_end(); ?>
