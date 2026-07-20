<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Service;
use PDO;

class ServiceRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM services WHERE deleted_at IS NULL ORDER BY name ASC");
        $results = $stmt->fetchAll();

        $services = [];
        foreach ($results as $row) {
            $services[] = new Service(
                (int) $row['id'],
                $row['name'],
                $row['category'],
                (float) $row['price'],
                (int) $row['duration_minutes'],
                (float) $row['tax_rate'],
                (float) $row['commission_rate'],
                (bool) $row['active']
            );
        }
        return $services;
    }

    public function getById(int $id): ?Service
    {
        $stmt = $this->pdo->prepare("SELECT * FROM services WHERE id = ? AND deleted_at IS NULL LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new Service(
            (int) $row['id'],
            $row['name'],
            $row['category'],
            (float) $row['price'],
            (int) $row['duration_minutes'],
            (float) $row['tax_rate'],
            (float) $row['commission_rate'],
            (bool) $row['active']
        );
    }

    public function save(Service $service): bool
    {
        if ($service->id > 0) {
            $stmt = $this->pdo->prepare("UPDATE services SET name = ?, category = ?, price = ?, duration_minutes = ?, tax_rate = ?, commission_rate = ?, active = ? WHERE id = ?");
            return $stmt->execute([
                $service->name,
                $service->category,
                $service->price,
                $service->durationMinutes,
                $service->taxRate,
                $service->commissionRate,
                $service->active ? 1 : 0,
                $service->id
            ]);
        } else {
            $stmt = $this->pdo->prepare("INSERT INTO services (name, category, price, duration_minutes, tax_rate, commission_rate, active) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $service->name,
                $service->category,
                $service->price,
                $service->durationMinutes,
                $service->taxRate,
                $service->commissionRate,
                $service->active ? 1 : 0
            ]);
            if ($result) {
                $service->id = (int) $this->pdo->lastInsertId();
            }
            return $result;
        }
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE services SET deleted_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
