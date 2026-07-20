<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\WorkOrder;
use PDO;

class WorkOrderRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getAll(array $filters = []): array
    {
        $sql = "SELECT w.*, c.full_name AS customer_name, c.phone AS customer_phone, 
                       v.plate_number, v.brand, v.model, v.color,
                       s.name AS service_name, s.price AS service_price, s.category AS service_category,
                       u.name AS employee_name
                FROM work_orders w
                JOIN customers c ON w.customer_id = c.id
                JOIN vehicles v ON w.vehicle_id = v.id
                JOIN services s ON w.service_id = s.id
                LEFT JOIN users u ON w.assigned_employee_id = u.id
                WHERE w.deleted_at IS NULL";
        
        $params = [];
        if (!empty($filters['status'])) {
            $sql .= " AND w.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['priority'])) {
            $sql .= " AND w.priority = ?";
            $params[] = $filters['priority'];
        }

        $sql .= " ORDER BY w.id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT w.*, c.full_name AS customer_name, c.phone AS customer_phone, 
                   v.plate_number, v.brand, v.model, v.color, v.vin, v.year, v.mileage,
                   s.name AS service_name, s.price AS service_price, s.tax_rate AS service_tax,
                   u.name AS employee_name
            FROM work_orders w
            JOIN customers c ON w.customer_id = c.id
            JOIN vehicles v ON w.vehicle_id = v.id
            JOIN services s ON w.service_id = s.id
            LEFT JOIN users u ON w.assigned_employee_id = u.id
            WHERE w.id = ? AND w.deleted_at IS NULL LIMIT 1
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function save(WorkOrder $wo): bool
    {
        if ($wo->id > 0) {
            $stmt = $this->pdo->prepare("UPDATE work_orders SET customer_id = ?, vehicle_id = ?, service_id = ?, status = ?, assigned_employee_id = ?, assigned_bay = ?, priority = ?, notes = ? WHERE id = ?");
            return $stmt->execute([
                $wo->customerId,
                $wo->vehicleId,
                $wo->serviceId,
                $wo->status,
                $wo->assignedEmployeeId,
                $wo->assignedBay,
                $wo->priority,
                $wo->notes,
                $wo->id
            ]);
        } else {
            $stmt = $this->pdo->prepare("INSERT INTO work_orders (customer_id, vehicle_id, service_id, status, assigned_employee_id, assigned_bay, priority, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $wo->customerId,
                $wo->vehicleId,
                $wo->serviceId,
                $wo->status,
                $wo->assignedEmployeeId,
                $wo->assignedBay,
                $wo->priority,
                $wo->notes
            ]);
            if ($result) {
                $wo->id = (int) $this->pdo->lastInsertId();
            }
            return $result;
        }
    }

    public function updateStatus(int $id, string $status, ?int $employeeId = null, ?int $bay = null): bool
    {
        if ($employeeId !== null && $bay !== null) {
            $stmt = $this->pdo->prepare("UPDATE work_orders SET status = ?, assigned_employee_id = ?, assigned_bay = ? WHERE id = ?");
            return $stmt->execute([$status, $employeeId, $bay, $id]);
        }
        
        $stmt = $this->pdo->prepare("UPDATE work_orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function getBaysStatus(): array
    {
        // We have 5 bays. Check which ones are currently occupied (in_progress work orders)
        $stmt = $this->pdo->query("
            SELECT assigned_bay, id, status, priority, vehicle_id, customer_id, 
                   (SELECT plate_number FROM vehicles WHERE id=vehicle_id) AS plate_number
            FROM work_orders 
            WHERE status='in_progress' AND assigned_bay IS NOT NULL AND deleted_at IS NULL
        ");
        $occupied = $stmt->fetchAll();

        $bays = [];
        for ($i = 1; $i <= 5; $i++) {
            $bays[$i] = ['bay_number' => $i, 'status' => 'available', 'order' => null];
        }

        foreach ($occupied as $row) {
            $bayNum = (int) $row['assigned_bay'];
            if (isset($bays[$bayNum])) {
                $bays[$bayNum] = [
                    'bay_number' => $bayNum,
                    'status' => 'occupied',
                    'order' => [
                        'id' => (int) $row['id'],
                        'plate_number' => $row['plate_number'],
                        'priority' => $row['priority']
                    ]
                ];
            }
        }

        return $bays;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE work_orders SET deleted_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
