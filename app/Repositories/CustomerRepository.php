<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Customer;
use App\Models\Vehicle;
use PDO;

class CustomerRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM customers WHERE deleted_at IS NULL ORDER BY id DESC");
        $results = $stmt->fetchAll();

        $customers = [];
        foreach ($results as $row) {
            $customers[] = new Customer(
                (int) $row['id'],
                $row['full_name'],
                $row['phone'],
                $row['email'] ?? '',
                (int) $row['loyalty_points'],
                $row['membership_type']
            );
        }
        return $customers;
    }

    public function search(string $query): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM customers WHERE deleted_at IS NULL AND (full_name LIKE ? OR phone LIKE ? OR email LIKE ?) ORDER BY id DESC");
        $likeQuery = "%$query%";
        $stmt->execute([$likeQuery, $likeQuery, $likeQuery]);
        $results = $stmt->fetchAll();

        $customers = [];
        foreach ($results as $row) {
            $customers[] = new Customer(
                (int) $row['id'],
                $row['full_name'],
                $row['phone'],
                $row['email'] ?? '',
                (int) $row['loyalty_points'],
                $row['membership_type']
            );
        }
        return $customers;
    }

    public function getById(int $id): ?Customer
    {
        $stmt = $this->pdo->prepare("SELECT * FROM customers WHERE id = ? AND deleted_at IS NULL LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new Customer(
            (int) $row['id'],
            $row['full_name'],
            $row['phone'],
            $row['email'] ?? '',
            (int) $row['loyalty_points'],
            $row['membership_type']
        );
    }

    public function save(Customer $customer): bool
    {
        if ($customer->id > 0) {
            $stmt = $this->pdo->prepare("UPDATE customers SET full_name = ?, phone = ?, email = ?, loyalty_points = ?, membership_type = ? WHERE id = ?");
            return $stmt->execute([
                $customer->fullName,
                $customer->phone,
                $customer->email,
                $customer->loyaltyPoints,
                $customer->membershipType,
                $customer->id
            ]);
        } else {
            $stmt = $this->pdo->prepare("INSERT INTO customers (full_name, phone, email, loyalty_points, membership_type) VALUES (?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $customer->fullName,
                $customer->phone,
                $customer->email,
                $customer->loyaltyPoints,
                $customer->membershipType
            ]);
            if ($result) {
                $customer->id = (int) $this->pdo->lastInsertId();
            }
            return $result;
        }
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE customers SET deleted_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getVehicles(int $customerId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM vehicles WHERE customer_id = ? AND deleted_at IS NULL");
        $stmt->execute([$customerId]);
        $results = $stmt->fetchAll();

        $vehicles = [];
        foreach ($results as $row) {
            $vehicles[] = new Vehicle(
                (int) $row['id'],
                (int) $row['customer_id'],
                $row['plate_number'],
                $row['vin'],
                $row['brand'],
                $row['model'],
                $row['year'] ? (int) $row['year'] : null,
                $row['color'],
                $row['mileage'] ? (int) $row['mileage'] : null,
                $row['fuel_type'],
                $row['transmission'],
                $row['insurance_expiry'],
                $row['registration_expiry']
            );
        }
        return $vehicles;
    }

    public function saveVehicle(Vehicle $vehicle): bool
    {
        if ($vehicle->id > 0) {
            $stmt = $this->pdo->prepare("UPDATE vehicles SET plate_number = ?, vin = ?, brand = ?, model = ?, year = ?, color = ?, mileage = ?, fuel_type = ?, transmission = ?, insurance_expiry = ?, registration_expiry = ? WHERE id = ?");
            return $stmt->execute([
                $vehicle->plateNumber,
                $vehicle->vin,
                $vehicle->brand,
                $vehicle->model,
                $vehicle->year,
                $vehicle->color,
                $vehicle->mileage,
                $vehicle->fuelType,
                $vehicle->transmission,
                $vehicle->insuranceExpiry,
                $vehicle->registrationExpiry,
                $vehicle->id
            ]);
        } else {
            $stmt = $this->pdo->prepare("INSERT INTO vehicles (customer_id, plate_number, vin, brand, model, year, color, mileage, fuel_type, transmission, insurance_expiry, registration_expiry) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $vehicle->customerId,
                $vehicle->plateNumber,
                $vehicle->vin,
                $vehicle->brand,
                $vehicle->model,
                $vehicle->year,
                $vehicle->color,
                $vehicle->mileage,
                $vehicle->fuelType,
                $vehicle->transmission,
                $vehicle->insuranceExpiry,
                $vehicle->registrationExpiry
            ]);
            if ($result) {
                $vehicle->id = (int) $this->pdo->lastInsertId();
            }
            return $result;
        }
    }
}
