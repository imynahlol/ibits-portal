<?php
class Fine {
    private PDO $db;

    public function __construct() {
        $this->db = db();
    }

    //fines for a specific user
    public function getByUser(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT id, amount, reason, status, due_date, created_at
            FROM fines
            WHERE user_id = ?
            ORDER BY status ASC, due_date DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    //gets summary of fines for a specific user, dbquery returns total amount, unpaid amount, total count, and unpaid count
    public function getSummaryByUser(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT
                COALESCE(SUM(amount), 0)                              AS total,
                COALESCE(SUM(CASE WHEN status='unpaid' THEN amount END), 0) AS unpaid,
                COUNT(*)                                              AS count,
                SUM(CASE WHEN status='unpaid' THEN 1 ELSE 0 END)      AS unpaid_count
            FROM fines
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();

        return [
            'total'        => (float)($row['total'] ?? 0),
            'unpaid'       => (float)($row['unpaid'] ?? 0),
            'count'        => (int)($row['count'] ?? 0),
            'unpaid_count' => (int)($row['unpaid_count'] ?? 0),
        ];
    }
}