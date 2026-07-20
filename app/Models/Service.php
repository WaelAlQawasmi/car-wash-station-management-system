<?php

namespace App\Models;

class Service
{
    public function __construct(
        public int $id = 0,
        public string $name = '',
        public string $category = '',
        public float $price = 0.0,
        public int $durationMinutes = 30,
        public float $taxRate = 0.0,
        public float $commissionRate = 0.0,
        public bool $active = true
    ) {
    }
}
