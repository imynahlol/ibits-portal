<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

$u       = require_feature('forms');
$canEdit = is_admin() && can_manage('forms');

const FORM_FILE_MAX = 10 * 1024 * 1024;

/** extension => [label, MIME type] */
function form_file_types(): array
{
    return [
        'pdf'  => ['PDF',   'application/pdf'],
        'doc'  => ['Word',  'application/msword'],
        'docx' => ['Word',  'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'xls'  => ['Excel', 'application/vnd.ms-excel'],
        'xlsx' => ['Excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
    ];
}

function form_file_dir(): string
{
    return ROOT_DIR . '/storage/form-files';
}

/** Add attachment columns to the existing forms table when needed. */
function form_files_migrate(): void
{
    static $done = false;
    if ($done) {
        return;
    }

    $pdo    = db();
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

    if ($driver === 'sqlite') {
        $rows = $pdo->query('PRAGMA table_info(forms)')->fetchAll();
        $have = array_column($rows, 'name');
    } elseif ($driver === 'mysql') {
        $rows = $pdo->query('SHOW COLUMNS FROM forms')->fetchAll();
        $have = array_column($rows, 'Field');
    } else {
        throw new RuntimeException('Forms attachments do not support this database driver.');
    }

    $columns = [
        'file_name' => $driver === 'sqlite' ? 'TEXT'    : 'VARCHAR(255) NULL',
        'file_path' => $driver === 'sqlite' ? 'TEXT'    : 'VARCHAR(255) NULL',
        'file_type' => $driver === 'sqlite' ? 'TEXT'    : 'VARCHAR(10) NULL',
        'file_size' => $driver === 'sqlite' ? 'INTEGER' : 'INT NULL',
        'form_link' => $driver === 'sqlite' ? 'TEXT'    : 'VARCHAR(255) NULL',
    ];

    foreach ($columns as $name => $definition) {
        if (!in_array($name, $have, true)) {
            $pdo->exec("ALTER TABLE forms ADD COLUMN $name $definition");
        }
    }

    $done = true;
}

function form_ini_bytes(string $key): int
{
    $value = trim((string)ini_get($key));
    if ($value === '' || $value === '0') {
        return PHP_INT_MAX;
    }

    $number = (int)$value;
    switch (strtolower(substr($value, -1))) {
        case 'g':
            $number *= 1024;
            // no break
        case 'm':
            $number *= 1024;
            // no break
        case 'k':
            $number *= 1024;
    }

    return $number > 0 ? $number : PHP_INT_MAX;
}

function form_file_limit(): int
{
    return min(
        FORM_FILE_MAX,
        form_ini_bytes('upload_max_filesize'),
        form_ini_bytes('post_max_size')
    );
}

function form_size_label(int $bytes): string
{
    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 1) . ' MB';
    }
    if ($bytes >= 1024) {
        return max(1, (int)round($bytes / 1024)) . ' KB';
    }
    return $bytes . ' B';
}

/** Validate and store an uploaded attachment. */
function form_file_save(array $file): array
{
    $error = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);

    if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
        return ['error' => 'File too large (max ' . form_size_label(form_file_limit()) . ').'];
    }
    if ($error !== UPLOAD_ERR_OK || !is_uploaded_file((string)($file['tmp_name'] ?? ''))) {
        return ['error' => 'Upload failed. Please try again.'];
    }

    $size = (int)($file['size'] ?? 0);
    if ($size <= 0 || $size > form_file_limit()) {
        return ['error' => 'File too large (max ' . form_size_label(form_file_limit()) . ').'];
    }

    $original  = basename(str_replace('\\', '/', (string)($file['name'] ?? '')));
    $extension = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    if (!isset(form_file_types()[$extension])) {
        return ['error' => 'Only PDF, DOC, DOCX, XLS and XLSX files are allowed.'];
    }

    $head  = (string)file_get_contents((string)$file['tmp_name'], false, null, 0, 8);
    $valid = match ($extension) {
        'pdf'          => str_starts_with($head, '%PDF'),
        'docx', 'xlsx' => str_starts_with($head, "PK\x03\x04"),
        'doc', 'xls'   => str_starts_with($head, "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1"),
        default        => false,
    };
    if (!$valid) {
        return ['error' => 'Not a valid ' . form_file_types()[$extension][0] . ' file.'];
    }

    $directory = form_file_dir();
    if (!is_dir($directory) && !@mkdir($directory, 0750, true) && !is_dir($directory)) {
        return ['error' => 'The attachment storage folder could not be created.'];
    }

    $stored = bin2hex(random_bytes(16)) . '.' . $extension;
    if (!move_uploaded_file((string)$file['tmp_name'], $directory . '/' . $stored)) {
        return ['error' => 'The uploaded file could not be saved.'];
    }

    $cleanName = preg_replace('/[^\p{L}\p{N}\s._()\-]/u', '_', $original) ?: $original;

    return [
        'file_name' => mb_substr($cleanName, 0, 200),
        'file_path' => $stored,
        'file_type' => $extension,
        'file_size' => $size,
        'form_link' => null,
        'link'      => '',
    ];
}

