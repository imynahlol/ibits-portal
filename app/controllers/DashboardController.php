<?php

    require_once __DIR__ . '/../middleware/AuthMiddleware.php';
    require_once __DIR__ . '/../models/Fine.php';
    require_once __DIR__ . '/../models/Event.php';

class DashboardController {
    public function index(): void {
        AuthMiddleware::requireLogin();

        $userId = (int)$_SESSION['user_id'];

        $fineModel  = new Fine();
        $eventModel = new Event();

        $fineSummary    = $fineModel->getSummaryByUser($userId);
        $upcomingEvents = $eventModel->getUpcoming(3);

        $ticketCount = 0;
        try {
            $stmt = db()->prepare("SELECT COUNT(*) FROM tickets WHERE user_id = ? AND status != 'resolved'");
            $stmt->execute([$userId]);
            $ticketCount = (int)$stmt->fetchColumn();
        } catch (Throwable $e) {

            $ticketCount = 0;
        }

        view('member/dashboard', [
            'user_name'      => $_SESSION['user_name'],
            'role'           => $_SESSION['role'],
            'fine_summary'   => $fineSummary,
            'upcoming'       => $upcomingEvents,
            'ticket_count'   => $ticketCount,
        ]);
    }
}