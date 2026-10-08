<?php
/**
 * CBL Approvals Sub-Module Controller
 * Pre-condition: Assumes an active, global database connection variable ($pdo) 
 * exists via your website's main entry shell script.
 */

// 1. Safety Guard: Block direct URL sniffing if not routed through your system core
if (!isset($pdo)) {
    header("Location: dashboard.php");
    exit;
}

// 2. Initialize container structures to prevent empty frontend crashes
$approval_records = [];
$approval_summary = [
    'pending_count'  => 0,
    'approved_count' => 0,
    'rejected_count' => 0
];

try {
    // 3. Query A: Fetch relational approval workflows with Inner/Left Joins
    // We use LEFT JOIN for approved_by because pending approvals start out as NULL
    $approvals_query = "
        SELECT 
            ca.id AS approval_id,
            ca.role AS required_role,
            ca.status AS approval_status,
            ca.remarks,
            ca.approved_at,
            cbl.title AS document_title,
            cbl.version AS document_version,
            u_reviewer.full_name AS reviewer_name,
            u_creator.full_name AS document_author
        FROM cbl_approvals ca
        INNER JOIN cbl_documents cbl ON ca.cbl_id = cbl.id
        INNER JOIN users u_creator ON cbl.created_by = u_creator.id
        LEFT JOIN users u_reviewer ON ca.approved_by = u_reviewer.id
        ORDER BY cbl.created_at DESC, ca.id ASC
    ";

    $stmt = $pdo->query($approvals_query);
    $approval_records = $stmt->fetchAll();

    // 4. Query B: Compute review lifecycle totals for overview counters
    $summary_query = "
        SELECT 
            COUNT(CASE WHEN status = 'pending' THEN 1 END) AS pending_total,
            COUNT(CASE WHEN status = 'approved' THEN 1 END) AS approved_total,
            COUNT(CASE WHEN status = 'rejected' THEN 1 END) AS rejected_total
        FROM cbl_approvals
    ";
    
    $summary_data = $pdo->query($summary_query)->fetch();
    
    if ($summary_data) {
        $approval_summary['pending_count']  = (int)($summary_data['pending_total'] ?? 0);
        $approval_summary['approved_count'] = (int)($summary_data['approved_total'] ?? 0);
        $approval_summary['rejected_count'] = (int)($summary_data['rejected_total'] ?? 0);
    }

} catch (\PDOException $e) {
    // Capture exception safely inside systemic system error records
    error_log("CBL Approvals Module Exception: " . $e->getMessage());
    $database_error = "Unable to process document approval pipelines at this time.";
}