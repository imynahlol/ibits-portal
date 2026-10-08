<?php
declare(strict_types=1);

final class Fine {
    public function __construct(private ?PDO $db) {}

    public function forUser(int $userId): array {
        if (!$this->db) return [];
        $stmt = $this->db->prepare('SELECT * FROM fines WHERE user_id = ? ORDER BY fine_date DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
