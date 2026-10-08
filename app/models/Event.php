<?php
declare(strict_types=1);

final class Event {
    public function __construct(private ?PDO $db) {}

    public function all(): array {
        if (!$this->db) return [];
        return $this->db->query('SELECT * FROM events ORDER BY start_date, start_time')->fetchAll();
    }
}