function form_link_save(string $link): array
{
    $parts = parse_url($link);

    if (
        !filter_var($link, FILTER_VALIDATE_URL)
        || !is_array($parts)
        || !in_array(strtolower((string)($parts['scheme'] ?? '')), ['http', 'https'], true)
    ) {
        return ['error' => 'Enter a valid http or https URL.'];
    }

    if (mb_strlen($link) > 255) {
        return ['error' => 'The link must be 255 characters or fewer.'];
    }

    return [
        'file_name' => 'Link',
        'file_path' => null,
        'file_type' => 'link',
        'file_size' => 0,
        'form_link' => $link,
        'link'      => $link,
    ];
}

function form_file_delete(?string $stored): void
{
    if (!$stored || basename($stored) !== $stored) {
        return;
    }

    $path = form_file_dir() . '/' . $stored;
    if (is_file($path) && !@unlink($path)) {
        error_log('Unable to delete form attachment: ' . $path);
    }
}

// Run the migration; if it fails, show the reason instead of a blank page.
try {
    form_files_migrate();
} catch (Throwable $exception) {
    error_log('[iBITS] forms migration failed: ' . $exception->getMessage());
    http_response_code(500);
    exit('Forms could not be loaded: ' . htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8'));
}

if (isset($_GET['download'])) {
    $form = q_one('SELECT * FROM forms WHERE id = ?', [(int)$_GET['download']]);

    if (!$form) {
        http_response_code(404);
        exit('Form not found.');
    }

    $stored    = (string)($form['file_path'] ?? '');
    $extension = strtolower((string)($form['file_type'] ?? ''));
    $path      = form_file_dir() . '/' . basename($stored);

    if (
        $stored === ''
        || basename($stored) !== $stored
        || !isset(form_file_types()[$extension])
        || !is_file($path)
    ) {
        http_response_code(404);
        exit('Attachment not found.');
    }

    $downloadName = (string)($form['file_name'] ?? 'attachment.' . $extension);

    header('Content-Type: ' . form_file_types()[$extension][1]);
    header('Content-Length: ' . (string)filesize($path));
    header("Content-Disposition: attachment; filename*=UTF-8''" . rawurlencode($downloadName));
    header('X-Content-Type-Options: nosniff');

    readfile($path);
    exit;
}

if (is_post()) {
    require_manage('forms');
    csrf_check();

    $action = post('action');
    $id     = (int)post('id');

    if ($action === 'delete') {
        $old = q_one('SELECT file_path FROM forms WHERE id = ?', [$id]);

        if (!$old) {
            flash('error', 'That form could not be found.');
        } else {
            q('DELETE FROM forms WHERE id = ?', [$id]);
            form_file_delete($old['file_path'] ?? null);
            flash('success', 'Form deleted.');
        }

        redirect('admin/forms.php');
    }

    if ($action === 'save') {
        $existing       = $id > 0 ? q_one('SELECT * FROM forms WHERE id = ?', [$id]) : null;
        $title          = post('title');
        $description    = post('description');
        $attachmentType = post('attachment_type');
        $attachment     = null;
        $error          = '';

        if ($id > 0 && !$existing) {
            $error = 'That form could not be found.';
        } elseif ($title === '' || mb_strlen($title) > 150) {
            $error = 'Enter a title (maximum 150 characters).';
        } elseif (mb_strlen($description) > 5000) {
            $error = 'The description must be 5000 characters or fewer.';
        } elseif (!in_array($attachmentType, ['file', 'link'], true)) {
            $error = 'Choose a file or an online link.';
        }

        if ($error === '' && $attachmentType === 'file') {
            $uploadError = (int)($_FILES['attachment']['error'] ?? UPLOAD_ERR_NO_FILE);

            if ($uploadError !== UPLOAD_ERR_NO_FILE) {
                $attachment = form_file_save($_FILES['attachment']);
                if (isset($attachment['error'])) {
                    $error = $attachment['error'];
                }
            } elseif (!empty($existing['file_path'])) {
                $attachment = [
                    'file_name' => $existing['file_name'],
                    'file_path' => $existing['file_path'],
                    'file_type' => $existing['file_type'],
                    'file_size' => $existing['file_size'],
                    'form_link' => null,
                    'link'      => '',
                ];
            } else {
                $error = 'Choose a file to upload.';
            }
        } elseif ($error === '' && $attachmentType === 'link') {
            $attachment = form_link_save(post('form_link'));
            if (isset($attachment['error'])) {
                $error = $attachment['error'];
            }
        }

        if ($error !== '') {
            flash('error', $error);
            redirect($id > 0 ? 'admin/forms.php?edit=' . $id : 'admin/forms.php');
        }

        $newPath = !empty($attachment['file_path']) && $attachment['file_path'] !== ($existing['file_path'] ?? null)
            ? $attachment['file_path']
            : null;

        try {
            if ($id > 0) {
                q(
                    'UPDATE forms SET title=?, description=?, link=?, file_name=?, file_path=?, file_type=?, file_size=?, form_link=? WHERE id=?',
                    [
                        $title,
                        $description,
                        $attachment['link'],
                        $attachment['file_name'],
                        $attachment['file_path'],
                        $attachment['file_type'],
                        $attachment['file_size'],
                        $attachment['form_link'],
                        $id,
                    ]
                );
            } else {
                q(
                    'INSERT INTO forms (title, description, link, created_by, file_name, file_path, file_type, file_size, form_link) VALUES (?,?,?,?,?,?,?,?,?)',
                    [
                        $title,
                        $description,
                        $attachment['link'],
                        $u['id'],
                        $attachment['file_name'],
                        $attachment['file_path'],
                        $attachment['file_type'],
                        $attachment['file_size'],
                        $attachment['form_link'],
                    ]
                );
            }
        } catch (Throwable $exception) {
            form_file_delete($newPath);
            throw $exception;
        }

        if (!empty($existing['file_path']) && $existing['file_path'] !== $attachment['file_path']) {
            form_file_delete($existing['file_path']);
        }

        flash('success', $id > 0 ? 'Form updated.' : 'Form added.');
        redirect('admin/forms.php');
    }

    flash('error', 'Choose a valid form action.');
    redirect('admin/forms.php');
}

$edit = ($canEdit && isset($_GET['edit']))
    ? q_one('SELECT * FROM forms WHERE id = ?', [(int)$_GET['edit']])
    : null;

$forms = q_all('SELECT * FROM forms ORDER BY created_at DESC, id DESC');

dash_start('Forms', 'forms');
?>

<p class="page-intro">Browse published forms and documents. Sign in to download attachments.</p>

<?php if ($canEdit): ?>
    <section class="card">
        <h2><?= $edit ? 'Edit form' : 'Add a form' ?></h2>

        <form method="post" enctype="multipart/form-data" class="form-light">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">

            <div class="grid2">
                <div class="field">
                    <label for="form-title">Title</label>
                    <input id="form-title" name="title" maxlength="150"
                           value="<?= e($edit['title'] ?? '') ?>" required>
                </div>

                <div class="field">
                    <label for="attachment-type">Attachment type</label>
                    <select id="attachment-type" name="attachment_type" required>
                        <option value="file" <?= (!$edit || !empty($edit['file_path'])) ? 'selected' : '' ?>>Upload a file</option>
                        <option value="link" <?= ($edit && empty($edit['file_path'])) ? 'selected' : '' ?>>Online link</option>
                    </select>
                </div>
            </div>

            <div class="field">
                <label for="form-description">Description</label>
                <textarea id="form-description" name="description" rows="3"
                          maxlength="5000"><?= e($edit['description'] ?? '') ?></textarea>
            </div>

            <div class="field" id="form-file-field">
                <label for="form-attachment">
                    File (PDF, DOC, DOCX, XLS or XLSX; max <?= e(form_size_label(form_file_limit())) ?>)
                </label>
                <input id="form-attachment" name="attachment" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx">
                <?php if (!empty($edit['file_name']) && !empty($edit['file_path'])): ?>
                    <small>Current file: <?= e($edit['file_name']) ?>. Leave empty to keep it.</small>
                <?php endif; ?>
            </div>

            <div class="field" id="form-link-field">
                <label for="form-link">Online URL</label>
                <input id="form-link" name="form_link" type="url" maxlength="255"
                       value="<?= e($edit['form_link'] ?? $edit['link'] ?? '') ?>"
                       placeholder="https://example.com/form">
            </div>

            <div class="actions">
                <button class="btn btn-navy" type="submit"><?= $edit ? 'Save changes' : 'Add form' ?></button>
                <?php if ($edit): ?>
                    <a class="btn btn-line" href="<?= e(url('admin/forms.php')) ?>">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <script>
      (() => {
        const type      = document.getElementById('attachment-type');
        const fileField = document.getElementById('form-file-field');
        const linkField = document.getElementById('form-link-field');
        const file      = document.getElementById('form-attachment');
        const link      = document.getElementById('form-link');

        const update = () => {
          const isFile = type.value === 'file';
          fileField.hidden = !isFile;
          linkField.hidden = isFile;
          file.required = isFile && !fileField.querySelector('small');
          link.required = !isFile;
        };

        type.addEventListener('change', update);
        update();
      })();
    </script>
<?php endif; ?>

<section class="card">
    <h2>Published forms</h2>

    <?php if (!$forms): ?>
        <p class="empty">No forms have been published yet.</p>
    <?php else: ?>
        <div class="tbl-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Form</th>
                        <th>Attachment</th>
                        <th>Published</th>
                        <?php if ($canEdit): ?><th></th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($forms as $form): ?>
                        <?php
                        $filePath   = (string)($form['file_path'] ?? '');
                        $formLink   = (string)($form['form_link'] ?? '');
                        $legacyLink = (string)($form['link'] ?? '');
                        $link       = $formLink !== '' ? $formLink : ($filePath === '' ? $legacyLink : '');
                        ?>
                        <tr>
                            <td>
                                <b><?= e($form['title']) ?></b>
                                <?php if (!empty($form['description'])): ?>
                                    <small><?= nl2br(e($form['description'])) ?></small>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($filePath !== ''): ?>
                                    <a class="btn btn-line btn-sm"
                                       href="<?= e(url('admin/forms.php?download=' . (int)$form['id'])) ?>">
                                        Download <?= e(strtoupper((string)$form['file_type'])) ?>
                                    </a>
                                    <small>
                                        <?= e($form['file_name'] ?? '') ?><?= !empty($form['file_size']) ? ' &middot; ' . e(form_size_label((int)$form['file_size'])) : '' ?>
                                    </small>
                                <?php elseif ($link !== ''): ?>
                                    <a class="btn btn-line btn-sm" href="<?= e($link) ?>" target="_blank" rel="noopener noreferrer">Open link</a>
                                <?php else: ?>
                                    <span class="empty">No attachment</span>
                                <?php endif; ?>
                            </td>

                            <td><?= e(fmt_date((string)$form['created_at'])) ?></td>

                            <?php if ($canEdit): ?>
                                <td>
                                    <div class="actions">
                                        <a class="btn btn-line btn-sm"
                                           href="<?= e(url('admin/forms.php?edit=' . (int)$form['id'])) ?>">Edit</a>

                                        <form method="post" class="inline" onsubmit="return confirm('Delete this form?')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= (int)$form['id'] ?>">
                                            <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php dash_end(); ?>