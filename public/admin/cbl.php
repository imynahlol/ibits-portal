<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
$u = require_feature('cbl');
const PROPOSERS = ['President', 'Vice President'];
$canPropose = can_manage('cbl') && in_array($u['position'], PROPOSERS, true);

if (is_post()) {
    require_manage('cbl');
    csrf_check();
    if (post('action') === 'propose') {
        if (!$canPropose) { http_response_code(403); exit('Your position cannot propose amendments.'); }
        if (post('amendment_no') === '' || post('title') === '' || mb_strlen(post('description')) < 10) flash('error', 'Fill in the number, title and a description.');
        else {
            q('INSERT INTO cbl_documents (amendment_no,title,description,status,created_by) VALUES (?,?,?,?,?)',
              [post('amendment_no'), post('title'), post('description'), 'pending_president', $u['id']]);
            flash('success', 'Amendment submitted for President and Vice President review.');
        }
    } elseif (post('action') === 'update') {
        $doc = q_one('SELECT * FROM cbl_documents WHERE id = ?', [(int)post('id')]);
        if (!$canPropose || !$doc || !in_array($doc['status'], ['pending_president', 'rejected'], true)) {
            http_response_code(403); exit('This amendment cannot be edited at its current workflow stage.');
        }
        $no = trim(post('amendment_no')); $title = trim(post('title')); $description = trim(post('description'));
        if ($no === '' || $title === '' || mb_strlen($description) < 10) flash('error', 'Fill in the number, title and a description of at least 10 characters.');
        else {
            if ($doc['status'] === 'rejected') q('DELETE FROM cbl_approvals WHERE cbl_id = ?', [$doc['id']]);
            q('UPDATE cbl_documents SET amendment_no=?, title=?, description=?, status=\'pending_president\' WHERE id=?', [$no, $title, $description, $doc['id']]);
            flash('success', 'Amendment updated and returned to the President review step.');
        }
    } elseif (post('action') === 'delete') {
        $doc = q_one('SELECT * FROM cbl_documents WHERE id = ?', [(int)post('id')]);
        if (!$canPropose || !$doc || !in_array($doc['status'], ['pending_president', 'rejected'], true)) {
            http_response_code(403); exit('Only amendments awaiting initial review or rejected amendments can be deleted.');
        }
        q('DELETE FROM cbl_documents WHERE id = ?', [$doc['id']]);
        flash('success', 'Amendment deleted.');
    } elseif (in_array(post('action'), ['approve', 'reject'], true)) {
        $doc = q_one('SELECT * FROM cbl_documents WHERE id = ?', [(int)post('id')]);
        // Only the position that owns the current step may decide.
        if (!$doc || (CBL_CHAIN[$doc['status']] ?? null) !== $u['position']) {
            flash('error', 'It is not your position\'s turn to decide on this amendment.');
        } else {
            $approve = post('action') === 'approve';
            q('UPDATE cbl_documents SET status = ? WHERE id = ?', [$approve ? CBL_NEXT[$doc['status']] : 'rejected', $doc['id']]);
            q('INSERT INTO cbl_approvals (cbl_id,position,decided_by,decision,remarks) VALUES (?,?,?,?,?)',
              [$doc['id'], $u['position'], $u['id'], $approve ? 'approved' : 'rejected', post('remarks')]);
            flash('success', $approve ? 'Approved.' : 'Amendment rejected.');
        }
    }
    redirect('admin/cbl.php');
}
$docs = q_all('SELECT d.*, u.full_name FROM cbl_documents d JOIN users u ON u.id = d.created_by ORDER BY d.id DESC');
$editDoc = null;
if ($canPropose && isset($_GET['edit'])) {
    $editDoc = q_one('SELECT * FROM cbl_documents WHERE id = ?', [(int)$_GET['edit']]);
    if (!$editDoc || !in_array($editDoc['status'], ['pending_president', 'rejected'], true)) $editDoc = null;
}
$steps = ['pending_president' => 'President', 'pending_vp' => 'Vice President'];
$order = array_keys($steps);

dash_start('CBL Workflow', 'cbl');
?>
<p class="page-intro">Amendments to the Constitution and By-Laws go through the President, then the Vice President.</p>

