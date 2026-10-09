<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

$u       = require_feature('events');
$canEdit = can_manage('events');

/* ---------- cover photo helpers ---------- */
function ev_store_image(array $f, ?string &$error): ?string
{
    $code = $f['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($code === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($code !== UPLOAD_ERR_OK) {
        $error = 'The image could not be uploaded (it may be larger than the server allows).';
        return null;
    }
    if (($f['size'] ?? 0) > 2 * 1024 * 1024) {
        $error = 'Image must be 2 MB or smaller.';
        return null;
    }

    $info = @getimagesize($f['tmp_name']);
    $ext  = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG  => 'png',
        IMAGETYPE_GIF  => 'gif',
        IMAGETYPE_WEBP => 'webp',
    ];
    if (!$info || !isset($ext[$info[2]])) {
        $error = 'Use a JPG, PNG, GIF or WebP image.';
        return null;
    }

    $dir = dirname(__DIR__) . '/uploads/events';
    if (!is_dir($dir) && !@mkdir($dir, 0755, true)) {
        $error = 'The uploads folder could not be created.';
        return null;
    }

    $name = 'ev_' . bin2hex(random_bytes(8)) . '.' . $ext[$info[2]];
    if (!@move_uploaded_file($f['tmp_name'], $dir . '/' . $name)) {
        $error = 'The image could not be saved.';
        return null;
    }

    return 'uploads/events/' . $name;
}

function ev_delete_image(?string $rel): void
{
    if ($rel && strpos($rel, 'uploads/events/') === 0) {
        $p = dirname(__DIR__) . '/' . $rel;
        if (is_file($p)) {
            @unlink($p);
        }
    }
}

// The events table needs an `image` column for cover photos. It is added automatically the first time a manager opens this page.
// If your database user cannot ALTER tables, run:  ALTER TABLE events ADD COLUMN image VARCHAR(255) NULL;
$hasImage = false;
try {
    if (q_val("SHOW COLUMNS FROM events LIKE 'image'", [])) {
        $hasImage = true;
    } elseif ($canEdit) {
        q('ALTER TABLE events ADD COLUMN image VARCHAR(255) NULL', []);
        $hasImage = true;
    }
} catch (Throwable $ex) {
    $hasImage = false;
}

$reopen = null; // posted values to put back in the form when validation fails
if (is_post()) {
    require_manage('events');
    csrf_check();
    $act = post('action');

    if ($act === 'delete') {
        $did    = (int)post('id');
        $oldImg = $hasImage ? (string)q_val('SELECT image FROM events WHERE id = ?', [$did]) : '';

        q('DELETE FROM events WHERE id = ?', [$did]);
        ev_delete_image($oldImg);

        flash('success', 'Event deleted.');
        redirect('admin/events.php');
    }

    if ($act === 'save') {
        $id    = (int)post('id');
        $start = str_replace('T', ' ', post('start'));
        $end   = str_replace('T', ' ', post('end'));
        $title = post('title');
        $err   = [];

        if ($title === '' || mb_strlen($title) > 150) {
            $err[] = 'Enter an event title.';
        }
        if (!strtotime($start)) {
            $err[] = 'Enter a valid start date and time.';
        }
        if ($end !== '' && (!strtotime($end) || strtotime($end) < strtotime($start))) {
            $err[] = 'End must be after the start.';
        }

        $newImg = null;
        if (!$err && $hasImage && isset($_FILES['image'])) {
            $ie     = null;
            $newImg = ev_store_image($_FILES['image'], $ie);
            if ($ie) {
                $err[] = $ie;
            }
        }

        if ($err) {
            foreach ($err as $x) {
                flash('error', $x);
            }
            $reopen = [
                'id'    => $id,
                'title' => $title,
                'cat'   => post('category'),
                'start' => substr($start, 0, 10),
                'st'    => substr($start, 11, 5),
                'end'   => substr($end, 0, 10),
                'et'    => substr($end, 11, 5),
                'loc'   => post('location'),
                'desc'  => post('description'),
                'pub'   => post('is_public') === '1' ? 1 : 0,
            ];
        } else {
            $row = [
                $title,
                post('description'),
                post('category'),
                date('Y-m-d H:i:s', strtotime($start)),
                $end ? date('Y-m-d H:i:s', strtotime($end)) : null,
                post('location'),
                post('is_public') === '1' ? 1 : 0,
            ];

            if ($hasImage) {
                $old = $id ? (string)q_val('SELECT image FROM events WHERE id = ?', [$id]) : '';
                $img = $newImg ?? (post('remove_image') === '1' ? null : ($old !== '' ? $old : null));

                if ($id) {
                    q(
                        'UPDATE events SET title=?,description=?,category=?,start_datetime=?,end_datetime=?,location=?,is_public=?,image=? WHERE id=?',
                        [...$row, $img, $id]
                    );
                } else {
                    q(
                        'INSERT INTO events (title,description,category,start_datetime,end_datetime,location,is_public,created_by,image) VALUES (?,?,?,?,?,?,?,?,?)',
                        [...$row, $u['id'], $img]
                    );
                }

                if ($old !== '' && $old !== $img) {
                    ev_delete_image($old);
                }
            } else {
                if ($id) {
                    q(
                        'UPDATE events SET title=?,description=?,category=?,start_datetime=?,end_datetime=?,location=?,is_public=? WHERE id=?',
                        [...$row, $id]
                    );
                } else {
                    q(
                        'INSERT INTO events (title,description,category,start_datetime,end_datetime,location,is_public,created_by) VALUES (?,?,?,?,?,?,?,?)',
                        [...$row, $u['id']]
                    );
                }
            }

            flash('success', $id ? 'Event updated.' : 'Event added.');
            redirect('admin/events.php');
        }
    }
}

$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$events = q_all('SELECT * FROM events ORDER BY start_datetime DESC');

// Events -> the shape the calendar script uses.
$cats     = ['Competition', 'Seminar', 'Workshop', 'Social', 'Meeting', 'Other'];
$jsEvents = [];

