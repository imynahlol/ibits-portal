<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

$u = require_feature('helpdesk');

$concern = ['subject' => '', 'category' => 'General', 'description' => ''];

if (is_post()) {
    csrf_check();
    $action = post('action');

    if ($action === 'create_concern') {
        foreach ($concern as $key => $_) {
            $concern[$key] = post($key);
        }

        $errors = [];

        if (mb_strlen($concern['subject']) < 3 || mb_strlen($concern['subject']) > 150) {
            $errors[] = 'Add a short subject (3 to 150 characters).';
        }
        if (!in_array($concern['category'], TICKET_CATEGORIES, true)) {
            $errors[] = 'Choose a category.';
        }
        if (mb_strlen($concern['description']) < 10 || mb_strlen($concern['description']) > 4000) {
            $errors[] = 'Describe your concern in 10 to 4000 characters.';
        }

        if ($errors) {
            foreach ($errors as $error) {
                flash('error', $error);
            }
        } else {
            q(
                'INSERT INTO tickets (user_id,subject,category,description) VALUES (?,?,?,?)',
                [$u['id'], $concern['subject'], $concern['category'], $concern['description']]
            );
            flash('success', 'Your concern was submitted to the Help Desk.');
            redirect('admin/helpdesk.php');
        }
    } else {
        // Only positions authorized to manage Help Desk can reply or change ticket status.
        require_manage('helpdesk');

        $id = (int)post('id');
        if (!q_val('SELECT 1 FROM tickets WHERE id = ?', [$id])) {
            redirect('admin/helpdesk.php');
        }

        if ($action === 'status' && isset(TICKET_STATUS[post('status')])) {
            q(
                'UPDATE tickets SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?',
                [post('status'), $id]
            );
            flash('success', 'Status updated.');
        } elseif ($action === 'reply') {
            $msg = post('message');

            if (mb_strlen($msg) < 2 || mb_strlen($msg) > 4000) {
                flash('error', 'Write a reply first.');
            } else {
                q(
                    'INSERT INTO ticket_replies (ticket_id,user_id,message) VALUES (?,?,?)',
                    [$id, $u['id'], $msg]
                );
                q(
                    "UPDATE tickets SET status = CASE WHEN status = 'open' THEN 'in_progress' ELSE status END, updated_at = CURRENT_TIMESTAMP WHERE id = ?",
                    [$id]
                );
                flash('success', 'Reply sent.');
            }
        }

        redirect(
            'admin/helpdesk.php'
            . (isset($_GET['status']) ? '?status=' . urlencode((string)$_GET['status']) : '')
            . '#t' . $id
        );
    }
}

$filter            = (string)($_GET['status'] ?? '');
$tickets           = tickets_all($filter);
$myTickets         = tickets_for_user((int)$u['id']);
$canManageHelpdesk = can_manage('helpdesk');

dash_start('Help Desk', 'helpdesk');
?>

<p class="page-intro">
    Submit your own concern to the Help Desk, and authorized Help Desk officers can review and reply to concerns.
</p>

<section class="card">
    <h2>Submit a concern</h2>

    <form method="post" class="form-light">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="create_concern">

        <div class="grid2">
            <div class="field">
                <label for="subject">Subject</label>
                <input id="subject" name="subject" maxlength="150"
                       value="<?= e($concern['subject']) ?>" required>
            </div>

            <div class="field">
                <label for="category">Category</label>
                <select id="category" name="category">
                    <?php foreach (TICKET_CATEGORIES as $c): ?>
                        <option value="<?= e($c) ?>" <?= $concern['category'] === $c ? 'selected' : '' ?>>
                            <?= e($c) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="field">
            <label for="description">Describe your concern</label>
            <textarea id="description" name="description" maxlength="4000" required><?= e($concern['description']) ?></textarea>
        </div>

        <button class="btn btn-navy" type="submit">Submit concern</button>
    </form>
