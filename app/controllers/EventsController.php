<?php
    require_once __DIR__ . '/../middleware/AuthMiddleware.php';
    require_once __DIR__ . '/../models/Event.php';

class EventsController {
    //member view calendar
    public function memberIndex(): void {
        AuthMiddleware::requireLogin();

        $category = $_GET['category'] ?? '';
        $eventModel = new Event();

        view('member/events', [
            'events'   => $eventModel->getAll($category ?: null),
            'category' => $category,
        ]);
    }
}