<?php if ($canPropose): ?>
<section class="card">
  <h2><?= $editDoc ? 'Edit amendment' : 'Propose an amendment' ?></h2>
  <form method="post" class="form-light">
    <?= csrf_field() ?><input type="hidden" name="action" value="<?= $editDoc ? 'update' : 'propose' ?>"><?php if ($editDoc): ?><input type="hidden" name="id" value="<?= (int)$editDoc['id'] ?>"><?php endif; ?>
    <div class="grid2">
      <div class="field"><label>Amendment no.</label><input name="amendment_no" maxlength="20" placeholder="2026-05" value="<?= e($editDoc['amendment_no'] ?? '') ?>" required></div>
      <div class="field"><label>Title</label><input name="title" maxlength="150" value="<?= e($editDoc['title'] ?? '') ?>" required></div>
    </div>
    <div class="field"><label>What changes?</label><textarea name="description" required><?= e($editDoc['description'] ?? '') ?></textarea></div>
    <div class="actions"><button class="btn btn-navy" type="submit"><?= $editDoc ? 'Save changes & resubmit' : 'Submit for approval' ?></button><?php if ($editDoc): ?><a class="btn btn-line" href="<?= e(url('admin/cbl.php')) ?>">Cancel</a><?php endif; ?></div>
  </form>
</section>
<?php endif; ?>

<section class="card">
  <h2>Amendments <small>(<?= count($docs) ?>)</small></h2>
  <?php if (!$docs): ?><p class="empty">No amendments yet.</p><?php endif; ?>
  <?php foreach ($docs as $d):
      $turn = (CBL_CHAIN[$d['status']] ?? null) === $u['position'];
      $idx = array_search($d['status'], $order, true); ?>
    <div class="ticket" style="padding:18px">
      <div class="actions" style="justify-content:space-between">
        <b style="color:var(--navy)">#<?= e($d['amendment_no']) ?>: <?= e($d['title']) ?></b>
        <?php if ($turn): ?><span class="badge badge-pending">Needs your approval</span>
        <?php else: ?><span class="badge badge-<?= $d['status'] === 'published' ? 'published' : ($d['status'] === 'rejected' ? 'rejected' : 'pending') ?>"><?= $d['status'] === 'published' ? 'Published' : ($d['status'] === 'rejected' ? 'Rejected' : 'Pending ' . e($steps[$d['status']])) ?></span><?php endif; ?>
      </div>
      <p style="margin:8px 0;font-size:14px;color:#475569"><?= e($d['description']) ?></p>
      <small style="color:var(--muted)">Proposed by <?= e($d['full_name']) ?> · <?= e(fmt_date($d['created_at'])) ?></small>
      <?php if ($canPropose && in_array($d['status'], ['pending_president', 'rejected'], true)): ?>
        <div class="actions" style="margin-top:10px"><a class="btn btn-line btn-sm" href="<?= e(url('admin/cbl.php?edit=' . (int)$d['id'])) ?>">Edit</a><form method="post" class="inline" onsubmit="return confirm('Delete this amendment and its approval history?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$d['id'] ?>"><button class="btn btn-danger btn-sm" type="submit">Delete</button></form></div>
      <?php endif; ?>
      <div class="steps">
        <?php foreach ($order as $i => $s):
            $cls = $d['status'] === 'published' ? 'done' : ($d['status'] === 'rejected' ? '' : ($i < $idx ? 'done' : ($i === $idx ? 'now' : ''))); ?>
          <span class="<?= $cls ?>"><?= e($steps[$s]) ?></span>
        <?php endforeach; ?>
      </div>
      <?php foreach (q_all('SELECT a.*, u.full_name FROM cbl_approvals a JOIN users u ON u.id = a.decided_by WHERE cbl_id = ? ORDER BY a.id', [$d['id']]) as $a): ?>
        <div class="reply"><small><?= e($a['position']) ?> (<?= e($a['full_name']) ?>) <?= e($a['decision']) ?></small><?= e($a['remarks']) ?></div>
      <?php endforeach; ?>
      <?php if ($turn): ?>
        <form method="post" class="form-light" style="margin-top:12px">
          <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$d['id'] ?>">
          <div class="field"><input name="remarks" maxlength="300" placeholder="Remarks (optional)"></div>
          <div class="actions">
            <button class="btn btn-navy btn-sm" name="action" value="approve">Approve</button>
            <button class="btn btn-danger btn-sm" name="action" value="reject" onclick="return confirm('Reject this amendment?')">Reject</button>
          </div>
        </form>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</section>
<?php dash_end(); ?>
