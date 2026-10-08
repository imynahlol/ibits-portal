<?php
declare(strict_types=1);

final class User extends Model {
    protected string $table = 'users';

    public function findByStudentNo(string $studentNo): ?array {
        return $this->findBy('student_no', $studentNo);
    }

    public function authenticate(string $studentNo, string $password): ?array {
        $user = $this->findByStudentNo($studentNo);
        if (!$user || empty($user['password_hash'])) {
            return null;
        }
        if (!password_verify($password, $user['password_hash'])) {
            return null;
        }
        unset($user['password_hash']);
        return $user;
    }

    public function register(array $data): int {
        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        unset($data['password']);
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }

    public function officers(): array {
        $sql = "SELECT * FROM users WHERE role <> 'Member' ORDER BY
                FIELD(role,'Adviser','President','Vice President','Secretary','Treasurer',
                      'Assistant Treasurer','Auditor','Assistant Auditor','Institutional')";
        return $this->pdo()->query($sql)->fetchAll();
    }

    public function population(): array {
        $pdo = $this->pdo();

        $program = $pdo->query(
            "SELECT program AS k, COUNT(*) AS n FROM users GROUP BY program"
        )->fetchAll();

        $year = $pdo->query(
            "SELECT year_level AS k, COUNT(*) AS n FROM users GROUP BY year_level"
        )->fetchAll();

        $section = $pdo->query(
            "SELECT section AS k, COUNT(*) AS n FROM users GROUP BY section"
        )->fetchAll();

        $toMap = static function (array $rows): array {
            $out = [];
            foreach ($rows as $r) {
                $out[$r['k'] ?? 'Unknown'] = (int) $r['n'];
            }
            return $out;
        };

        return [
            'program' => $toMap($program),
            'year'    => $toMap($year),
            'section' => $toMap($section),
            'officers'=> $this->count('role', 'President') + count($this->officers()) - 1,
        ];
    }
}