<?php

namespace App\Services;

use App\Repositories\CustomerRepository;

class CustomerService
{
    public function __construct(private readonly CustomerRepository $repository)
    {
    }

    public function getAll(): array
    {
        return $this->repository->getAll();
    }
}
