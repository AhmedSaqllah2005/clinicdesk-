<?php
class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function showLogin() {
        if (Auth::check()) {
            redirect('index.php?page=dashboard');
        }
        require 'views/auth/login.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die("Invalid Request Method");
        }

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = "Invalid security token";
            redirect('index.php?page=login');
        }

        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = "Please fill all fields";
            redirect('index.php?page=login');
        }


        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Invalid email format";
            redirect('index.php?page=login');
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['error'] = "Invalid credentials";
            redirect('index.php?page=login');
        }

        if (isset($user['is_active']) && $user['is_active'] == 0) {
            $_SESSION['error'] = "Account suspended. Contact admin.";
            redirect('index.php?page=login');
        }

        Auth::login($user);

        if ($user['role'] == 'admin') {
            redirect('index.php?page=dashboard');
        } elseif ($user['role'] == 'doctor') {
            redirect('index.php?page=doctor_dashboard');
        } else {
            redirect('index.php?page=dashboard');
        }
    }

    public function logout() {
        Auth::logout();
    }
}