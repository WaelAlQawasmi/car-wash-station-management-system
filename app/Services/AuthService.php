<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\AuditRepository;

class AuthService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly AuditRepository $auditRepository
    ) {
    }

    public function isAuthenticated(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user']);
    }

    public function login(string $email, string $password): bool
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            $this->auditRepository->log(null, 'failed_login', 'users', null, ['email' => $email, 'reason' => 'user_not_found']);
            return false;
        }

        if ($user['is_active'] == 0) {
            $this->auditRepository->log($user['id'], 'failed_login', 'users', $user['id'], ['email' => $email, 'reason' => 'inactive_account']);
            return false;
        }

        if (password_verify($password, $user['password_hash'])) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['user'] = [
                'id' => (int) $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ];

            $this->userRepository->updateLastLogin((int) $user['id']);
            $this->auditRepository->log((int) $user['id'], 'login', 'users', (int) $user['id'], ['ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1']);
            return true;
        }

        $this->auditRepository->log($user['id'], 'failed_login', 'users', $user['id'], ['email' => $email, 'reason' => 'invalid_password']);
        return false;
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user'])) {
            $userId = $_SESSION['user']['id'];
            $this->auditRepository->log($userId, 'logout', 'users', $userId);
        }

        session_destroy();
    }
}
