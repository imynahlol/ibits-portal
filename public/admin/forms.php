<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
$u = require_feature('forms');

if (is_post()) {
    require_manage('forms');
    csrf_check();
    if (post('action') === 'delete') {
        q('DELETE FROM forms WHERE id = ?', [(int)post('id')]);
        flash('success', 'Form removed.');
    } else {
        $title = post('title'); $link = post('link');
        if ($title === '' || mb_strlen($title) > 150)                      flash('error', 'Enter a form title.');
        elseif (!preg_match('#^https?://#i', $link) || !filter_var($link, FILTER_VALIDATE_URL)) flash('error', 'Enter a valid link starting with http:// or https://');
        else {
            q('INSERT INTO forms (title,description,link,created_by) VALUES (?,?,?,?)', [$title, post('description'), $link, $u['id']]);
            flash('success', 'Form added.');
        }
    }
    redirect('admin/forms.php');
}
$forms = q_all('SELECT f.*, u.full_name FROM forms f JOIN users u ON u.id = f.created_by ORDER BY f.id DESC');
$canEdit = can_manage('forms');

dash_start('Forms', 'forms');
?>
<p class="page-intro">Templates and documents the organization shares.</p>
<?php if ($canEdit): ?>
<section class="card">
  <h2>Add a form</h2>
  <form method="post" class="form-light">
    <?= csrf_field() ?>
    <div class="grid2">
      <div class="field"><label>Title</label><input name="title" maxlength="150" required></div>
      <div class="field"><label>Link</label><input name="link" type="url" placeholder="https://…" required></div>
    </div>
    <div class="field"><label>Description</label><textarea name="description" style="min-height:70px"></textarea></div>
    <button class="btn btn-navy" type="submit">Add form</button>
  </form>
</section>
<?php endif; ?>
<section class="card">
  <h2>Published forms <small>(<?= count($forms) ?>)</small></h2>
  <?php if (!$forms): ?><p class="empty">No forms yet.</p><?php endif; ?>
  <?php foreach ($forms as $f): ?>
    <div class="reply" style="display:flex;gap:14px;align-items:center;justify-content:space-between;flex-wrap:wrap">
      <div><b><?= e($f['title']) ?></b><small><?= e($f['description']) ?> · added by <?= e($f['full_name']) ?></small></div>
      <div class="actions">
        <a class="btn btn-line btn-sm" href="<?= e($f['link']) ?>" target="_blank" rel="noopener noreferrer">Open</a>
        <?php if ($canEdit): ?><form method="post" class="inline" onsubmit="return confirm('Remove this form?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$f['id'] ?>"><button class="btn btn-danger btn-sm">Remove</button></form><?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
</section>
<?php dash_end(); ?>