foreach ($events as $ev) {
    $sd  = (string)$ev['start_datetime'];
    $ed  = !empty($ev['end_datetime']) ? (string)$ev['end_datetime'] : '';
    $st  = substr($sd, 11, 5);
    $et  = $ed !== '' ? substr($ed, 11, 5) : '';
    $cat = trim((string)($ev['category'] ?? ''));
    if ($cat === '') {
        $cat = 'Other';
    }
    if (!in_array($cat, $cats, true)) {
        $cats[] = $cat;
    }

    $img = trim((string)($ev['image'] ?? ''));

    $jsEvents[] = [
        'id'    => (int)$ev['id'],
        'title' => (string)$ev['title'],
        'start' => substr($sd, 0, 10),
        'st'    => $st === '00:00' ? '' : $st,
        'end'   => $ed !== '' ? substr($ed, 0, 10) : substr($sd, 0, 10),
        'et'    => $et === '00:00' ? '' : $et,
        'loc'   => (string)($ev['location'] ?? ''),
        'cat'   => $cat,
        'desc'  => (string)($ev['description'] ?? ''),
        'pub'   => (int)$ev['is_public'],
        'img'   => $img !== '' ? url($img) : '',
    ];
}

if ($reopen && $reopen['cat'] !== '' && !in_array($reopen['cat'], $cats, true)) {
    $cats[] = $reopen['cat'];
}

$J = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;

