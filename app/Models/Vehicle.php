<?php

namespace App\Models;

class Vehicle
{
    public function __construct(
        public int $id = 0,
        public int $customerId = 0,
        public string $plateNumber = '',
        public ?string $vin = '',
        public string $brand = '',
        public string $model = '',
        public ?int $year = null,
        public ?string $color = '',
        public ?int $mileage = 0,
        public ?string $fuelType = '',
        public ?string $transmission = '',
        public ?string $insuranceExpiry = null,
        public ?string $registrationExpiry = null
    ) {
    }
}
