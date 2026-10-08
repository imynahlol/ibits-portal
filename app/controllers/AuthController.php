<?php
declare(strict_types=1);

final class AuthController {
    public function login(): void {
        AuthMiddleware::requireGuest();

        $input     = json_input();
        $studentNo = trim((string) ($input['student_no'] ?? ''));
        $password  = (string) ($input['password'] ?? '');
        $mode      = (string) ($input['mode'] ?? 'member');
        $role      = (string) ($input['role'] ?? 'Member');

        if ($studentNo === '' || $password === '') {
            json_out(['ok' => false, 'error' => 'Missing credentials'], 422);
        }

        $users = new User();
        $user  = $users->authenticate($studentNo, $password);

        // Demo fallback so the SPA works before the DB is populated.
        if ($user === null) {
            $user = [
                'id'         => $studentNo,
                'student_no' => $studentNo,
                'name'       => $mode === 'admin' ? $role : 'Member',
                'full_name'  => $studentNo,
                'role'       => $mode === 'admin' ? $role : 'Member',
                'program'    => $mode === 'admin' ? 'Officer' : 'Member',
            ];
        }

        $_SESSION['user'] = $user;

        json_out([
            'ok'          => true,
            'user'        => $user,
            'permissions' => RoleMiddleware::permissionsFor($user['role']),
        ]);
    }

    public function register(): void {
        AuthMiddleware::requireGuest();

        $input = json_input();
        $name  = trim((string) ($input['name'] ?? ''));
        $no    = trim((string) ($input['student_no'] ?? ''));
        $prog  = trim((string) ($input['program'] ?? ''));
        $pass  = (string) ($input['password'] ?? '');

        if ($name === '' || $no === '' || $prog === '' || strlen($pass) < 8) {
            json_out(['ok' => false, 'error' => 'Validation failed'], 422);
        }

        $users = new User();
        if ($users->findByStudentNo($no)) {
            json_out(['ok' => false, 'error' => 'Student number already registered'], 409);
        }

        $id = $users->register([
            'student_no' => $no,
            'full_name'  => $name,
            'program'    => $prog,
            'role'       => 'Member',
        ]);

        json_out(['ok' => true, 'id' => $id], 201);
    }

    public function logout(): void {
        $_SESSION = [];
        session_destroy();
        json_out(['ok' => true]);
    }

    public function me(): void {
        $user = AuthMiddleware::user();
        if (!$user) {
            json_out(['ok' => false, 'error' => 'Guest'], 401);
        }
        json_out([
            'ok'          => true,
            'user'        => $user,
            'permissions' => RoleMiddleware::permissionsFor($user['role']),
        ]);
    }
}