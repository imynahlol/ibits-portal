<?php
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

        $data = [
            'full_name'      => trim($_POST['full_name'] ?? ''),
            'student_number' => trim($_POST['student_number'] ?? ''),
            'sex'            => $_POST['sex'] ?? '',
            'program'        => trim($_POST['program'] ?? ''),
            'year_level'     => trim($_POST['year_level'] ?? ''),
            'password'       => $_POST['password'] ?? '',
        ];

        $errors = [];
        if ($data['full_name'] === '')      $errors[] = 'Full name is required.';
        if ($data['student_number'] === '') $errors[] = 'Student number is required.';
        if (!in_array($data['sex'], ['Male','Female','Other'], true))
            $errors[] = 'Please select a valid sex.';
        if ($data['program'] === '')        $errors[] = 'Program is required.';
        if ($data['year_level'] === '')     $errors[] = 'Year level is required.';
        if (strlen($data['password']) < 6)  $errors[] = 'Password must be at least 6 characters.';

        $userModel = new User();

        if (!$errors && $userModel->findByStudentNumber($data['student_number'])) {
            $errors[] = 'Student number is already registered.';
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = $data;
            redirect($_ENV['APP_URL'] . '/register');
        }

        $userModel->create($data);
        flash('success', 'Registration successful. Please log in.');
        redirect($_ENV['APP_URL'] . '/login');
    }

    public function login(): void {
        if (!verify_csrf($_POST['csrf_token'] ?? null)) {
            http_response_code(419);
            die('Invalid CSRF token.');
        }

        $studentNumber = trim($_POST['student_number'] ?? '');
        $password      = $_POST['password'] ?? '';

        $userModel = new User();
        $user = $userModel->findByStudentNumber($studentNumber);

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);

            $_SESSION['user_id']    = (int)$user['id'];
            $_SESSION['user_name']  = $user['full_name'];
            $_SESSION['role']       = $user['role'];

            redirect($_ENV['APP_URL'] . '/dashboard');
        }

        $_SESSION['errors'] = ['Invalid student number or password.'];
        $_SESSION['old']    = ['student_number' => $studentNumber];
        redirect($_ENV['APP_URL'] . '/login');
    }

    public function logout(): void {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']);
        }
        session_destroy();
        redirect($_ENV['APP_URL'] . '/login');
    }
}