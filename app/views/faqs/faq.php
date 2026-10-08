<?php
/**
 * FAQ & Categories Combined Sub-Module Controller
 * Pre-condition: Assumes an active, global database connection variable ($pdo) 
 * exists via your website's main entry shell script.
 */

// 1. Safety Guard: Block direct URL sniffing if not routed through your system core
if (!isset($pdo)) {
    header("Location: dashboard.php");
    exit;
}

// 2. Initialize container structures to prevent empty frontend loop crashes
$faq_categories  = [];
$grouped_faqs    = [];
$faq_stats       = [
    'total_categories' => 0,
    'published_faqs'   => 0,
    'draft_faqs'       => 0
];

try {
    // 3. Query A: Fetch all categories sorted by display weight
    // Quantifies how many FAQs are currently nested inside each section
    $category_query = "
        SELECT 
            fc.id AS cat_id,
            fc.name AS cat_name,
            fc.slug AS cat_slug,
            fc.display_order AS cat_order,
            COUNT(f.id) AS total_questions
        FROM faq_categories fc
        LEFT JOIN faqs f ON fc.id = f.category_id
        GROUP BY fc.id
        ORDER BY fc.display_order ASC, fc.name ASC
    ";
    $faq_categories = $pdo->query($category_query)->fetchAll();

    // 4. Query B: Fetch raw FAQ entries paired with their category contexts
    // Accounts for uncategorized elements via a LEFT JOIN mapping
    $faq_query = "
        SELECT 
            f.id AS faq_id,
            f.category_id,
            f.question,
            f.answer,
            f.is_published,
            f.display_order AS faq_order,
            fc.name AS category_name
        FROM faqs f
        LEFT JOIN faq_categories fc ON f.category_id = fc.id
        ORDER BY fc.display_order ASC, f.display_order ASC, f.id ASC
    ";
    $raw_faqs = $pdo->query($faq_query)->fetchAll();

    // 5. Array Restructuring: Pivot the raw FAQs into an easy-to-use categorized map
    foreach ($raw_faqs as $faq) {
        $cat_name = $faq['category_name'] ?? 'Uncategorized';
        $grouped_faqs[$cat_name][] = $faq;
    }

    // 6. Query C: Compile high-level dashboard metrics for overview widgets
    $stats_query = "
        SELECT 
            (SELECT COUNT(*) FROM faq_categories) AS category_total,
            COUNT(CASE WHEN is_published = 1 THEN 1 END) AS published_total,
            COUNT(CASE WHEN is_published = 0 THEN 1 END) AS draft_total
        FROM faqs
    ";
    $stats_data = $pdo->query($stats_query)->fetch();

    if ($stats_data) {
        $faq_stats['total_categories'] = (int)$stats_data['category_total'];
        $faq_stats['published_faqs']   = (int)$stats_data['published_total'];
        $faq_stats['draft_faqs']       = (int)$stats_data['draft_total'];
    }

} catch (\PDOException $e) {
    error_log("FAQ System Core Exception: " . $e->getMessage());
    $database_error = "The help center dataset failed to initialize smoothly.";
}
