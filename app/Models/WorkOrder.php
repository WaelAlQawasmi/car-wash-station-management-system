<?php

namespace App\Models;

class WorkOrder
{
    public function __construct(
        public int $id = 0,
        public int $customerId = 0,
        public int $vehicleId = 0,
        public int $serviceId = 0,
        public string $status = 'waiting',
        public ?int $assignedEmployeeId = null,
        public ?int $assignedBay = null,
        public string $priority = 'normal',
        public ?string $notes = '',
        public ?string $createdAt = null
    ) {
    }
}
