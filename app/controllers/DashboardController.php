<?php

require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class DashboardController {
    public function index(): void {
        AuthMiddleware::requireLogin();

        view('member/dashboard', [
            'user_name' => $_SESSION['user_name'],
            'role'      => $_SESSION['role'],
        ]);
    }
}