dash_start('Events', 'events');
?>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
  @keyframes evfade{from{opacity:0}to{opacity:1}}
  @keyframes evpop{from{opacity:0;transform:translateY(16px) scale(.96)}to{opacity:1;transform:none}}
  @keyframes evsw{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}

  .evo{position:fixed;inset:0;z-index:70;display:none;align-items:center;justify-content:center;padding:16px;background:rgba(2,10,28,.64);backdrop-filter:blur(5px);-webkit-backdrop-filter:blur(5px);font-family:Poppins,sans-serif}
  .evo.open{display:flex;animation:evfade .25s ease-out}

  .evx{position:relative;width:min(420px,100%);height:min(560px,100%);perspective:1400px;animation:evpop .4s cubic-bezier(.2,.7,.2,1)}
  .evi{position:relative;width:100%;height:100%;transform-style:preserve-3d;transition:transform .75s cubic-bezier(.2,.7,.2,1)}
  .evx.flipped .evi{transform:rotateY(180deg)}

  .evf,.evb{position:absolute;inset:0;border-radius:1.5rem;overflow:hidden;backface-visibility:hidden;-webkit-backface-visibility:hidden;box-shadow:0 30px 70px -20px rgba(0,0,0,.7),0 4px 0 rgba(250,213,23,.55)}
  .evf{cursor:pointer;background:#06215c}
  .evf:hover .evh{transform:translateX(4px)}
  .evb{transform:rotateY(180deg);background:#fff;color:#06215c;display:flex;flex-direction:column}
  .evx .evb{pointer-events:none}
  .evx.flipped .evb{pointer-events:auto}
  .evx.flipped .evf{pointer-events:none}

  .eve{position:absolute;inset:0;border-radius:1.5rem;overflow:hidden;backface-visibility:hidden;-webkit-backface-visibility:hidden;background:#fff;color:#06215c;display:flex;flex-direction:column;visibility:hidden;pointer-events:none;box-shadow:0 30px 70px -20px rgba(0,0,0,.7),0 4px 0 rgba(250,213,23,.55)}
  .evx.edit .evi{transform:rotateY(360deg)}
  .evx.ed .evf{visibility:hidden}
  .evx.ed .eve{visibility:visible}
  .evx.edit .evclose{left:auto;right:14px;background:rgba(6,33,92,.08);color:#06215c}
  .evx.edit .evclose:hover{background:rgba(6,33,92,.16)}
  .evx.edit .eve{pointer-events:auto}
  .evx.edit .evb{pointer-events:none}

  .eve .evs{width:auto;flex:1;min-height:0;max-height:none;border-radius:0;box-shadow:none;animation:none;padding:22px 22px 0}
  .eve .fa{position:sticky;bottom:0;background:#fff;padding:12px 0 16px;border-top:1px solid #eef1f7}

  .ev-art{position:relative;overflow:hidden;background:linear-gradient(135deg,#06215c,#0a328c);background-size:cover;background-position:center}
  .ev-art::before{content:"";position:absolute;inset:0;opacity:.16;background:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='260' height='120'%3E%3Cg font-family='Menlo,Consolas,monospace' font-size='9' fill='%23ffffff' letter-spacing='3.2'%3E%3Ctext x='0' y='14'%3E00000000000000000000000000%3C/text%3E%3Ctext x='0' y='31'%3E11111111111111111111111111%3C/text%3E%3Ctext x='0' y='48'%3E11111111111111111111111111%3C/text%3E%3Ctext x='0' y='65'%3E00000000000000000000000000%3C/text%3E%3Ctext x='0' y='82'%3E11111111111111111111111111%3C/text%3E%3Ctext x='0' y='99'%3E11111111111111111111111111%3C/text%3E%3Ctext x='0' y='116'%3E00000000000000000000000000%3C/text%3E%3C/g%3E%3C/svg%3E") 0 6px/260px 120px repeat}
  .ev-art::after{content:"";position:absolute;top:20px;right:-14px;width:58px;height:58px;border-radius:50%;background:#fad517}

  .evf .ev-art{position:absolute;inset:0}
  .evb .strip{height:96px;flex:none;cursor:pointer}

  .evt{position:absolute;inset:0;z-index:1;color:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:64px 26px 84px}
  .evt small{display:block;font-size:12px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:#fad517}
  .evt h3{margin:8px 0 0;font-size:30px;font-weight:700;line-height:1.15;word-break:break-word}

  .evh{position:absolute;left:26px;bottom:26px;display:inline-block;padding:9px 18px;border-radius:999px;background:#fad517;color:#06215c;font-size:13px;font-weight:600;transition:transform .3s;box-shadow:0 3px 0 #bda000}

  .evclose{position:absolute;top:14px;left:14px;z-index:6;width:36px;height:36px;border:0;border-radius:50%;background:rgba(255,255,255,.2);color:#fff;font-size:20px;cursor:pointer;backdrop-filter:blur(6px);transition:transform .3s,background .2s}
  .evclose:hover{transform:rotate(90deg);background:rgba(255,255,255,.35)}

  .evbody{flex:1;overflow:auto;padding:20px 24px 8px}
  .evchip{display:inline-block;padding:5px 12px;border-radius:10px;background:#fdf0b0;font-size:12px;font-weight:700}
  .evbody h3{margin:12px 0 10px;font-size:26px;font-weight:700;line-height:1.2;word-break:break-word}

  .lbl{margin-top:12px;font-size:12px;color:#6b7280}
  .val{font-size:15px;font-weight:500;color:#1f2937;word-break:break-word}

  .evact,.fa{display:flex;gap:10px;justify-content:flex-end;padding:14px 24px 20px;flex:none}

  .eb{padding:9px 20px;border-radius:12px;border:1px solid #e5e7eb;background:#fff;color:#06215c;font:500 14px Poppins,sans-serif;cursor:pointer;transition:transform .2s,box-shadow .2s,background .2s}
  .eb:hover{background:#f5f7fb;transform:translateY(-2px)}
  .eb:active{transform:translateY(2px)}
  .eb.d{color:#dc2626;border-color:#fbcfd0}
  .eb.d:hover{background:#dc2626;color:#fff;border-color:#dc2626}
  .eb.p{background:#06215c;color:#fff;border-color:#06215c;font-weight:600;box-shadow:0 4px 0 #031236}
  .eb.p:hover{background:#0a328c;box-shadow:0 6px 0 #031236}
  .eb.p.r{background:#dc2626;border-color:#dc2626;box-shadow:0 4px 0 #7f1d1d}

  .evs{width:min(560px,100%);max-height:100%;overflow:auto;padding:26px 26px 6px;border-radius:1.5rem;background:#fff;color:#06215c;box-shadow:0 30px 70px -20px rgba(0,0,0,.7),0 4px 0 rgba(250,213,23,.55);animation:evpop .4s cubic-bezier(.2,.7,.2,1)}
  .evs.sm{width:min(400px,100%)}
  .evs h3{margin:0 0 16px;font-size:20px;font-weight:700}
  .evs p{margin:0 0 6px}
  .evs>*+*{margin-top:12px}
  .evs .fa{padding:10px 0 20px}
  .evs label{display:block;margin-bottom:5px;font-size:12px;color:#6b7280}

  .fi{width:100%;box-sizing:border-box;padding:12px 16px;border:1px solid #e5e7eb;border-radius:14px;background:#fff;color:#1f2937;font:400 15px Poppins,sans-serif;transition:border-color .2s,box-shadow .2s}
  .fi:focus{outline:0;border-color:#fad517;box-shadow:0 0 0 4px rgba(250,213,23,.28)}

  .g2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
  .evs .ck{display:flex;align-items:center;gap:10px;font-size:14px;color:#1f2937;margin:0}
  .evs .ck input{width:18px;height:18px;accent-color:#06215c}
  .evs .nt{font-size:12px;color:#6b7280}
  .evs .er{font-size:13px;color:#dc2626;min-height:0}

  #ev-toast{position:fixed;left:50%;bottom:24px;z-index:90;transform:translate(-50%,24px);opacity:0;pointer-events:none;padding:12px 20px;border-radius:14px;background:#06215c;color:#fff;font:500 14px Poppins,sans-serif;box-shadow:0 14px 30px -8px rgba(0,0,0,.5),0 3px 0 #fad517;transition:all .4s cubic-bezier(.2,.7,.2,1)}
  #ev-toast.show{opacity:1;transform:translate(-50%,0)}

  .evo :focus-visible,#ev-cal :focus-visible{outline:3px solid #fad517;outline-offset:2px}

  @media(max-width:640px){.g2{grid-template-columns:1fr}}
  @media(prefers-reduced-motion:reduce){.evi{transition-duration:.01ms}#ev-cal,.evx,.evs,.evo.open,#ev-cal .cg.sw{animation:none}}

  /* gradient pass (kept for light theme; dark theme is overridden to solid below) */
  .ev-art::after{background:linear-gradient(135deg,#fad517,#ffe86b)}
  .evh{background:linear-gradient(135deg,#fad517,#ffe86b)}
  .evchip{background:linear-gradient(135deg,#fad517,#ffe86b)}
  .evb,.eve,.evs{background:linear-gradient(170deg,#ffffff 0%,#f3f6ff 100%)}
  .eve .evs{background:transparent}
  .eve .fa{background:#f3f6ff}
  .eb{background:linear-gradient(180deg,#ffffff,#f0f3fb)}
  .eb:hover{background:linear-gradient(180deg,#ffffff,#e6ecfa)}
  .eb.d:hover{background:linear-gradient(135deg,#dc2626,#f05252)}
  .eb.p{background:linear-gradient(135deg,#06215c,#0a328c)}
  .eb.p:hover{background:linear-gradient(135deg,#0a328c,#2a5fe0)}
  .eb.p.r,.eb.p.r:hover{background:linear-gradient(135deg,#dc2626,#f05252)}
  #ev-toast{background:linear-gradient(135deg,#06215c,#0a328c)}

  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .evb,
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .eve,
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .evs{background:linear-gradient(160deg,#0b3190 0%,#081a45 42%,#030b24 100%);color:#fff}
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .eve .evs{background:transparent}
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .eve .fa{background:#030b24;border-top:1px solid rgba(255,255,255,.12)}
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .lbl,
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .evs label,
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .evs .nt{color:#a9b8d6}
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .val,
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .evs .ck{color:#fff}
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .evchip{color:#06215c}
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .evs .er{color:#ff9a9a}
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .fi{background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.16);color:#fff;color-scheme:dark}
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .fi::placeholder{color:#8fa3c8}
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .fi option{background:#0a1a3d;color:#fff}
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .evs .ck input{accent-color:#fad517}
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .eb{background:linear-gradient(180deg,rgba(255,255,255,.14),rgba(255,255,255,.04));border-color:rgba(255,255,255,.18);color:#fff}
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .eb:hover{background:linear-gradient(180deg,rgba(255,255,255,.22),rgba(255,255,255,.08))}
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .eb.d{color:#ff9a9a;border-color:rgba(255,138,138,.4)}
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .eb.d:hover{background:linear-gradient(135deg,#dc2626,#f05252);color:#fff;border-color:#dc2626}
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .eb.p{background:linear-gradient(135deg,#fad517,#ffe86b);color:#06215c;border-color:#fad517;box-shadow:0 4px 0 #bda000}
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .eb.p:hover{background:linear-gradient(135deg,#ffe86b,#fff4a8);box-shadow:0 6px 0 #bda000}
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .eb.p.r,
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .eb.p.r:hover{background:linear-gradient(135deg,#dc2626,#f05252);color:#fff;border-color:#dc2626;box-shadow:0 4px 0 #7f1d1d}
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .evx.edit .evclose{background:rgba(255,255,255,.2);color:#fff}
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .evx.edit .evclose:hover{background:rgba(255,255,255,.34)}

  /* PATCH 1: solid dark, no gradient */
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .evb,
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .eve,
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .evs{background:#081a45;color:#fff}
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) .eve .fa{background:#081a45}

  /* PATCH 2: filter buttons */

  /* ================= Events / GPOA page ================= */
  #gp{--ink:#fff;--mut:#a9b8d6;--ln:rgba(255,255,255,.12);--card:#081a45;--pill:#0a1330;--pillb:rgba(255,255,255,.12);--pillt:#fff;--cell:transparent;--off:rgba(255,255,255,.03);--sel:#fff;--th:#0a328c;font-family:'Poppins',system-ui,sans-serif;color:var(--ink);max-width:1400px}
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) #gp{--ink:#fff;--mut:#a9b8d6;--ln:rgba(255,255,255,.12);--card:#081a45;--pill:#0a1330;--pillb:rgba(255,255,255,.12);--pillt:#fff;--cell:transparent;--off:rgba(255,255,255,.03);--sel:#fff}
  #gp *{box-sizing:border-box}

  #gp .gp-head{display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:12px;margin-bottom:18px}
  #gp .gp-head h1{margin:0;font-size:24px;font-weight:700;color:var(--ink);line-height:1.2}
  #gp .gp-head p{margin:3px 0 0;font-size:14px;color:var(--mut)}
  #gp .gp-head p b{color:var(--ink)}

  #gp .gp-add{display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border:0;border-radius:12px;background:linear-gradient(135deg,#fad517,#ffe86b);color:#06215c;font:700 14px Poppins,sans-serif;cursor:pointer;box-shadow:0 4px 0 #bda000;transition:transform .2s,box-shadow .2s}
  #gp .gp-add:hover{transform:translateY(-2px);box-shadow:0 6px 0 #bda000,0 12px 22px rgba(250,213,23,.3)}
  #gp .gp-add:active{transform:translateY(3px);box-shadow:0 1px 0 #bda000}

  #gp .gp-yr,#gp .gp-fl{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:14px}
  #gp .gp-yr button{padding:7px 20px;border-radius:999px;border:1px solid var(--pillb);background:var(--pill);color:var(--pillt);font:700 14px Poppins,sans-serif;cursor:pointer;transition:transform .2s,background .2s}
  #gp .gp-fl button{padding:6px 18px;border-radius:10px;border:1px solid var(--pillb);background:var(--pill);color:var(--pillt);font:500 13px Poppins,sans-serif;cursor:pointer;transition:transform .2s,background .2s}
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) #gp .gp-fl button{color:#c9d4ee}
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) #gp .gp-fl button.on{color:#06215c}
  #gp .gp-yr button:hover,#gp .gp-fl button:hover{transform:translateY(-2px)}
  #gp .gp-yr button.on,#gp .gp-fl button.on{background:#fad517;border-color:#fad517;color:#06215c;font-weight:700}

  #gp .gp-grid{display:grid;gap:24px;grid-template-columns:minmax(0,1fr);align-items:start}
  @media(min-width:900px){#gp .gp-grid{grid-template-columns:minmax(0,3.2fr) minmax(0,1fr)}}

  #gp .gp-cal,#gp .gp-side{background:var(--card);border:1px solid var(--ln);border-radius:16px;box-shadow:0 10px 30px -22px rgba(6,33,92,.5)}
  #gp .gp-cal{padding:16px}

  #gp .gp-ch{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
  #gp .gp-ct{text-align:center}
  #gp .gp-ct b{display:block;font-size:18px;font-weight:700;color:var(--ink);line-height:1.2}
  #gp .gp-td{background:none;border:0;padding:0;margin-top:2px;font:600 12px Poppins,sans-serif;color:var(--ink);cursor:pointer;text-decoration:underline;text-underline-offset:3px;opacity:.85}
  #gp .gp-td:hover{color:#d4a900}
  #gp .gp-nb{width:36px;height:36px;border-radius:10px;border:1px solid var(--ln);background:transparent;color:var(--ink);display:grid;place-items:center;cursor:pointer;transition:all .25s}
  #gp .gp-nb:hover{background:#fad517;border-color:#fad517;color:#06215c;transform:translateY(-2px)}

  #gp .gp-wd,#gp .gp-cg{display:grid;grid-template-columns:repeat(7,minmax(0,1fr))}
  #gp .gp-wd div{padding:6px 0;text-align:center;font-size:11px;font-weight:600;letter-spacing:.08em;color:var(--mut)}
  #gp .gp-cg.sw{animation:evsw .35s cubic-bezier(.2,.7,.2,1)}

  #gp .gp-cc{min-height:104px;min-width:0;padding:6px;border:1px solid var(--ln);margin:0 -1px -1px 0;background:var(--cell);cursor:pointer;transition:background .2s;overflow:hidden}
  #gp .gp-cc:hover{background:rgba(250,213,23,.08)}
  #gp .gp-cc.off{background:var(--off)}
  #gp .gp-cc.off .gp-dn{opacity:.45}
  #gp .gp-cc.today{box-shadow:inset 0 0 0 2px #fad517}
  #gp .gp-cc.sel{box-shadow:inset 0 0 0 2px var(--sel);background:rgba(127,150,200,.12)}
  #gp .gp-dn{display:block;font-size:12px;font-weight:700;margin:0 0 3px;color:var(--ink)}

  #gp .chip{display:block;width:100%;margin-top:4px;padding:3px 10px;border:0;border-radius:999px;text-align:left;font:600 12px Poppins,sans-serif;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;cursor:pointer;transition:transform .2s,box-shadow .2s}
  #gp .chip:hover{transform:translateY(-1px);box-shadow:0 6px 14px -6px rgba(0,0,0,.5)}
  #gp .chip.Current,#gp .chip.Ongoing{background:#fdf0b0;color:#06215c}
  #gp .chip.Future{background:#0a328c;color:#fff}
  #gp .chip.Past{background:#e5e7eb;color:#475569}
  :is(html.dark,html[data-theme="dark"],body.dark,body.dark-mode) #gp .chip.Past{background:rgba(255,255,255,.1);color:#cbd5e1}
  #gp .chip.x{display:none}
  #gp .gp-cc.open{overflow:auto}
  #gp .gp-cc.open .chip.x{display:block}

  #gp .more{display:block;margin:4px 2px 0;background:none;border:0;padding:0;font:600 11px Poppins,sans-serif;color:var(--mut);cursor:pointer}
  #gp .more:hover{color:#d4a900}
  #gp .gp-count{margin:12px 0 0;font-size:12px;color:var(--mut)}

  #gp .gp-side{padding:16px}
  #gp .gp-side h3{margin:0 0 2px;font-size:14px;font-weight:700;color:var(--ink)}
  #gp .gp-info{margin-top:10px;padding:10px 12px;border-radius:12px;background:#fad517;color:#06215c}
  #gp .gp-info b{display:block;font-size:13px;font-weight:700;line-height:1.3;overflow-wrap:anywhere}
  #gp .gp-info small{display:block;margin-top:2px;font-size:12px;font-weight:500;color:#06215c;opacity:.8}
  #gp .gp-empty{margin:12px 0 0;font-size:13px;color:var(--mut)}
  #gp :focus-visible{outline:3px solid #fad517;outline-offset:2px}

  @media(max-width:640px){
    #gp .gp-cc{min-height:56px;padding:3px}
    #gp .gp-dn{font-size:11px}
    #gp .chip{padding:1px 4px;font-size:9px}
    #gp .gp-side{padding:16px}
    #gp .gp-head h1{font-size:20px}
  }
</style>

<div id="gp">
  <div class="gp-head">
    <div>
      <h1>General Plan of Activities</h1>
      <p>The organization calendar. Events marked <b>public</b> appear on the homepage.</p>
    </div>
    <?php if ($canEdit): ?>
      <button type="button" class="gp-add" id="gp-add">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
        Add Event
      </button>
    <?php endif; ?>
  </div>

  <div class="gp-yr" id="gp-yr"></div>
  <div class="gp-fl" id="gp-fl"></div>

  <div class="gp-grid">
    <section class="gp-cal">
      <div class="gp-ch">
        <button type="button" class="gp-nb" data-nav="-1" aria-label="Previous month">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>
        </button>
        <div class="gp-ct">
          <b id="gp-t"></b>
          <button type="button" class="gp-td" data-nav="0">Today</button>
        </div>
        <button type="button" class="gp-nb" data-nav="1" aria-label="Next month">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
        </button>
      </div>
      <div class="gp-wd">
        <div>SUN</div><div>MON</div><div>TUE</div><div>WED</div><div>THU</div><div>FRI</div><div>SAT</div>
      </div>
      <div class="gp-cg" id="gp-g"></div>
      <p class="gp-count" id="gp-count"></p>
    </section>

    <aside class="gp-side">
      <h3 id="gp-st">Selected day</h3>
      <div id="gp-list"></div>
    </aside>
  </div>
</div>

<!-- The calendar submits through this form, so saving and deleting still go through the same checks as before. -->
<form id="ev-post" method="post" enctype="multipart/form-data" hidden>
  <?= csrf_field() ?>
  <input name="action"><input name="id"><input name="title"><input name="category">
  <input name="start"><input name="end"><input name="location"><input name="description"><input name="is_public"><input name="remove_image">
</form>

<script>
  window.IBITS_CAN_EDIT = <?= $canEdit ? 'true' : 'false' ?>;
  window.IBITS_IMG      = <?= $hasImage ? 'true' : 'false' ?>;
  window.IBITS_CATS     = <?= json_encode($cats, $J) ?>;
  window.IBITS_EVENTS   = <?= json_encode($jsEvents, $J) ?>;
  window.IBITS_EDIT_ID  = <?= $editId ?>;
  window.IBITS_REOPEN   = <?= json_encode($reopen, $J) ?>;
</script>

<script>
(function () {
  var CAN  = !!window.IBITS_CAN_EDIT;
  var IMG  = !!window.IBITS_IMG;
  var M    = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
  var CATS = window.IBITS_CATS || ['Competition', 'Seminar', 'Workshop', 'Social', 'Meeting', 'Other'];
  var EV   = (window.IBITS_EVENTS || []).slice();
  var cur  = new Date();
  var editing = null;
  var viewing = null;
  var flt     = 'All';

  cur = new Date(cur.getFullYear(), cur.getMonth(), 1);

  var q = function (s) { return document.querySelector(s); };
  var p2 = function (n) { return String(n).padStart(2, '0'); };
  var esc = function (s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  };
  var iso = function (d) { return d.getFullYear() + '-' + p2(d.getMonth() + 1) + '-' + p2(d.getDate()); };
  var pd  = function (s) { var a = s.split('-'); return new Date(+a[0], a[1] - 1, +a[2]); };
  var st  = function (e) {
    var t = iso(new Date());
    return e.end < t ? 'Past' : e.start > t ? 'Future' : e.start === e.end ? 'Current' : 'Ongoing';
  };

  var sel = iso(new Date());

  function fdr(e) {
    var a = pd(e.start);
    var b = pd(e.end);
    var y = a.getFullYear();
    if (e.start === e.end) {
      return M[a.getMonth()] + ' ' + a.getDate() + ', ' + y;
    }
    if (a.getMonth() === b.getMonth() && y === b.getFullYear()) {
      return M[a.getMonth()] + ' ' + a.getDate() + '–' + b.getDate() + ', ' + y;
    }
    return M[a.getMonth()] + ' ' + a.getDate() + (y !== b.getFullYear() ? ', ' + y : '') + ' – ' + M[b.getMonth()] + ' ' + b.getDate() + ', ' + b.getFullYear();
  }

  function tm(s) {
    if (!s) return '';
    var h = +s.slice(0, 2);
    return (h % 12 || 12) + ':' + s.slice(3, 5) + ' ' + (h < 12 ? 'AM' : 'PM');
  }

  var ftm = function (e) {
    return (e.st || e.et) ? (tm(e.st) || '—') + (e.et ? ' – ' + tm(e.et) : '') : 'All day';
  };

  function art(el, e) {
    el.style.backgroundImage = e.img
      ? 'linear-gradient(135deg,rgba(6,33,92,.55),rgba(10,50,140,.8)),url("' + e.img + '")'
      : '';
  }

  /* ---- popups (title card first, flips to details) ---- */
  var o = document.createElement('div');
  o.innerHTML =
    '<div class="evo" id="ev-view"><div class="evx" id="ev-x">' +
      '<button class="evclose" data-x aria-label="Close">×</button>' +
      '<div class="evi">' +
        '<div class="evf" id="ev-f" tabindex="0" role="button" aria-label="Flip to see details">' +
          '<div class="ev-art" id="ev-fa"></div>' +
          '<div class="evt"><small id="ev-fk"></small><h3 id="ev-ft"></h3><span class="evh">Click to see details →</span></div>' +
        '</div>' +
        '<div class="evb">' +
          '<div class="ev-art strip" id="ev-st" title="Flip back"></div>' +
          '<div class="evbody"><span class="evchip" id="ev-chip"></span><h3 id="ev-bt"></h3><div id="ev-det"></div></div>' +
          '<div class="evact">' +
            (CAN ? '<button class="eb" id="ev-edit">Edit</button><button class="eb d" id="ev-del">Delete</button>' : '') +
            '<button class="eb p" data-x>Close</button>' +
          '</div>' +
        '</div>' +
        '<div class="eve" id="ev-ef"></div>' +
      '</div>' +
    '</div></div>' +
    (CAN ?
      '<div class="evo" id="ev-fm"><div class="evs" id="ev-fbox" role="dialog" aria-modal="true" aria-labelledby="ev-fh">' +
        '<h3 id="ev-fh">Edit Event</h3>' +
        '<input class="fi" id="f-t" maxlength="150" placeholder="Event title" aria-label="Event title">' +
        '<div class="g2">' +
          '<div><label for="f-sd">Start date</label><input class="fi" type="date" id="f-sd"></div>' +
          '<div><label for="f-ed">End date</label><input class="fi" type="date" id="f-ed"></div>' +
        '</div>' +
        '<div class="g2">' +
          '<div><label for="f-st">Start time</label><input class="fi" type="time" id="f-st"></div>' +
          '<div><label for="f-et">End time</label><input class="fi" type="time" id="f-et"></div>' +
        '</div>' +
        '<input class="fi" id="f-l" maxlength="150" placeholder="Location" aria-label="Location">' +
        '<select class="fi" id="f-c" aria-label="Category">' + CATS.map(function (c) { return '<option>' + esc(c) + '</option>'; }).join('') + '</select>' +
        '<div><label for="f-v">Visibility</label><select class="fi" id="f-v"><option value="1">Public (shown on homepage)</option><option value="0">Members only</option></select></div>' +
        '<textarea class="fi" id="f-d" rows="3" placeholder="Description" aria-label="Description"></textarea>' +
        (IMG ?
          '<div><label for="f-i">Cover photo (optional, max 2 MB)</label>' +
          '<input class="fi" type="file" name="image" id="f-i" accept="image/jpeg,image/png,image/gif,image/webp">' +
          '<div id="f-cur" style="display:none;margin-top:10px">' +
            '<img id="f-cimg" alt="Current cover photo" style="width:100%;max-height:110px;object-fit:cover;border-radius:12px">' +
            '<label class="ck" style="margin-top:8px"><input type="checkbox" id="f-rm">Remove current cover photo</label>' +
          '</div></div>'
          : '') +
        '<p class="nt">Status (Past, Current, Ongoing, Future) is worked out from the dates.</p>' +
        '<p class="er" id="f-er" role="alert"></p>' +
        '<div class="fa"><button class="eb" id="f-cancel">Cancel</button><button class="eb p" id="f-save">Save</button></div>' +
      '</div></div>' +
      '<div class="evo" id="ev-dm"><div class="evs sm" role="alertdialog" aria-modal="true" aria-labelledby="dm-h">' +
        '<h3 id="dm-h">Delete event?</h3>' +
        '<p class="nt" id="dm-t"></p>' +
        '<div class="fa"><button class="eb" data-x>Cancel</button><button class="eb p r" id="dm-ok">Delete</button></div>' +
      '</div></div>'
      : '');

  while (o.firstChild) {
    document.body.appendChild(o.firstChild);
  }

  function post(d) {
    var f = q('#ev-post');
    Object.keys(d).forEach(function (k) { f.elements.namedItem(k).value = d[k]; });
    try { sessionStorage.setItem('gp_sel', JSON.stringify({ sel: sel, flt: flt })); } catch (x) {}
    var fi = q('#f-i');
    if (d.action === 'save' && fi && fi.files && fi.files.length) {
      f.appendChild(fi);
    }
    f.submit();
  }

  /* ---- page pieces ---- */
  function evOn(k) {
    return EV
      .filter(function (e) { return e.start <= k && k <= e.end && (flt === 'All' || st(e) === flt); })
      .sort(function (a, b) {
        return a.start !== b.start
          ? (a.start < b.start ? -1 : 1)
          : ((a.st || '') < (b.st || '') ? -1 : (a.st || '') > (b.st || '') ? 1 : 0);
      });
  }

  function renderYears() {
    var ty = new Date().getFullYear();
    var lo = ty - 2;
    var hi = ty + 1;
    EV.forEach(function (e) {
      var a = +e.start.slice(0, 4);
      var b = +e.end.slice(0, 4);
      if (a < lo) lo = a;
      if (b > hi) hi = b;
    });

    var h = '';
    for (var y = lo; y <= hi; y++) {
      h += '<button type="button" class="' + (y === cur.getFullYear() ? 'on' : '') + '" data-y="' + y + '">' + y + '</button>';
    }
    q('#gp-yr').innerHTML = h;
  }

  function renderFilters() {
    q('#gp-fl').innerHTML = ['All', 'Past', 'Current', 'Ongoing', 'Future'].map(function (s) {
      return '<button type="button" class="' + (s === flt ? 'on' : '') + '" data-f="' + s + '">' + s + '</button>';
    }).join('');
  }

  function renderCal(sw) {
    var y = cur.getFullYear();
    var m = cur.getMonth();
    var f = new Date(y, m, 1).getDay();
    var n = Math.ceil((f + new Date(y, m + 1, 0).getDate()) / 7) * 7;
    var td = iso(new Date());
    var h = '';
    var L = innerWidth <= 640 ? 1 : 3;

    q('#gp-t').textContent = M[m] + ' ' + y;

    for (var i = 0; i < n; i++) {
      var d  = new Date(y, m, 1 - f + i);
      var k  = iso(d);
      var ev = evOn(k);

      h += '<div class="gp-cc' + (d.getMonth() !== m ? ' off' : '') + (k === td ? ' today' : '') + (k === sel ? ' sel' : '') + '" data-d="' + k + '">';
      h += '<span class="gp-dn">' + d.getDate() + '</span>';

      ev.forEach(function (e, j) {
        h += '<button type="button" class="chip ' + st(e) + (j >= L ? ' x' : '') + '" data-id="' + e.id + '" title="' + esc(e.title) + '">' + esc(e.title) + '</button>';
      });
      if (ev.length > L) {
        h += '<button type="button" class="more" data-more>+' + (ev.length - L) + (L > 1 ? ' more' : '') + '</button>';
      }
      h += '</div>';
    }

    var gr = q('#gp-g');
    gr.innerHTML = h;
    if (sw) {
      gr.classList.remove('sw');
      void gr.offsetWidth;
      gr.classList.add('sw');
    }

    var a = iso(new Date(y, m, 1));
    var b = iso(new Date(y, m + 1, 0));
    var c = EV.filter(function (e) {
      return e.start <= b && e.end >= a && (flt === 'All' || st(e) === flt);
    }).length;
    q('#gp-count').textContent = c + ' ' + (flt === 'All' ? '' : flt.toLowerCase() + ' ') + 'event' + (c === 1 ? '' : 's') + ' in ' + M[m] + ' ' + y;
  }

  function renderSide() {
    var d  = pd(sel);
    var ev = evOn(sel);
    q('#gp-st').textContent = M[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();

    var h = ev.map(function (e) {
      return '<article class="gp-info"><b>' + esc(e.title) + '</b><small>' + esc(fdr(e)) + '</small></article>';
    }).join('');
    if (!ev.length) h = '<p class="gp-empty">No events on this day.</p>';
    q('#gp-list').innerHTML = h;
  }

  function renderAll(sw) {
    renderYears();
    renderFilters();
    renderCal(sw);
    renderSide();
  }

  /* ---- popups logic ---- */
  function closeAll() {
    document.querySelectorAll('.evo.open').forEach(function (x) { x.classList.remove('open'); });
    q('#ev-x').classList.remove('flipped', 'edit', 'ed');
  }

  function fillView(e) {
    q('#ev-fk').textContent = st(e) + ' · ' + e.cat;
    q('#ev-ft').textContent = e.title;
    q('#ev-bt').textContent = e.title;
    q('#ev-chip').textContent = st(e) + ' · ' + e.cat;

    q('#ev-det').innerHTML = [
      ['Date', fdr(e)],
      ['Time', ftm(e)],
      ['Location', e.loc || '—'],
      ['Visibility', e.pub ? 'Public' : 'Members only'],
      ['Description', e.desc || '—'],
    ].map(function (r) {
      return '<div class="lbl">' + r[0] + '</div><div class="val">' + esc(r[1]) + '</div>';
    }).join('');

    art(q('#ev-fa'), e);
    art(q('#ev-st'), e);
  }

  function openView(id) {
    viewing = EV.filter(function (e) { return e.id == id; })[0];
    if (!viewing) return;
    fillView(viewing);
    q('#ev-x').classList.remove('flipped', 'edit', 'ed');
    q('#ev-view').classList.add('open');
    q('#ev-f').focus();
  }

  function backEdit() {
    var x = q('#ev-x');
    x.classList.remove('edit');
    clearTimeout(backEdit.t);
    backEdit.t = setTimeout(function () {
      if (!x.classList.contains('edit')) x.classList.remove('ed');
    }, 800);
  }

  function openForm(e, date, card) {
    if (!CAN) return;
    editing = e;
    var d = date || sel;
    var b = q('#ev-fbox');
    var x = q('#ev-x');
    q('#ev-fh').textContent = e ? 'Edit Event' : 'Add Event';

    q('#f-t').value  = e ? e.title : '';
    q('#f-sd').value = e ? e.start : d;
    q('#f-ed').value = e ? e.end : d;
    q('#f-st').value = e ? e.st || '' : '09:00';
    q('#f-et').value = e ? e.et || '' : '17:00';
    q('#f-l').value  = e ? e.loc || '' : '';
    q('#f-c').value  = e ? e.cat : CATS[0];
    q('#f-d').value  = e ? e.desc || '' : '';
    q('#f-v').value  = e && e.pub === 0 ? '0' : '1';
    q('#f-er').textContent = '';

    if (IMG) {
      q('#f-i').value = '';
      q('#f-rm').checked = false;
      q('#f-cur').style.display = e && e.img ? 'block' : 'none';
      if (e && e.img) q('#f-cimg').src = e.img;
    }

    if (card) {
      clearTimeout(backEdit.t);
      q('#ev-ef').appendChild(b);
      b.scrollTop = 0;
      x.classList.add('ed', 'edit');
      setTimeout(function () { q('#f-t').focus({ preventScroll: true }); }, 700);
      return;
    }

    closeAll();
    q('#ev-fm').appendChild(b);
    q('#ev-fm').classList.add('open');
    q('#f-t').focus();
  }

  function save() {
    var t   = q('#f-t').value.trim();
    var a   = q('#f-sd').value;
    var b   = q('#f-ed').value || a;
    var er  = q('#f-er');
    var s   = q('#f-st').value;
    var en  = q('#f-et').value;
    var fi  = IMG ? q('#f-i') : null;
    var fl  = fi && fi.files[0];

    if (!t || !a) { er.textContent = 'Please add a title and a start date.'; return; }
    if (b < a)    { er.textContent = 'End date can’t be before the start date.'; return; }
    if (b === a && en && s && en <= s) { er.textContent = 'End time must be after the start time.'; return; }
    if (fl && fl.size > 2097152) { er.textContent = 'Image must be 2 MB or smaller.'; return; }
    if (fl && !/^image\//.test(fl.type)) { er.textContent = 'Please choose an image file.'; return; }

    sel = a;
    post({
      action: 'save',
      id: editing ? editing.id : 0,
      title: t,
      category: q('#f-c').value,
      start: a + 'T' + (s || '00:00'),
      end: en ? b + 'T' + en : (b !== a ? b + 'T23:59' : ''),
      location: q('#f-l').value.trim(),
      description: q('#f-d').value.trim(),
      is_public: q('#f-v').value,
      remove_image: IMG && q('#f-rm').checked ? '1' : '0',
    });
  }

  if (CAN) {
    q('#f-save').onclick = save;
    q('#ev-edit').onclick = function () { openForm(viewing, null, true); };
    q('#f-cancel').onclick = function () {
      q('#ev-x').classList.contains('edit') ? backEdit() : closeAll();
    };
    q('#ev-del').onclick = function () {
      q('#dm-t').textContent = '“' + viewing.title + '” will be removed from the calendar. This can’t be undone.';
      closeAll();
      q('#ev-dm').classList.add('open');
    };
    q('#dm-ok').onclick = function () { post({ action: 'delete', id: viewing.id }); };
    q('#gp-add').onclick = function () { openForm(null, sel); };
  }

  q('#ev-f').onclick = function () { q('#ev-x').classList.add('flipped'); };
  q('#ev-f').onkeydown = function (e) {
    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); this.click(); }
  };
  q('#ev-st').onclick = function () { q('#ev-x').classList.remove('flipped'); };

  function pick(k) {
    sel = k;
    var d = pd(k);
    if (d.getMonth() !== cur.getMonth() || d.getFullYear() !== cur.getFullYear()) {
      cur = new Date(d.getFullYear(), d.getMonth(), 1);
    }
    renderAll();
  }

  document.addEventListener('click', function (e) {
    var t = e.target;
    var c;

    if (t.closest('.evo')) {
      if (t.closest('[data-x]') || (q('.evo.open') && !t.closest('.evx,.evs'))) closeAll();
      return;
    }

    if (c = t.closest('#gp-fl [data-f]')) {
      flt = c.dataset.f;
      return renderAll(1);
    }

    if (c = t.closest('#gp-yr [data-y]')) {
      cur = new Date(+c.dataset.y, cur.getMonth(), 1);
      var s = pd(sel);
      if (s.getFullYear() !== cur.getFullYear()) {
        sel = iso(new Date(cur.getFullYear(), cur.getMonth(), 1));
      }
      return renderAll(1);
    }

    if (c = t.closest('#gp [data-nav]')) {
      var v = +c.dataset.nav;
      var n = new Date();
      if (v) {
        cur = new Date(cur.getFullYear(), cur.getMonth() + v, 1);
        var td = new Date();
        sel = (cur.getFullYear() === td.getFullYear() && cur.getMonth() === td.getMonth()) ? iso(td) : iso(cur);
      } else {
        cur = new Date(n.getFullYear(), n.getMonth(), 1);
        sel = iso(n);
      }
      return renderAll(1);
    }

    if (c = t.closest('#gp .chip')) {
      var cell = t.closest('.gp-cc');
      if (cell) { sel = cell.dataset.d; renderAll(); }
      return openView(c.dataset.id);
    }

    if (t.closest('#gp [data-more]')) {
      t.closest('.gp-cc').classList.toggle('open');
      return;
    }

    if (c = t.closest('#gp .gp-cc')) {
      return pick(c.dataset.d);
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeAll();
  });

  var lastL = innerWidth <= 640 ? 1 : 3;
  addEventListener('resize', function () {
    var l = innerWidth <= 640 ? 1 : 3;
    if (l !== lastL) {
      lastL = l;
      renderCal();
    }
  });

  try {
    var sv = JSON.parse(sessionStorage.getItem('gp_sel') || 'null');
    sessionStorage.removeItem('gp_sel');
    if (sv && sv.sel) {
      sel = sv.sel;
      flt = sv.flt || 'All';
      var sd0 = pd(sel);
      cur = new Date(sd0.getFullYear(), sd0.getMonth(), 1);
    }
  } catch (x) {}

  renderAll();

  var R = window.IBITS_REOPEN;
  var E = window.IBITS_EDIT_ID;

  if (CAN && R) {
    var ex = R.id ? EV.filter(function (e) { return e.id == R.id; })[0] : null;
    var sd = pd(R.start || iso(new Date()));

    sel = R.start || sel;
    cur = new Date(sd.getFullYear(), sd.getMonth(), 1);
    renderAll();
    openForm(ex || null, R.start);

    if (!Array.prototype.some.call(q('#f-c').options, function (op) { return op.value === R.cat; })) {
      q('#f-c').insertAdjacentHTML('beforeend', '<option>' + esc(R.cat) + '</option>');
    }

    q('#f-t').value  = R.title;
    q('#f-sd').value = R.start;
    q('#f-ed').value = R.end || R.start;
    q('#f-st').value = R.st || '';
    q('#f-et').value = R.et || '';
    q('#f-l').value  = R.loc;
    q('#f-c').value  = R.cat || CATS[0];
    q('#f-d').value  = R.desc;
    q('#f-v').value  = String(R.pub);
    q('#f-er').textContent = 'Check the details and save again.';
  } else if (CAN && E) {
    var ee = EV.filter(function (e) { return e.id == E; })[0];
    if (ee) {
      sel = ee.start;
      var sd2 = pd(ee.start);
      cur = new Date(sd2.getFullYear(), sd2.getMonth(), 1);
      renderAll();
      openView(ee.id);
      setTimeout(function () { openForm(ee, null, true); }, 50);
    }
  }
})();
</script>

<?php dash_end(); ?>