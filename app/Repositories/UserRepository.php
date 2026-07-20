<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class UserRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ? AND deleted_at IS NULL LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function updateLastLogin(int $userId): void
    {
        $stmt = $this->pdo->prepare("UPDATE users SET last_login_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$userId]);
    }

    public function getAllStaff(): array
    {
        $stmt = $this->pdo->query("SELECT id, name, email, role, is_active, last_login_at FROM users WHERE deleted_at IS NULL ORDER BY role ASC, name ASC");
        return $stmt->fetchAll();
    }

    public function createStaff(string $name, string $email, string $password, string $role): bool
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $email, $hash, $role]);
    }

    public function toggleActive(int $userId): bool
    {
        $stmt = $this->pdo->prepare("UPDATE users SET is_active = 1 - is_active WHERE id = ?");
        return $stmt->execute([$userId]);
    }
}
