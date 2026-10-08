<?php
declare(strict_types=1);

final class Amendment extends Model {
    protected string $table = 'amendments';

    //ordered approval chain for a CBL amendment.
    public const CHAIN = ['Secretary', 'Adviser', 'President', 'Vice President'];

    public function pending(): array {
        return $this->where('status', 'Pending', 'id DESC');
    }

    public function stageIndex(array $amendment): int {
        return array_search($amendment['current_stage'] ?? 'Secretary', self::CHAIN, true) ?: 0;
    }

    public function approve(int $id, string $approverRole): bool {
        $amendment = $this->find($id);
        if (!$amendment) {
            return false;
        }

        $index = array_search($approverRole, self::CHAIN, true);
        if ($index === false) {
            return false;
        }

        $next = self::CHAIN[$index + 1] ?? null;

        return $this->update($id, [
            'current_stage' => $next ?? 'Published',
            'status'        => $next === null ? 'Published' : 'Pending',
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    public function reject(int $id): bool {
        return $this->update($id, [
            'status'     => 'Rejected',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}