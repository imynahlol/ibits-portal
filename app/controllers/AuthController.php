<?php
// app/controllers/AuthController.php

require_once __DIR__ . '/../models/User.php';

class AuthController {
    public function showLogin(): void {
        view('auth/login');
    }

    public function showRegister(): void {
        view('auth/register');
    }

    public function register(): void {
        if (!verify_csrf($_POST['csrf_token'] ?? null)) {
            http_response_code(419);
            die('Invalid CSRF token.');
        }

        // --------------------------------------------------
        // 1. Collect input
        // --------------------------------------------------
        $accountType = $_POST['account_type'] ?? 'student'; // 'student' | 'adviser'

        $data = [
            'full_name'  => trim($_POST['full_name'] ?? ''),
            'id_number'  => trim($_POST['id_number'] ?? ''),
            'sex'        => $_POST['sex']            ?? '',
            'program'    => trim($_POST['program']    ?? ''),
            'year_level' => trim($_POST['year_level'] ?? ''),
            'password'   => $_POST['password']       ?? '',
            'role'       => $accountType === 'adviser' ? 'adviser' : 'member',
        ];

        // --------------------------------------------------
        // 2. Validate
        // --------------------------------------------------
        $errors = [];

        if ($data['full_name'] === '') {
            $errors[] = 'Full name is required.';
        }

        if ($data['id_number'] === '') {
            $errors[] = 'ID number is required.';
        } elseif ($accountType === 'student' && !is_valid_student_id($data['id_number'])) {
            $errors[] = 'Student ID must be in the format 20XX-XXXXX-LQ-0.';
        } elseif ($accountType === 'adviser' && !is_valid_adviser_id($data['id_number'])) {
            $errors[] = 'Adviser ID must be exactly 5 digits.';
        }

        if (!in_array($data['sex'], ['Female', 'Male', 'Non-Binary'], true)) {
            $errors[] = 'Please select a valid sex.';
        }

        if ($accountType === 'student') {
            if ($data['program'] === '') {
                $errors[] = 'Program is required for students.';
            }
            if ($data['year_level'] === '') {
                $errors[] = 'Year level is required for students.';
            }
        } else {
            // Advisers don't have program / year level
            $data['program']    = null;
            $data['year_level'] = null;
        }

        if (strlen($data['password']) < 6) {
            $errors[] = 'Password must be at least 6 characters.';
        }

        // --------------------------------------------------
        // 3. Uniqueness
        // --------------------------------------------------
        $userModel = new User();

        if (!$errors && $userModel->findByIdNumber($data['id_number'])) {
            $errors[] = 'That ID number is already registered.';
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = $data + ['account_type' => $accountType];
            redirect($_ENV['APP_URL'] . '/register');
        }

        // --------------------------------------------------
        // 4. Create
        // --------------------------------------------------
        $userModel->create($data);

        flash('success', 'Registration successful. Please log in.');
        redirect($_ENV['APP_URL'] . '/login');
    }

    public function login(): void {
        if (!verify_csrf($_POST['csrf_token'] ?? null)) {
            http_response_code(419);
            die('Invalid CSRF token.');
        }

        $idNumber = trim($_POST['id_number'] ?? '');
        $password = $_POST['password'] ?? '';

        $userModel = new User();
        $user = $userModel->findByIdNumber($idNumber);

        if ($user && password_verify($password, $user['password_hash'])) {
            // Prevent session fixation
            session_regenerate_id(true);

            $_SESSION['user_id']   = (int)$user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['role']      = $user['role'];

            redirect($_ENV['APP_URL'] . '/dashboard');
        }

        $_SESSION['errors'] = ['Invalid ID number or password.'];
        $_SESSION['old']    = ['id_number' => $idNumber];
        redirect($_ENV['APP_URL'] . '/login');
    }

    public function logout(): void {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
        redirect($_ENV['APP_URL'] . '/login');
    }
}