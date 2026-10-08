<?php
/**
 * Finance Sub-Module Controller
 * Designed to be integrated directly into your existing web dashboard.
 * 
 * Pre-condition: Assumes an active, global database connection variable ($pdo)
 * exists via your website's main entry wrapper (e.g., config/database.php).
 */

// 1. Safety Guard: Block direct URL sniffing if not routed through your system core
if (!isset($pdo)) {
    header("Location: dashboard.php");
    exit;
}

// 2. Initialize container arrays to prevent frontend rendering errors
$finance_records = [];
$finance_summary = [
    'total_budget'  => 0.00,
    'total_expense' => 0.00,
    'total_inflow'  => 0.00
];

try {
    // 3. Query A: Fetch relational ledger logs using Inner Joins
    // This perfectly matches your structured 'finances', 'events', and 'users' schemas
    $ledger_query = "
        SELECT 
            f.id,
            f.date_,
            f.event_ AS finance_notes,
            f.budget,
            f.expense,
            f.inflow,
            e.title AS event_title,
            u.full_name AS creator_name
        FROM finances f
        INNER JOIN events e ON f.event_id = e.id
        INNER JOIN users u ON f.created_by = u.id
        ORDER BY f.date_ DESC
    ";

    $stmt = $pdo->query($ledger_query);
    $finance_records = $stmt->fetchAll();

    // 4. Query B: Compute global summary metrics straight from the DB engine
    $summary_query = "
        SELECT 
            SUM(budget) AS total_budget, 
            SUM(expense) AS total_expense, 
            SUM(inflow) AS total_inflow 
        FROM finances
    ";
    
    $summary_data = $pdo->query($summary_query)->fetch();
    
    // Assign calculated totals if records exist, maintaining precise floating decimals
    if ($summary_data) {
        $finance_summary['total_budget']  = (float)($summary_data['total_budget'] ?? 0.00);
        $finance_summary['total_expense'] = (float)($summary_data['total_expense'] ?? 0.00);
        $finance_summary['total_inflow']  = (float)($summary_data['total_inflow'] ?? 0.00);
    }

} catch (\PDOException $e) {
    // Standard system log capture - adjust this line to match your site's global error handler
    error_log("Finance Module Exception: " . $e->getMessage());
    $database_error = "Unable to process financial records at this time.";
}