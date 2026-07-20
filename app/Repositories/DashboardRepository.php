<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class DashboardRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getSummary(): array
    {
        // 1. Daily revenue (completed/delivered today)
        $stmt = $this->pdo->query("
            SELECT COALESCE(SUM(s.price), 0) 
            FROM work_orders w
            JOIN services s ON w.service_id = s.id
            WHERE w.status IN ('completed', 'delivered') 
              AND DATE(w.updated_at) = CURRENT_DATE() 
              AND w.deleted_at IS NULL
        ");
        $dailyRevenue = (float) $stmt->fetchColumn();

        // 2. Monthly revenue (completed/delivered this month)
        $stmt = $this->pdo->query("
            SELECT COALESCE(SUM(s.price), 0) 
            FROM work_orders w
            JOIN services s ON w.service_id = s.id
            WHERE w.status IN ('completed', 'delivered') 
              AND MONTH(w.updated_at) = MONTH(CURRENT_DATE()) 
              AND YEAR(w.updated_at) = YEAR(CURRENT_DATE())
              AND w.deleted_at IS NULL
        ");
        $monthlyRevenue = (float) $stmt->fetchColumn();

        // 3. Today's cars (distinct vehicles in queue/workorders today)
        $stmt = $this->pdo->query("
            SELECT COUNT(DISTINCT vehicle_id) 
            FROM work_orders 
            WHERE DATE(created_at) = CURRENT_DATE() 
              AND deleted_at IS NULL
        ");
        $todaysCars = (int) $stmt->fetchColumn();

        // 4. Today's washes (category = 'wash')
        $stmt = $this->pdo->query("
            SELECT COUNT(*) 
            FROM work_orders w
            JOIN services s ON w.service_id = s.id
            WHERE s.category = 'wash' 
              AND DATE(w.created_at) = CURRENT_DATE() 
              AND w.deleted_at IS NULL
        ");
        $todaysWashes = (int) $stmt->fetchColumn();

        // 5. Today's oil changes (category = 'oil')
        $stmt = $this->pdo->query("
            SELECT COUNT(*) 
            FROM work_orders w
            JOIN services s ON w.service_id = s.id
            WHERE s.category = 'oil' 
              AND DATE(w.created_at) = CURRENT_DATE() 
              AND w.deleted_at IS NULL
        ");
        $todaysOilChanges = (int) $stmt->fetchColumn();

        // 6. Pending orders (waiting or in_progress)
        $stmt = $this->pdo->query("
            SELECT COUNT(*) 
            FROM work_orders 
            WHERE status IN ('waiting', 'in_progress') 
              AND deleted_at IS NULL
        ");
        $pendingOrders = (int) $stmt->fetchColumn();

        // 7. Employees working (active users with role 'employee' or 'supervisor' or assigned to a pending job today)
        $stmt = $this->pdo->query("
            SELECT COUNT(*) 
            FROM users 
            WHERE role IN ('employee', 'supervisor') 
              AND is_active = 1 
              AND deleted_at IS NULL
        ");
        $employeesWorking = (int) $stmt->fetchColumn();

        // 8. Available Bays (5 total bays, minus the ones currently occupied by in_progress work_orders)
        $stmt = $this->pdo->query("
            SELECT COUNT(DISTINCT assigned_bay) 
            FROM work_orders 
            WHERE status = 'in_progress' 
              AND assigned_bay IS NOT NULL 
              AND deleted_at IS NULL
        ");
        $occupiedBays = (int) $stmt->fetchColumn();
        $availableBays = max(0, 5 - $occupiedBays);

        // 9. Top Services
        $stmt = $this->pdo->query("
            SELECT s.name, COUNT(*) AS count 
            FROM work_orders w
            JOIN services s ON w.service_id = s.id
            WHERE w.deleted_at IS NULL
            GROUP BY s.id
            ORDER BY count DESC 
            LIMIT 3
        ");
        $topServices = $stmt->fetchAll() ?: [];

        // 10. Top Employees (score calculated by number of completed/delivered jobs)
        $stmt = $this->pdo->query("
            SELECT u.name, COUNT(w.id) * 10 AS score
            FROM work_orders w
            JOIN users u ON w.assigned_employee_id = u.id
            WHERE w.status IN ('completed', 'delivered') 
              AND w.deleted_at IS NULL
            GROUP BY u.id
            ORDER BY score DESC 
            LIMIT 3
        ");
        $topEmployees = $stmt->fetchAll() ?: [];
        // Add fake score percentage display helpers if no items found
        if (empty($topEmployees)) {
            $topEmployees = [
                ['name' => 'No active worker data', 'score' => 0]
            ];
        }

        // 11. Best Customers (total spent)
        $stmt = $this->pdo->query("
            SELECT c.full_name AS name, SUM(s.price) AS spent 
            FROM work_orders w
            JOIN customers c ON w.customer_id = c.id
            JOIN services s ON w.service_id = s.id
            WHERE w.status IN ('completed', 'delivered') 
              AND w.deleted_at IS NULL
            GROUP BY c.id
            ORDER BY spent DESC 
            LIMIT 3
        ");
        $bestCustomers = $stmt->fetchAll() ?: [];

        // 12. Recent Activities (latest audit logs)
        $stmt = $this->pdo->query("
            SELECT a.*, u.name AS user_name 
            FROM audit_logs a
            LEFT JOIN users u ON a.user_id = u.id
            ORDER BY a.created_at DESC 
            LIMIT 5
        ");
        $recentActivities = $stmt->fetchAll() ?: [];

        return [
            'daily_revenue' => $dailyRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'todays_cars' => $todaysCars,
            'todays_washes' => $todaysWashes,
            'todays_oil_changes' => $todaysOilChanges,
            'pending_orders' => $pendingOrders,
            'employees_working' => $employeesWorking,
            'available_bays' => $availableBays,
            'top_services' => $topServices,
            'top_employees' => $topEmployees,
            'best_customers' => $bestCustomers,
            'recent_activities' => $recentActivities
        ];
    }
}
