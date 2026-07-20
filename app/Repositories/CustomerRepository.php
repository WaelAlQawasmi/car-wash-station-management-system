<?php

namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository
{
    public function getAll(): array
    {
        return [
            new Customer(1, 'Ahmed Saleh', '+966500111111', 'ahmed@example.com', 320, 'gold'),
            new Customer(2, 'Nora Alqahtani', '+966500222222', 'nora@example.com', 185, 'silver'),
            new Customer(3, 'Khalid Omar', '+966500333333', 'khalid@example.com', 420, 'platinum'),
        ];
    }
}
