<?php
declare(strict_types=1);

final class Event extends Model {
    protected string $table = 'events';

    public function upcoming(int $limit = 5): array {
        $stmt = $this->pdo()->prepare(
            "SELECT * FROM events WHERE start_date >= CURDATE()
             ORDER BY start_date ASC LIMIT :lim"
        );
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function publicEvents(): array {
        return $this->where('is_public', 1, 'start_date DESC');
    }

    public function highlightsForYear(int $year): array {
        $stmt = $this->pdo()->prepare(
            "SELECT * FROM events
             WHERE YEAR(start_date) = :y AND (is_highlight = 1 OR image_path IS NOT NULL)
             ORDER BY start_date ASC"
        );
        $stmt->execute(['y' => $year]);
        return $stmt->fetchAll();
    }

    public function forMonth(int $year, int $month): array {
        $stmt = $this->pdo()->prepare(
            "SELECT * FROM events
             WHERE (YEAR(start_date) = :y AND MONTH(start_date) = :m)
                OR (YEAR(end_date)   = :y AND MONTH(end_date)   = :m)
             ORDER BY start_date ASC"
        );
        $stmt->execute(['y' => $year, 'm' => $month]);
        return $stmt->fetchAll();
    }
}