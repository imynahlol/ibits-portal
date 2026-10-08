<?php
declare(strict_types=1);

final class Fine extends Model{
    protected string $table = 'fines';

    public function forUser(string $studentNo): array {
        return $this->where('student_no', $studentNo, 'issued_on DESC');
    }

    public function unpaidTotal(string $studentNo): float {
        $stmt = $this->pdo()->prepare(
            "SELECT COALESCE(SUM(amount),0) FROM fines
             WHERE student_no = :s AND status <> 'Paid'"
        );
        $stmt->execute(['s' => $studentNo]);
        return (float) $stmt->fetchColumn();
    }

    public function markPaid(int $id): bool {
        return $this->update($id, [
            'status'  => 'Paid',
            'paid_on' => date('Y-m-d'),
        ]);
    }
}