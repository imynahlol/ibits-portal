<?php
declare(strict_types=1);

final class FineController {
    public function index(): void {
        AuthMiddleware::requireLogin();
        json_out(['ok' => true, 'fines' => (new Fine())->forUser(AuthMiddleware::id())]);
    }

    public function store(): void {
        AuthMiddleware::requireLogin();
        RoleMiddleware::require('manage_tickets');

        $input = json_input();
        $id = (new Fine())->create([
            'student_no' => (string) ($input['student_no'] ?? ''),
            'reason'     => (string) ($input['reason'] ?? ''),
            'amount'     => (float) ($input['amount'] ?? 0),
            'status'     => 'Unpaid',
            'issued_on'  => date('Y-m-d'),
        ]);

        json_out(['ok' => true, 'id' => $id], 201);
    }

    public function pay(): void {
        AuthMiddleware::requireLogin();
        RoleMiddleware::require('manage_tickets');

        $id = (int) (json_input()['id'] ?? 0);
        json_out(['ok' => (new Fine())->markPaid($id)]);
    }
}