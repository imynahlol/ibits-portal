<?php
declare(strict_types=1);

final class EventController {
    public function index(): void {
        $year  = (int) ($_GET['year']  ?? date('Y'));
        $month = (int) ($_GET['month'] ?? date('n'));

        json_out([
            'ok'         => true,
            'events'     => (new Event())->forMonth($year, $month),
            'highlights' => (new Event())->highlightsForYear($year),
        ]);
    }

    public function store(): void {
        AuthMiddleware::requireLogin();
        RoleMiddleware::require('admin_events');

        $input = json_input();
        $image = null;

        if (!empty($_FILES['image']['tmp_name'])) {
            $image = $this->storeUpload($_FILES['image']);
        }

        $id = (new Event())->create([
            'title'        => trim((string) ($input['title'] ?? '')),
            'start_date'   => (string) ($input['start'] ?? ''),
            'end_date'     => (string) ($input['end'] ?? $input['start'] ?? ''),
            'start_time'   => (string) ($input['st'] ?? '08:00'),
            'end_time'     => (string) ($input['et'] ?? '17:00'),
            'location'     => trim((string) ($input['loc'] ?? '')),
            'category'     => (string) ($input['cat'] ?? 'Academic'),
            'description'  => (string) ($input['desc'] ?? ''),
            'is_public'    => 1,
            'is_highlight' => !empty($input['hl']) ? 1 : 0,
            'image_path'   => $image,
            'created_at'   => date('Y-m-d H:i:s'),
        ]);

        json_out(['ok' => true, 'id' => $id], 201);
    }

    public function update(): void {
        AuthMiddleware::requireLogin();
        RoleMiddleware::require('admin_events');

        $input = json_input();
        $id    = (int) ($input['id'] ?? 0);

        (new Event())->update($id, [
            'title'        => trim((string) ($input['title'] ?? '')),
            'start_date'   => (string) ($input['start'] ?? ''),
            'end_date'     => (string) ($input['end'] ?? $input['start'] ?? ''),
            'start_time'   => (string) ($input['st'] ?? '08:00'),
            'end_time'     => (string) ($input['et'] ?? '17:00'),
            'location'     => trim((string) ($input['loc'] ?? '')),
            'category'     => (string) ($input['cat'] ?? 'Academic'),
            'description'  => (string) ($input['desc'] ?? ''),
            'is_highlight' => !empty($input['hl']) ? 1 : 0,
        ]);

        json_out(['ok' => true]);
    }

    public function destroy(): void {
        AuthMiddleware::requireLogin();
        RoleMiddleware::require('admin_events');

        $id = (int) (json_input()['id'] ?? 0);
        json_out(['ok' => (new Event())->delete($id)]);
    }

    private function storeUpload(array $file): ?string {
        $max = ((int) env('UPLOAD_MAX_MB', 2)) * 1024 * 1024;
        if ($file['size'] > $max) {
            json_out(['ok' => false, 'error' => 'Image too large'], 413);
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        $allow = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];

        if (!isset($allow[$mime])) {
            json_out(['ok' => false, 'error' => 'Unsupported image type'], 415);
        }

        $name = bin2hex(random_bytes(8)) . '.' . $allow[$mime];
        $dir  = dirname(__DIR__, 2) . '/public/uploads';

        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        move_uploaded_file($file['tmp_name'], $dir . '/' . $name);
        return 'uploads/' . $name;
    }
}