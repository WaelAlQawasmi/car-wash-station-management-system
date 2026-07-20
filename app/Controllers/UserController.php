<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Repositories\UserRepository;
use App\Repositories\AuditRepository;

class UserController extends Controller
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly AuditRepository $auditRepository
    ) {
    }

    /** Guard: only super_admin may access */
    private function requireSuperAdmin(): void
    {
        $role = $_SESSION['user']['role'] ?? '';
        if ($role !== 'super_admin') {
            if ($this->isJsonRequest()) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Forbidden']);
                exit;
            }
            $_SESSION['error'] = 'Access denied. Super Admin only.';
            header('Location: /dashboard');
            exit;
        }
    }

    private function isJsonRequest(): bool
    {
        return str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
            || str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')
            || ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    }

    /** GET /users  — render the user management page */
    public function index(): void
    {
        $this->requireSuperAdmin();
        $users = $this->userRepository->getAllStaff();
        $this->view('users/index', ['users' => $users]);
    }

    /** GET /api/users  — JSON list of all users */
    public function apiList(): void
    {
        $this->requireSuperAdmin();
        header('Content-Type: application/json');
        $users = $this->userRepository->getAllStaff();
        // Strip sensitive data
        $safe = array_map(fn($u) => array_diff_key($u, array_flip(['password_hash', 'remember_token'])), $users);
        echo json_encode(['success' => true, 'data' => array_values($safe)]);
    }

    /** POST /users/save  — create or update user (JSON or form) */
    public function save(): void
    {
        $this->requireSuperAdmin();

        // Support both JSON body and form-post
        $input = $this->parseInput();

        if (!Csrf::validate($input['csrf_token'] ?? '')) {
            $this->respond(false, 'Security validation failed.');
            return;
        }

        $id       = isset($input['id']) && $input['id'] !== '' ? (int)$input['id'] : null;
        $name     = trim($input['name'] ?? '');
        $email    = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $role     = trim($input['role'] ?? '');
        $isActive = isset($input['is_active']) ? (int)(bool)$input['is_active'] : 1;

        $allowedRoles = ['super_admin', 'admin', 'branch_manager', 'cashier', 'employee'];

        if (empty($name) || empty($email) || empty($role)) {
            $this->respond(false, 'Name, email and role are required.');
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->respond(false, 'Invalid email address.');
            return;
        }
        if (!in_array($role, $allowedRoles, true)) {
            $this->respond(false, 'Invalid role selected.');
            return;
        }

        $currentUserId = $_SESSION['user']['id'];

        if ($id === null) {
            // Create new user
            if (empty($password) || strlen($password) < 6) {
                $this->respond(false, 'Password must be at least 6 characters.');
                return;
            }
            $ok = $this->userRepository->createStaff($name, $email, $password, $role);
            if (!$ok) {
                $this->respond(false, 'Failed to create user. Email may already be taken.');
                return;
            }
            $newId = $this->userRepository->lastInsertId();
            $this->auditRepository->log($currentUserId, 'create_user', 'users', $newId, ['name' => $name, 'role' => $role]);
            $this->respond(true, 'User created successfully.');
        } else {
            // Update existing user
            $ok = $this->userRepository->updateStaff($id, $name, $email, $role, $isActive, $password ?: null);
            if (!$ok) {
                $this->respond(false, 'Failed to update user. Email may already be taken.');
                return;
            }
            $this->auditRepository->log($currentUserId, 'update_user', 'users', $id, ['name' => $name, 'role' => $role]);
            $this->respond(true, 'User updated successfully.');
        }
    }

    /** POST /users/toggle  — toggle is_active for a user */
    public function toggle(): void
    {
        $this->requireSuperAdmin();
        $input = $this->parseInput();

        if (!Csrf::validate($input['csrf_token'] ?? '')) {
            $this->respond(false, 'Security validation failed.');
            return;
        }

        $id = (int)($input['id'] ?? 0);
        if ($id < 1) {
            $this->respond(false, 'Invalid user ID.');
            return;
        }

        $ok = $this->userRepository->toggleActive($id);
        $currentUserId = $_SESSION['user']['id'];
        $this->auditRepository->log($currentUserId, 'toggle_user_active', 'users', $id);
        $this->respond($ok, $ok ? 'User status updated.' : 'Failed to update status.');
    }

    /** POST /users/delete  — soft-delete a user */
    public function delete(): void
    {
        $this->requireSuperAdmin();
        $input = $this->parseInput();

        if (!Csrf::validate($input['csrf_token'] ?? '')) {
            $this->respond(false, 'Security validation failed.');
            return;
        }

        $id = (int)($input['id'] ?? 0);
        $currentUserId = $_SESSION['user']['id'];
        if ($id === $currentUserId) {
            $this->respond(false, 'You cannot delete your own account.');
            return;
        }

        $ok = $this->userRepository->softDelete($id);
        $this->auditRepository->log($currentUserId, 'delete_user', 'users', $id);
        $this->respond($ok, $ok ? 'User deleted.' : 'Failed to delete user.');
    }

    // ---- Helpers ----

    private function parseInput(): array
    {
        $ct = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($ct, 'application/json')) {
            $body = file_get_contents('php://input');
            return json_decode($body, true) ?? [];
        }
        return $_POST;
    }

    private function respond(bool $success, string $message): void
    {
        if ($this->isJsonRequest()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => $success, 'message' => $message]);
            exit;
        }
        $_SESSION[$success ? 'success' : 'error'] = $message;
        header('Location: /users');
        exit;
    }
}
