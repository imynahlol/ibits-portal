<?php
declare(strict_types=1);

final class AdminController {
    public function org(): void {
        AuthMiddleware::requireLogin();
        RoleMiddleware::require('admin_org');
        json_out(['ok' => true, 'officers' => (new User())->officers()]);
    }

    public function cbl(): void {
        AuthMiddleware::requireLogin();
        RoleMiddleware::require('admin_cbl');
        json_out(['ok' => true, 'pending' => (new Amendment())->pending()]);
    }

    public function approveAmendment(): void {
        AuthMiddleware::requireLogin();
        RoleMiddleware::require('admin_cbl');

        $input = json_input();
        $id    = (int) ($input['id'] ?? 0);
        $role  = AuthMiddleware::user()['role'] ?? '';

        json_out(['ok' => (new Amendment())->approve($id, $role)]);
    }

    public function rejectAmendment(): void {
        AuthMiddleware::requireLogin();
        RoleMiddleware::require('admin_cbl');

        $id = (int) (json_input()['id'] ?? 0);
        json_out(['ok' => (new Amendment())->reject($id)]);
    }
}