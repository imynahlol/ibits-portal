<?php
declare(strict_types=1);

final class TicketController {
    public function index(): void {
        AuthMiddleware::requireLogin();
        $tickets = new Ticket();

        $list = RoleMiddleware::has('manage_tickets')
            ? $tickets->all('id DESC')
            : $tickets->forUser(AuthMiddleware::id());

        json_out(['ok' => true, 'tickets' => $list, 'counts' => $tickets->countsByStatus()]);
    }

    public function store(): void {
        AuthMiddleware::requireLogin();

        $input = json_input();
        $id = (new Ticket())->create([
            'subject'    => trim((string) ($input['subject'] ?? '')),
            'category'   => (string) ($input['category'] ?? 'Other'),
            'description'=> trim((string) ($input['description'] ?? '')),
            'status'     => 'Open',
            'owner_no'   => AuthMiddleware::id(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        json_out(['ok' => true, 'id' => $id], 201);
    }

    public function status(): void {
        AuthMiddleware::requireLogin();
        RoleMiddleware::require('manage_tickets');

        $input = json_input();
        json_out(['ok' => (new Ticket())->setStatus(
            (int) ($input['id'] ?? 0),
            (string) ($input['status'] ?? 'Open')
        )]);
    }
}