<?php

namespace App\Services;

use App\Repositories\CustomerRepository;
use App\Models\Customer;
use App\Models\Vehicle;

class CustomerService
{
    public function __construct(private readonly CustomerRepository $repository)
    {
    }

    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    public function search(string $query): array
    {
        if (empty($query)) {
            return $this->getAll();
        }
        return $this->repository->search($query);
    }

    public function getById(int $id): ?Customer
    {
        return $this->repository->getById($id);
    }

    public function save(Customer $customer): bool
    {
        return $this->repository->save($customer);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getVehicles(int $customerId): array
    {
        return $this->repository->getVehicles($customerId);
    }

    public function saveVehicle(Vehicle $vehicle): bool
    {
        return $this->repository->saveVehicle($vehicle);
    }
}
