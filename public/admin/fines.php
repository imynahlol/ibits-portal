<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
$u = require_feature('fines');

$canEdit = is_admin() && can_manage('fines');
if (isset($_GET['member_search'])) {
    if (!$canEdit) {
        http_response_code(403);
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    $search = trim((string)$_GET['member_search']);
    $members = mb_strlen($search) >= 2
        ? q_all("SELECT id, full_name, id_number FROM users
                WHERE account_type = 'member' AND status = 'active' AND full_name LIKE ?
                ORDER BY full_name LIMIT 20", ['%' . $search . '%'])
        : [];
    echo json_encode($members, JSON_THROW_ON_ERROR);
    exit;
}
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
        $memberId = (int)post('member_id');
        $member = q_one("SELECT id, full_name FROM users WHERE id = ? AND account_type = 'member' AND status = 'active'", [$memberId]);
        $userId = (int)($member['id'] ?? 0);
        $amount = filter_var(post('amount'), FILTER_VALIDATE_FLOAT);
        $due = post('due_date'); $reason = trim(post('reason'));
        if (!$member) flash('error', 'Select an active student from the name search before issuing a fine.');
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
$edit = ($canEdit && isset($_GET['edit'])) ? q_one('SELECT f.*, m.id AS member_id, m.id_number AS member_id_number, m.full_name AS member_name FROM fines f JOIN users m ON m.id = f.user_id WHERE f.id = ?', [(int)$_GET['edit']]) : null;
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
      <div class="field member-picker">
        <label for="member_name">Student name</label>
        <input id="member_name" type="search" placeholder="Start typing a student's name" value="<?= e($edit['member_name'] ?? '') ?>" autocomplete="off" aria-autocomplete="list" aria-controls="member_search_results" required>
        <input id="member_id" name="member_id" type="hidden" value="<?= (int)($edit['member_id'] ?? 0) ?>">
        <div id="member_search_results" class="member-search-results" role="listbox" aria-label="Matching students" hidden></div>
        <small id="member_selection"><?= !empty($edit['member_name']) ? 'Selected: ' . e($edit['member_name']) . ' (' . e($edit['member_id_number']) . ')' : 'Choose a student from the matching results. Student ID numbers are shown to distinguish students with the same name.' ?></small>
      </div>
      <div class="field"><label>Amount (₱)</label><input name="amount" type="number" min="1" step="0.01" value="<?= e((string)($edit['amount'] ?? '')) ?>" required></div>
      <div class="field"><label>Reason</label><input name="reason" maxlength="200" value="<?= e($edit['reason'] ?? '') ?>" required></div>
      <div class="field"><label>Due date</label><input name="due_date" type="date" value="<?= e($edit['due_date'] ?? '') ?>" required></div>
    </div>
    <div class="actions"><button class="btn btn-navy" type="submit"><?= $edit ? 'Save changes' : 'Issue fine' ?></button><?php if ($edit): ?><a class="btn btn-line" href="<?= e(url('admin/fines.php')) ?>">Cancel</a><?php endif; ?></div>
  </form>
</section>
<?php endif; ?>

<?php if ($canEdit): ?>
<style>
.member-search-results{position:relative;z-index:2;max-height:240px;overflow-y:auto;border:1px solid var(--line,#d7dce5);border-radius:10px;background:var(--card,#fff);box-shadow:0 8px 20px rgba(0,0,0,.12)}
.member-search-results[hidden]{display:none}
.member-search-results button{display:block;width:100%;padding:10px 12px;border:0;border-bottom:1px solid var(--line,#d7dce5);background:transparent;text-align:left;color:inherit;cursor:pointer}
.member-search-results button:last-child{border-bottom:0}
.member-search-results button:hover,.member-search-results button:focus{background:rgba(6,33,92,.08)}
.member-search-results button small{display:block}
</style>
<script>
(function () {
  var input = document.getElementById('member_name');
  var memberId = document.getElementById('member_id');
  var results = document.getElementById('member_search_results');
  var selection = document.getElementById('member_selection');
  var form = input.form;
  var searchUrl = <?= json_encode(url('admin/fines.php'), JSON_THROW_ON_ERROR) ?>;
  var timer;
  var requestNumber = 0;

  function clearResults() {
    results.replaceChildren();
    results.hidden = true;
  }

  function renderResults(members) {
    clearResults();
    if (!members.length) {
      var empty = document.createElement('small');
      empty.textContent = 'No active students found with that name.';
      empty.style.display = 'block';
      empty.style.padding = '10px 12px';
      results.appendChild(empty);
      results.hidden = false;
      return;
    }
    members.forEach(function (member) {
      var option = document.createElement('button');
      option.type = 'button';
      option.setAttribute('role', 'option');
      var name = document.createElement('span');
      name.textContent = member.full_name;
      var idNumber = document.createElement('small');
      idNumber.textContent = member.id_number;
      option.appendChild(name);
      option.appendChild(idNumber);
      option.addEventListener('click', function () {
        input.value = member.full_name;
        memberId.value = member.id;
        selection.textContent = 'Selected: ' + member.full_name + ' (' + member.id_number + ')';
        input.setCustomValidity('');
        clearResults();
      });
      results.appendChild(option);
    });
    results.hidden = false;
  }

  input.addEventListener('input', function () {
    memberId.value = '';
    selection.textContent = 'Choose a student from the matching results.';
    input.setCustomValidity('');
    clearTimeout(timer);
    clearResults();
    var query = input.value.trim();
    var currentRequest = ++requestNumber;
    if (query.length < 2) return;
    timer = setTimeout(function () {
      fetch(searchUrl + '?member_search=' + encodeURIComponent(query), {
        headers: { 'Accept': 'application/json' }
      }).then(function (response) {
        if (!response.ok) throw new Error('Student search failed.');
        return response.json();
      }).then(function (members) {
        if (currentRequest === requestNumber) renderResults(members);
      }).catch(function () {
        if (currentRequest === requestNumber) {
          clearResults();
          selection.textContent = 'Could not search students. Please try again.';
        }
      });
    }, 200);
  });

  form.addEventListener('submit', function (event) {
    if (!memberId.value) {
      event.preventDefault();
      input.setCustomValidity('Select a student from the matching results.');
      input.reportValidity();
    }
  });

  document.addEventListener('click', function (event) {
    if (!event.target.closest('.member-picker')) clearResults();
  });
})();
</script>
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
