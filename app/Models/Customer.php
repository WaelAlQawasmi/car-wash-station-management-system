<?php

namespace App\Models;

class Customer
{
    public function __construct(
        public int $id = 0,
        public string $fullName = '',
        public string $phone = '',
        public string $email = '',
        public int $loyaltyPoints = 0,
        public string $membershipType = 'standard'
    ) {
    }
}
