<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
$u = require_feature('forms');
$canEdit = can_manage('forms');

if (is_post()) {
    require_manage('forms');
    csrf_check();
    $act = post('action');
    $id = (int)post('id');
    if ($act === 'delete') {
        q('DELETE FROM forms WHERE id = ?', [$id]);
        flash('success', 'Form removed.');
    } else {
        $title = trim(post('title')); $link = trim(post('link')); $description = trim(post('description'));
        if ($title === '' || mb_strlen($title) > 150) flash('error', 'Enter a form title (maximum 150 characters).');
        elseif (!preg_match('#^https?://#i', $link) || !filter_var($link, FILTER_VALIDATE_URL)) flash('error', 'Enter a valid link starting with http:// or https://.');
        elseif ($act === 'update' && !q_val('SELECT 1 FROM forms WHERE id = ?', [$id])) flash('error', 'That form could not be found.');
        else {
            if ($act === 'update') {
                q('UPDATE forms SET title = ?, description = ?, link = ? WHERE id = ?', [$title, $description, $link, $id]);
                flash('success', 'Form updated.');
            } else {
                q('INSERT INTO forms (title,description,link,created_by) VALUES (?,?,?,?)', [$title, $description, $link, $u['id']]);
                flash('success', 'Form added.');
            }
        }
    }
    redirect('admin/forms.php');
}
$edit = isset($_GET['edit']) ? q_one('SELECT * FROM forms WHERE id = ?', [(int)$_GET['edit']]) : null;
if ($edit && !$canEdit) { $edit = null; }
$forms = q_all('SELECT f.*, u.full_name FROM forms f JOIN users u ON u.id = f.created_by ORDER BY f.id DESC');

dash_start('Forms', 'forms');
?>
<p class="page-intro">Templates, excuse-letter forms, and documents the organization shares.</p>
<?php if ($canEdit): ?>
<section class="card">
  <h2><?= $edit ? 'Edit form' : 'Add a form' ?></h2>
  <form method="post" class="form-light">
    <?= csrf_field() ?><input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
    <div class="grid2">
      <div class="field"><label>Title</label><input name="title" maxlength="150" value="<?= e($edit['title'] ?? '') ?>" required></div>
      <div class="field"><label>Link</label><input name="link" type="url" placeholder="https://…" value="<?= e($edit['link'] ?? '') ?>" required></div>
    </div>
    <div class="field"><label>Description</label><textarea name="description" style="min-height:70px"><?= e($edit['description'] ?? '') ?></textarea></div>
    <div class="actions"><button class="btn btn-navy" type="submit"><?= $edit ? 'Save changes' : 'Add form' ?></button><?php if ($edit): ?><a class="btn btn-line" href="<?= e(url('admin/forms.php')) ?>">Cancel</a><?php endif; ?></div>
  </form>
</section>
<?php else: ?><div class="alert alert-info">Read-only access for your position.</div><?php endif; ?>
<section class="card">
  <h2>Published forms <small>(<?= count($forms) ?>)</small></h2>
  <?php if (!$forms): ?><p class="empty">No forms yet.</p><?php endif; ?>
  <?php foreach ($forms as $f): ?>
    <div class="reply" style="display:flex;gap:14px;align-items:center;justify-content:space-between;flex-wrap:wrap">
      <div><b><?= e($f['title']) ?></b><small><?= e($f['description']) ?> · added by <?= e($f['full_name']) ?></small></div>
      <div class="actions"><a class="btn btn-line btn-sm" href="<?= e($f['link']) ?>" target="_blank" rel="noopener noreferrer">Open</a>
        <?php if ($canEdit): ?><a class="btn btn-line btn-sm" href="<?= e(url('admin/forms.php?edit=' . (int)$f['id'])) ?>">Edit</a><form method="post" class="inline" onsubmit="return confirm('Remove this form?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$f['id'] ?>"><button class="btn btn-danger btn-sm">Delete</button></form><?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
</section>
<?php dash_end(); ?>
