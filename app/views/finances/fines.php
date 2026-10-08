<?php
/**
 * Fines Sub-Module Controller
 * Pre-condition: Assumes an active, global database connection variable ($pdo) 
 * exists via your website's main entry shell script.
 */

// 1. Safety Guard: Block direct URL snapping if not routed through your system core
if (!isset($pdo)) {
    header("Location: dashboard.php");
    exit;
}

// 2. Initialize container structures to prevent empty frontend crashes
$fines_records = [];
$fines_summary = [
    'total_unpaid_amount' => 0.00,
    'total_paid_amount'   => 0.00,
    'unpaid_count'        => 0
];

try {
    // 3. Query A: Fetch relational fine logs with Inner Joins
    // This resolves user_id and created_by to display actual names on your web layout
    $fines_query = "
        SELECT 
            f.id,
            f.amount,
            f.reason,
            f.status,
            f.due_date,
            f.created_at,
            u_target.full_name AS fine_recipient,
            u_target.id_number AS recipient_id,
            u_creator.full_name AS issuer_name
        FROM fines f
        INNER JOIN users u_target ON f.user_id = u_target.id
        INNER JOIN users u_creator ON f.created_by = u_creator.id
        ORDER BY f.created_at DESC
    ";

    $stmt = $pdo->query($fines_query);
    $fines_records = $stmt->fetchAll();

    // 4. Query B: Compute global summary metrics straight from the DB engine
    // Leverages your idx_fines_status index for rapid execution speeds
    $summary_query = "
        SELECT 
            SUM(CASE WHEN status = 'unpaid' THEN amount ELSE 0 END) AS unpaid_total,
            SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) AS paid_total,
            COUNT(CASE WHEN status = 'unpaid' THEN 1 END) AS unpaid_rows
        FROM fines
    ";
    
    $summary_data = $pdo->query($summary_query)->fetch();
    
    if ($summary_data) {
        $fines_summary['total_unpaid_amount'] = (float)($summary_data['unpaid_total'] ?? 0.00);
        $fines_summary['total_paid_amount']   = (float)($summary_data['paid_total'] ?? 0.00);
        $fines_summary['unpaid_count']        = (int)($summary_data['unpaid_rows'] ?? 0);
    }

} catch (\PDOException $e) {
    // Capture exception safely inside systemic system error records
    error_log("Fines Module Exception: " . $e->getMessage());
    $database_error = "Unable to process fine ledger matrices at this time.";
}