<?php
/**
 * Tickets Sub-Module Controller
 * Pre-condition: Assumes an active, global database connection variable ($pdo) 
 * exists via your website's main entry shell script.
 */

// 1. Safety Guard: Block direct URL sniffing if not routed through your system core
if (!isset($pdo)) {
    header("Location: dashboard.php");
    exit;
}

// 2. Initialize container structures to prevent empty frontend crashes
$ticket_records = [];
$ticket_summary = [
    'open_count'        => 0,
    'in_progress_count' => 0,
    'resolved_count'    => 0,
    'high_priority'     => 0
];

try {
    // 3. Query A: Fetch relational support tickets with Joins
    // Arranged to prioritize high-priority, unresolved tickets at the top
    $tickets_query = "
        SELECT 
            t.id AS ticket_id,
            t.subject,
            t.description,
            t.status AS ticket_status,
            t.priority AS ticket_priority,
            t.created_at,
            t.updated_at,
            u_author.full_name AS author_name,
            u_author.id_number AS author_id_num,
            u_agent.full_name AS assigned_agent_name
        FROM tickets t
        INNER JOIN users u_author ON t.user_id = u_author.id
        LEFT JOIN users u_agent ON t.assigned_to = u_agent.id
        ORDER BY 
            CASE WHEN t.status = 'resolved' THEN 2 ELSE 1 END ASC,
            CASE WHEN t.priority = 'high' THEN 1 
                 WHEN t.priority = 'medium' THEN 2 
                 ELSE 3 END ASC,
            t.created_at DESC
    ";

    $stmt = $pdo->query($tickets_query);
    $ticket_records = $stmt->fetchAll();

    // 4. Query B: Compute summary metrics leveraging idx_tickets_status
    $summary_query = "
        SELECT 
            COUNT(CASE WHEN status = 'open' THEN 1 END) AS open_total,
            COUNT(CASE WHEN status = 'in_progress' THEN 1 END) AS progress_total,
            COUNT(CASE WHEN status = 'resolved' THEN 1 END) AS resolved_total,
            COUNT(CASE WHEN priority = 'high' AND status != 'resolved' THEN 1 END) AS high_priority_total
        FROM tickets
    ";
    
    $summary_data = $pdo->query($summary_query)->fetch();
    
    if ($summary_data) {
        $ticket_summary['open_count']        = (int)($summary_data['open_total'] ?? 0);
        $ticket_summary['in_progress_count'] = (int)($summary_data['progress_total'] ?? 0);
        $ticket_summary['resolved_count']    = (int)($summary_data['resolved_total'] ?? 0);
        $ticket_summary['high_priority']     = (int)($summary_data['high_priority_total'] ?? 0);
    }

} catch (\PDOException $e) {
    error_log("Tickets Module Exception: " . $e->getMessage());
    $database_error = "Unable to process help desk ticket records at this time.";
}