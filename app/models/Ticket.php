<?php
declare(strict_types=1);

final class Ticket extends Model {
    protected string $table = 'tickets';

    public function forUser(string $studentNo): array {
        return $this->where('owner_no', $studentNo, 'id DESC');
    }

    public function openCount(string $studentNo): int {
        $stmt = $this->pdo()->prepare(
            "SELECT COUNT(*) FROM tickets
             WHERE owner_no = :s AND status <> 'Resolved'"
        );
        $stmt->execute(['s' => $studentNo]);
        return (int) $stmt->fetchColumn();
    }

    public function countsByStatus(): array {
        $rows = $this->pdo()->query(
            "SELECT status, COUNT(*) AS n FROM tickets GROUP BY status"
        )->fetchAll();

        $out = ['Open' => 0, 'In Progress' => 0, 'Resolved' => 0];
        foreach ($rows as $r) {
            $out[$r['status']] = (int) $r['n'];
        }
        return $out;
    }

    public function setStatus(int $id, string $status): bool {
        return $this->update($id, ['status' => $status]);
    }
}