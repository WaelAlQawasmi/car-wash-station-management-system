<?php

namespace App\Controllers;

use App\Core\Controller;

class AuthController extends Controller
{
    public function login(): void
    {
        $this->view('auth/login');
    }

    public function authenticate(): void
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '';
        $password = $_POST['password'] ?? '';

        if ($email === 'admin@carstashen.com' && $password === 'password123') {
            $_SESSION['user'] = ['name' => 'Admin User', 'email' => $email, 'role' => 'super_admin'];
            header('Location: /dashboard');
            exit;
        }

        $_SESSION['error'] = 'Invalid credentials';
        header('Location: /login');
        exit;
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: /login');
        exit;
    }
}
