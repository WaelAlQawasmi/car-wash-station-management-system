<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\InventoryRepository;
use App\Core\Csrf;

class InventoryController extends Controller
{
    public function __construct(private readonly InventoryRepository $inventoryRepository)
    {
    }

    public function index(): void
    {
        $items = $this->inventoryRepository->getAll();
        $lowStock = $this->inventoryRepository->getLowStock();
        $movements = $this->inventoryRepository->getMovements(20);

        $this->view('inventory/index', [
            'items' => $items,
            'lowStock' => $lowStock,
            'movements' => $movements
        ]);
    }

    public function save(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Security validation failed.';
            header('Location: /inventory');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $sku = trim($_POST['sku'] ?? '');
        $barcode = trim($_POST['barcode'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $minStock = (int) ($_POST['min_stock_level'] ?? 5);
        $price = (float) ($_POST['unit_price'] ?? 0.0);
        $supplier = trim($_POST['supplier_name'] ?? '');

        if (empty($name) || empty($category)) {
            $_SESSION['error'] = 'Name and Category are required inventory fields.';
            header('Location: /inventory');
            exit;
        }

        if ($this->inventoryRepository->save($id, $name, $sku, $barcode, $category, $minStock, $price, $supplier)) {
            $_SESSION['success'] = 'Inventory item definition saved.';
        } else {
            $_SESSION['error'] = 'Failed to save inventory item.';
        }

        header('Location: /inventory');
        exit;
    }

    public function adjust(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Security validation failed.';
            header('Location: /inventory');
            exit;
        }

        $itemId = (int) ($_POST['inventory_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 0);
        $type = trim($_POST['type'] ?? 'Adjustment'); // 'Restock', 'Correction', 'Damage'

        if ($itemId === 0 || $quantity === 0) {
            $_SESSION['error'] = 'Adjustment failed: item or quantity is zero.';
            header('Location: /inventory');
            exit;
        }

        $userId = $_SESSION['user']['id'] ?? null;
        if ($this->inventoryRepository->adjustStock($itemId, $quantity, $type, $userId)) {
            $_SESSION['success'] = 'Stock adjusted successfully.';
        } else {
            $_SESSION['error'] = 'Failed to adjust stock level.';
        }

        header('Location: /inventory');
        exit;
    }
}
