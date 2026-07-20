<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class InventoryRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM inventory ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function getLowStock(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM inventory WHERE stock_quantity <= min_stock_level ORDER BY stock_quantity ASC");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM inventory WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByBarcode(string $barcode): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM inventory WHERE barcode = ? LIMIT 1");
        $stmt->execute([$barcode]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function save(int $id, string $name, string $sku, string $barcode, string $category, int $minStock, float $price, string $supplier): bool
    {
        if ($id > 0) {
            $stmt = $this->pdo->prepare("UPDATE inventory SET name = ?, sku = ?, barcode = ?, category = ?, min_stock_level = ?, unit_price = ?, supplier_name = ? WHERE id = ?");
            return $stmt->execute([$name, $sku, $barcode, $category, $minStock, $price, $supplier, $id]);
        } else {
            $stmt = $this->pdo->prepare("INSERT INTO inventory (name, sku, barcode, category, min_stock_level, unit_price, supplier_name, stock_quantity) VALUES (?, ?, ?, ?, ?, ?, ?, 0)");
            return $stmt->execute([$name, $sku, $barcode, $category, $minStock, $price, $supplier]);
        }
    }

    public function adjustStock(int $itemId, int $change, string $type, ?int $userId): bool
    {
        $this->pdo->beginTransaction();
        try {
            // Update stock
            $stmt = $this->pdo->prepare("UPDATE inventory SET stock_quantity = stock_quantity + ? WHERE id = ?");
            $stmt->execute([$change, $itemId]);

            // Log movement
            $stmtLog = $this->pdo->prepare("INSERT INTO stock_movements (inventory_id, quantity_changed, type, user_id) VALUES (?, ?, ?, ?)");
            $stmtLog->execute([$itemId, $change, $type, $userId]);

            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function getMovements(int $limit = 50): array
    {
        $stmt = $this->pdo->prepare("
            SELECT m.*, i.name AS item_name, i.category AS item_category, u.name AS user_name
            FROM stock_movements m
            JOIN inventory i ON m.inventory_id = i.id
            LEFT JOIN users u ON m.user_id = u.id
            ORDER BY m.created_at DESC LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
