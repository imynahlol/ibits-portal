<?php
declare(strict_types=1);

/* ============ Events ============ */
function event_status(array $ev): string {
    $now   = date('Y-m-d H:i:s');
    $start = $ev['start_datetime'];
    $end   = $ev['end_datetime'] ?: $start;
    if ($end < $now)   return 'Past';
    if ($start > $now) return 'Upcoming';
    return 'Ongoing';
}

/** Events for the homepage: what's happening or coming first, then recent past. */
function home_events(int $limit = 4): array {
    $all = q_all('SELECT * FROM events WHERE is_public = 1 ORDER BY start_datetime');
    $now = date('Y-m-d H:i:s');
    $live = array_filter($all, fn($e) => ($e['end_datetime'] ?: $e['start_datetime']) >= $now);
    $past = array_reverse(array_filter($all, fn($e) => ($e['end_datetime'] ?: $e['start_datetime']) < $now));
    return array_slice([...array_values($live), ...array_values($past)], 0, $limit);
}

/* ============ Population ============ */
function population(): array {
    $rows = q_all("SELECT program, year_level, COUNT(*) AS n FROM users
                   WHERE account_type = 'member' AND status = 'active' AND program IS NOT NULL
                   GROUP BY program, year_level");
    $by = [];
    foreach ($rows as $r) $by[$r['program']][$r['year_level']] = (int)$r['n'];

    $years = ['1st Year', '2nd Year', '3rd Year', '4th Year'];
    $out = ['programs' => [], 'officers' => (int)q_val("SELECT COUNT(*) FROM users WHERE account_type='admin' AND status='active'")];
    $students = 0;
    foreach (PROGRAMS as $prog => $levels) {
        $list = [];
        for ($i = 0; $i < $levels; $i++) $list[$prog . ' ' . ($i + 1)] = $by[$prog][$years[$i]] ?? 0;
        $out['programs'][$prog] = ['levels' => $list, 'total' => array_sum($list)];
        $students += array_sum($list);
    }
    $out['students'] = $students;
    $out['total']    = $students + $out['officers'];
    return $out;
}

/* ============ Help desk ============ */
const TICKET_CATEGORIES = ['General', 'Account Concern', 'Internet / Portal Issue', 'Form Request', 'Event Concern', 'Other'];
const TICKET_STATUS = ['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved'];

function tickets_for_user(int $uid): array {
    $tickets = q_all('SELECT * FROM tickets WHERE user_id = ? ORDER BY id DESC', [$uid]);
    return attach_replies($tickets);
}
function tickets_all(string $status = ''): array {
    $sql = 'SELECT t.*, u.full_name, u.id_number FROM tickets t JOIN users u ON u.id = t.user_id';
    $params = [];
    if (isset(TICKET_STATUS[$status])) { $sql .= ' WHERE t.status = ?'; $params[] = $status; }
    return attach_replies(q_all($sql . ' ORDER BY t.id DESC', $params));
}
function attach_replies(array $tickets): array {
    foreach ($tickets as &$t) {
        $t['replies'] = q_all('SELECT r.*, u.full_name, u.position FROM ticket_replies r JOIN users u ON u.id = r.user_id WHERE ticket_id = ? ORDER BY r.id', [$t['id']]);
    }
    return $tickets;
}
function status_badge(string $status): string {
    $label = TICKET_STATUS[$status] ?? ucfirst($status);
    return '<span class="badge badge-' . e($status) . '">' . e($label) . '</span>';
}

/* ============ Fines / members ============ */
function members_list(): array {
    return q_all("SELECT id, full_name, id_number FROM users WHERE account_type='member' AND status='active' ORDER BY full_name");
}
