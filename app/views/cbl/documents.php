<?php
/**
 * CBL Documents Sub-Module Controller
 * Pre-condition: Assumes an active, global database connection variable ($pdo) 
 * exists via your website's main entry shell script.
 */

// 1. Safety Guard: Block direct URL sniffing if not routed through your system core
if (!isset($pdo)) {
    header("Location: dashboard.php");
    exit;
}

// 2. Initialize container structures to prevent empty frontend crashes
$document_records = [];
$document_summary = [
    'draft_count'      => 0,
    'pending_review'   => 0,
    'published_count'  => 0,
    'rejected_count'   => 0
];

try {
    // 3. Query A: Fetch relational document records with Inner Joins
    // Links created_by back to the users table to get the author's official full name
    $documents_query = "
        SELECT 
            d.id AS document_id,
            d.title,
            d.version,
            d.file_path,
            d.status AS document_status,
            d.created_at,
            d.updated_at,
            u.full_name AS author_name,
            u.id_number AS author_id_num
        FROM cbl_documents d
        INNER JOIN users u ON d.created_by = u.id
        ORDER BY d.updated_at DESC, d.created_at DESC
    ";

    $stmt = $pdo->query($documents_query);
    $document_records = $stmt->fetchAll();

    // 4. Query B: Compute global status metrics for top-level operational counters
    $summary_query = "
        SELECT 
            COUNT(CASE WHEN status = 'draft' THEN 1 END) AS draft_total,
            COUNT(CASE WHEN status IN ('pending_adviser', 'pending_president', 'pending_vp') THEN 1 END) AS pending_total,
            COUNT(CASE WHEN status = 'published' THEN 1 END) AS published_total,
            COUNT(CASE WHEN status = 'rejected' THEN 1 END) AS rejected_total
        FROM cbl_documents
    ";
    
    $summary_data = $pdo->query($summary_query)->fetch();
    
    if ($summary_data) {
        $document_summary['draft_count']      = (int)($summary_data['draft_total'] ?? 0);
        $document_summary['pending_review']   = (int)($summary_data['pending_total'] ?? 0);
        $document_summary['published_count']  = (int)($summary_data['published_total'] ?? 0);
        $document_summary['rejected_count']   = (int)($summary_data['rejected_total'] ?? 0);
    }

} catch (\PDOException $e) {
    // Capture exception safely inside systemic system error records
    error_log("CBL Documents Module Exception: " . $e->getMessage());
    $database_error = "Unable to process the document management records at this time.";
}