<?php
class Event {
    private PDO $db;
    public function __construct() {
        $this->db = db();
    }

    //events and filter category: past, current, ongoing, future
    public function getAll(?string $category = null): array {
        if ($category && in_array($category, ['past','current','ongoing','future'], true)) {
            $stmt = $this->db->prepare("
                SELECT id, title, description, category, start_datetime, end_datetime, location
                FROM events
                WHERE category = ?
                ORDER BY start_datetime DESC
            ");
            $stmt->execute([$category]);
            return $stmt->fetchAll();
        }

        return $this->db->query("
            SELECT id, title, description, category, start_datetime, end_datetime, location
            FROM events
            ORDER BY start_datetime DESC
        ")->fetchAll();
    }

    public function getUpcoming(int $limit = 3): array {
        $stmt = $this->db->prepare("
            SELECT id, title, category, start_datetime, location
            FROM events
            WHERE start_datetime >= NOW()
            ORDER BY start_datetime ASC
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}