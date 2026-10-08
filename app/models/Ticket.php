<?php
declare(strict_types=1);

final class Ticket {
    public function __construct(private ?PDO $db) {}

    public function forUser(int $userId): array {
        if (!$this->db) return [];
        $stmt = $this->db->prepare('SELECT * FROM tickets WHERE owner_id = ? ORDER BY id DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
