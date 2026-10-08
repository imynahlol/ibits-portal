<?php
declare(strict_types=1);

final class ApiController {
    // Aggregated bootstrap payload for the SPA.
    public function bootstrap(): void
    {
        $users  = new User();
        $events = new Event();

        json_out([
            'ok'         => true,
            'population' => $users->population(),
            'events'     => $events->all('start_date ASC'),
            'fines'      => AuthMiddleware::user()
                ? (new Fine())->forUser(AuthMiddleware::id())
                : [],
            'tickets'    => AuthMiddleware::user()
                ? (new Ticket())->forUser(AuthMiddleware::id())
                : [],
        ]);
    }

    // AI help desk proxy. Keeps the API key server-side — the browser never sees it.
    public function ai(): void {
        $payload  = json_input();
        $messages = $payload['messages'] ?? [];

        $key = env('AI_API_KEY', '');
        if ($key === '') {
            json_out([
                'ok'    => true,
                'reply' => 'The assistant is not configured yet. Please create a ticket in the Help Desk.',
            ]);
        }

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $key,
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'model'      => 'gpt-4o-mini',
                'messages'   => array_merge([[
                    'role'    => 'system',
                    'content' => 'You are the iBITS Help Desk assistant. Answer briefly about fines, events, forms, CBL and tickets.',
                ]], $messages),
                'max_tokens' => 300,
            ]),
        ]);

        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code !== 200 || !$body) {
            log_line('ai', 'Upstream error: ' . $code);
            json_out(['ok' => false, 'error' => 'Assistant unavailable'], 502);
        }

        $json  = json_decode($body, true);
        $reply = $json['choices'][0]['message']['content'] ?? 'No reply.';

        json_out(['ok' => true, 'reply' => $reply]);
    }
}