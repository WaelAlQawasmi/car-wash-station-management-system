<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class DashboardRepository
{
    public function getSummary(): array
    {
        $pdo = Database::getConnection();

        return [
            'daily_revenue' => 12450,
            'monthly_revenue' => 378900,
            'todays_cars' => 26,
            'todays_oil_changes' => 11,
            'todays_washes' => 15,
            'pending_orders' => 9,
            'employees_working' => 7,
            'available_bays' => 4,
            'top_services' => [
                ['name' => 'Exterior Wash', 'count' => 32],
                ['name' => 'Oil Change', 'count' => 21],
                ['name' => 'Ceramic', 'count' => 17],
            ],
            'top_employees' => [
                ['name' => 'Sami Alharbi', 'score' => 98],
                ['name' => 'Mona Khaled', 'score' => 94],
                ['name' => 'Rami Hussain', 'score' => 91],
            ],
            'best_customers' => [
                ['name' => 'Ahmed Saleh', 'spent' => 8420],
                ['name' => 'Nora Alqahtani', 'spent' => 7310],
                ['name' => 'Khalid Omar', 'spent' => 6890],
            ],
        ];
    }
}
