<?php
class PageController {
    public function home(): void {
        $pdo = db();
        $events = $pdo->query("
            SELECT id, title, description, category, start_datetime, end_datetime, location
            FROM events
            ORDER BY start_datetime DESC
            LIMIT 6
        ")->fetchAll();

        view('public/home', ['events' => $events]);
    }

    public function about(): void {
        view('public/about');
    }

    public function events(): void {
        $pdo = db();
        $category = $_GET['category'] ?? '';

        if (in_array($category, ['past','current','ongoing','future'], true)) {
            $stmt = $pdo->prepare("
                SELECT * FROM events
                WHERE category = ?
                ORDER BY start_datetime DESC
            ");
            $stmt->execute([$category]);
            $events = $stmt->fetchAll();
        } else {
            $events = $pdo->query("
                SELECT * FROM events
                ORDER BY start_datetime DESC
            ")->fetchAll();
        }

        view('public/events', ['events' => $events, 'category' => $category]);
    }

    public function faqs(): void {
        $pdo = db();
        $faqs = $pdo->query("
            SELECT * FROM faqs
            WHERE is_public = 1
            ORDER BY id ASC
        ")->fetchAll();

        view('public/faqs', ['faqs' => $faqs]);
    }
}