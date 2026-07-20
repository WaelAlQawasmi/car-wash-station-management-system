<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class AuditRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function log(?int $userId, string $action, string $entityType, ?int $entityId, ?array $details = null): bool
    {
        $detailsJson = $details ? json_encode($details, JSON_UNESCAPED_SLASHES) : null;
        $stmt = $this->pdo->prepare("INSERT INTO audit_logs (user_id, action, entity_type, entity_id, details) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$userId, $action, $entityType, $entityId, $detailsJson]);
    }

    public function getAll(int $limit = 100): array
    {
        $stmt = $this->pdo->prepare("
            SELECT a.*, u.name AS user_name, u.role AS user_role
            FROM audit_logs a
            LEFT JOIN users u ON a.user_id = u.id
            ORDER BY a.created_at DESC LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
