<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
$u = require_member();

$f = ['subject' => '', 'category' => 'General', 'description' => ''];
if (is_post()) {
    csrf_check();
    foreach ($f as $k => $_) $f[$k] = post($k);
    $err = [];
    if (mb_strlen($f['subject']) < 3 || mb_strlen($f['subject']) > 150) $err[] = 'Add a short subject (3 to 150 characters).';
    if (!in_array($f['category'], TICKET_CATEGORIES, true))             $err[] = 'Choose a category.';
    if (mb_strlen($f['description']) < 10)                               $err[] = 'Please describe your concern in a little more detail.';
    if (mb_strlen($f['description']) > 4000)                             $err[] = 'Description is too long (4000 characters max).';
    if (!$err) {
        q('INSERT INTO tickets (user_id,subject,category,description) VALUES (?,?,?,?)',
          [$u['id'], $f['subject'], $f['category'], $f['description']]);
        flash('success', 'Your concern was submitted. An officer will reply here.');
        redirect('member/helpdesk.php');
    }
    foreach ($err as $x) flash('error', $x);
}

$tickets = tickets_for_user($u['id']);
dash_start('Help Desk', 'helpdesk');
?>
<p class="page-intro">Send a concern to the officers and follow their replies here.</p>

<section class="card">
  <h2>Submit a concern</h2>
  <form method="post" class="form-light">
    <?= csrf_field() ?>
    <div class="grid2">
      <div class="field"><label for="subject">Subject</label>
        <input id="subject" name="subject" maxlength="150" value="<?= e($f['subject']) ?>" required></div>
      <div class="field"><label for="category">Category</label>
        <select id="category" name="category"><?php foreach (TICKET_CATEGORIES as $c): ?>
          <option <?= $f['category'] === $c ? 'selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?></select></div>
    </div>
    <div class="field"><label for="description">What happened?</label>
      <textarea id="description" name="description" maxlength="4000" required><?= e($f['description']) ?></textarea></div>
    <button class="btn btn-navy" type="submit">Submit concern</button>
  </form>
</section>

<section class="card">
  <h2>Your concerns <small>(<?= count($tickets) ?>)</small></h2>
  <?php if (!$tickets): ?><p class="empty">You haven't submitted any concerns yet.</p><?php endif; ?>
  <?php foreach ($tickets as $t): ?>
    <details class="ticket">
      <summary>
        <b><?= e($t['subject']) ?></b>
        <small><?= e($t['category']) ?> · <?= e(fmt_date($t['created_at'])) ?></small>
        <?= status_badge($t['status']) ?>
      </summary>
      <div class="ticket-body">
        <p><?= e($t['description']) ?></p>
        <?php foreach ($t['replies'] as $r): ?>
          <div class="reply staff"><small><?= e($r['full_name']) ?> · <?= e($r['position'] ?? 'Officer') ?> · <?= e(fmt_date($r['created_at'], 'M j, g:i A')) ?></small><?= e($r['message']) ?></div>
        <?php endforeach; ?>
        <?php if (!$t['replies']): ?><small style="color:var(--muted)">No reply yet.</small><?php endif; ?>
      </div>
    </details>
  <?php endforeach; ?>
</section>
<?php dash_end(); ?>