</section>

<section class="card">
    <h2>My submitted concerns <small>(<?= count($myTickets) ?>)</small></h2>

    <?php if (!$myTickets): ?>
        <p class="empty">You have not submitted any concerns yet.</p>
    <?php else: ?>
        <?php foreach ($myTickets as $mine): ?>
            <details class="ticket">
                <summary>
                    <b><?= e($mine['subject']) ?></b>
                    <small><?= e($mine['category']) ?> · <?= e(fmt_date($mine['created_at'])) ?></small>
                    <?= status_badge($mine['status']) ?>
                </summary>

                <div class="ticket-body">
                    <p><?= e($mine['description']) ?></p>

                    <?php foreach ($mine['replies'] as $reply): ?>
                        <div class="reply staff">
                            <small>
                                <?= e($reply['full_name']) ?>
                                · <?= e($reply['position'] ?? 'Officer') ?>
                                · <?= e(fmt_date($reply['created_at'], 'M j, g:i A')) ?>
                            </small>
                            <?= e($reply['message']) ?>
                        </div>
                    <?php endforeach; ?>

                    <?php if (!$mine['replies']): ?>
                        <small style="color:var(--muted)">No reply yet.</small>
                    <?php endif; ?>
                </div>
            </details>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php if (!$canManageHelpdesk): ?>
    <div class="alert alert-info">
        You can submit and follow your own concerns here. Only officers assigned to manage Help Desk can reply to tickets or change their status.
    </div>
<?php else: ?>
    <div class="filters">
        <a href="<?= e(url('admin/helpdesk.php')) ?>" class="<?= $filter === '' ? 'on' : '' ?>">All</a>
        <?php foreach (TICKET_STATUS as $k => $label): ?>
            <a href="<?= e(url('admin/helpdesk.php?status=' . $k)) ?>" class="<?= $filter === $k ? 'on' : '' ?>">
                <?= e($label) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (!$tickets): ?>
        <p class="empty">Nothing here.</p>
    <?php endif; ?>

    <?php foreach ($tickets as $t): ?>
        <details class="ticket" id="t<?= (int)$t['id'] ?>" <?= ($_GET['open'] ?? '') == $t['id'] ? 'open' : '' ?>>
            <summary>
                <b><?= e($t['subject']) ?></b>
                <small>
                    <?= e($t['full_name']) ?>
                    · <?= e($t['category']) ?>
                    · <?= e(fmt_date($t['created_at'])) ?>
                </small>
                <?= status_badge($t['status']) ?>
            </summary>

            <div class="ticket-body">
                <p><?= e($t['description']) ?></p>

                <?php foreach ($t['replies'] as $r): ?>
                    <div class="reply staff">
                        <small>
                            <?= e($r['full_name']) ?>
                            · <?= e($r['position'] ?? 'Officer') ?>
                            · <?= e(fmt_date($r['created_at'], 'M j, g:i A')) ?>
                        </small>
                        <?= e($r['message']) ?>
                    </div>
                <?php endforeach; ?>

                <form method="post" class="form-light" style="margin-top:14px">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                    <input type="hidden" name="action" value="reply">

                    <div class="field">
                        <textarea name="message" style="min-height:70px" placeholder="Write a reply…" required></textarea>
                    </div>

                    <button class="btn btn-navy btn-sm">Send reply</button>
                </form>

                <form method="post" class="form-light actions" style="margin-top:10px">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                    <input type="hidden" name="action" value="status">

                    <div class="field" style="margin:0">
                        <select name="status">
                            <?php foreach (TICKET_STATUS as $k => $label): ?>
                                <option value="<?= e($k) ?>" <?= $t['status'] === $k ? 'selected' : '' ?>>
                                    <?= e($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button class="btn btn-line btn-sm">Update status</button>
                </form>
            </div>
        </details>
    <?php endforeach; ?>
<?php endif; ?>

<?php dash_end(); ?>