<?php
declare(strict_types=1);

final class MemberController {
    public function dashboard(): void {
        AuthMiddleware::requireLogin();
        $user = AuthMiddleware::user();

        json_out([
            'ok'      => true,
            'fines'   => (new Fine())->unpaidTotal($user['id']),
            'events'  => (new Event())->upcoming(3),
            'tickets' => (new Ticket())->openCount($user['id']),
        ]);
    }

    public function fines(): void {
        AuthMiddleware::requireLogin();
        json_out(['ok' => true, 'fines' => (new Fine())->forUser(AuthMiddleware::id())]);
    }

    public function settings(): void {
        AuthMiddleware::requireLogin();
        json_out(['ok' => true, 'user' => AuthMiddleware::user()]);
    }
}