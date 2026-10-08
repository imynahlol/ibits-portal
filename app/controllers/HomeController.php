<?php
declare(strict_types=1);

final class HomeController {
    public function index(): void {
        $events = (new Event())->publicEvents();
        view('layouts.app', [
            'title'       => 'iBITS Portal | We Code as One',
            'currentView' => 'public-home',
            'events'      => $events,
        ]);
    }
}