<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;
use App\Core\Csrf;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    public function login(): void
    {
        // Don't render inside layout dashboard, render standalone login view
        if ($this->authService->isAuthenticated()) {
            header('Location: /dashboard');
            exit;
        }

        // Just output the login template directly (no layout wrapping)
        include __DIR__ . '/../Views/auth/login.php';
    }

    public function authenticate(): void
    {
        // CSRF verification
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Csrf::validate($csrfToken)) {
            $_SESSION['error'] = 'Security check failed. Please refresh and try again.';
            header('Location: /login');
            exit;
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '';
        $password = $_POST['password'] ?? '';

        if ($this->authService->login($email, $password)) {
            header('Location: /dashboard');
            exit;
        }

        $_SESSION['error'] = 'Invalid email or password.';
        header('Location: /login');
        exit;
    }

    public function logout(): void
    {
        $this->authService->logout();
        header('Location: /login');
        exit;
    }
}
