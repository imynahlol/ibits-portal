<?php
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../models/Fine.php';

class FinesController {

    //member view
    public function myFines(): void {
        AuthMiddleware::requireLogin();

        $userId = (int)$_SESSION['user_id'];
        $fineModel = new Fine();

        view('member/fines', [
            'fines'   => $fineModel->getByUser($userId),
            'summary' => $fineModel->getSummaryByUser($userId),
        ]);
    }
}