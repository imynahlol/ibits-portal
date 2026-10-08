<?php
/**
 * Ticket Replies Sub-Module Controller
 * Pre-condition: Assumes an active, global database connection variable ($pdo) 
 * exists via your website's main entry shell script.
 */

// 1. Safety Guard: Block direct URL sniffing if not routed through your system core
if (!isset($pdo)) {
    header("Location: dashboard.php");
    exit;
}

// 2. Extract and sanitize the active target Ticket ID from the query string
// Example URL target: dashboard.php?page=ticket_view&ticket_id=12
$active_ticket_id = isset($_GET['ticket_id']) ? (int)$_GET['ticket_id'] : 0;

// 3. Initialize container structures to prevent empty frontend crashes
$reply_records = [];
$reply_summary = [
    'total_replies_count' => 0,
    'last_reply_time'     => null
];

// Only run queries if a valid ticket ID has been provided in the URL context
if ($active_ticket_id > 0) {
    try {
        // 4. Query A: Fetch chronological reply streams with Inner Joins
        // Connects user_id back to users to display real names and roles side-by-side
        $replies_query = "
            SELECT 
                tr.id AS reply_id,
                tr.message,
                tr.created_at,
                u.full_name AS replier_name,
                u.role AS replier_role,
                u.profile_picture AS replier_avatar
            FROM ticket_replies tr
            INNER JOIN users u ON tr.user_id = u.id
            WHERE tr.ticket_id = :ticket_id
            ORDER BY tr.created_at ASC
        ";

        $stmt = $pdo->prepare($replies_query);
        $stmt->execute(['ticket_id' => $active_ticket_id]);
        $reply_records = $stmt->fetchAll();

        // 5. Query B: Compute conversation thread metrics for this specific ticket
        $summary_query = "
            SELECT 
                COUNT(*) AS total_replies,
                MAX(created_at) AS latest_reply
            FROM ticket_replies
            WHERE ticket_id = :ticket_id
        ";
        
        $stmt_summary = $pdo->prepare($summary_query);
        $stmt_summary->execute(['ticket_id' => $active_ticket_id]);
        $summary_data = $stmt_summary->fetch();
        
        if ($summary_data) {
            $reply_summary['total_replies_count'] = (int)($summary_data['total_replies'] ?? 0);
            $reply_summary['last_reply_time']     = $summary_data['latest_reply'];
        }

    } catch (\PDOException $e) {
        error_log("Ticket Replies Module Exception: " . $e->getMessage());
        $database_error = "Unable to process conversation streams at this time.";
    }
}
