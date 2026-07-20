<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\CustomerService;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Core\Csrf;

class CustomerController extends Controller
{
    public function __construct(private readonly CustomerService $customerService)
    {
    }

    public function index(): void
    {
        $searchQuery = filter_input(INPUT_GET, 'search', FILTER_DEFAULT) ?? '';
        $customers = $this->customerService->search($searchQuery);
        
        $this->view('customers/index', [
            'customers' => $customers,
            'searchQuery' => $searchQuery
        ]);
    }

    public function save(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Security validation failed.';
            header('Location: /customers');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $points = (int) ($_POST['loyalty_points'] ?? 0);
        $membership = trim($_POST['membership_type'] ?? 'standard');

        if (empty($name) || empty($phone)) {
            $_SESSION['error'] = 'Name and Phone fields are required.';
            header('Location: /customers');
            exit;
        }

        $customer = new Customer($id, $name, $phone, $email, $points, $membership);
        if ($this->customerService->save($customer)) {
            $_SESSION['success'] = 'Customer saved successfully.';
        } else {
            $_SESSION['error'] = 'Failed to save customer.';
        }

        header('Location: /customers');
        exit;
    }

    public function delete(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Security validation failed.';
            header('Location: /customers');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($this->customerService->delete($id)) {
            $_SESSION['success'] = 'Customer deleted successfully.';
        } else {
            $_SESSION['error'] = 'Failed to delete customer.';
        }

        header('Location: /customers');
        exit;
    }

    public function vehicles(): void
    {
        $customerId = (int) ($_GET['customer_id'] ?? 0);
        $vehicles = $this->customerService->getVehicles($customerId);
        
        $data = [];
        foreach ($vehicles as $v) {
            $data[] = [
                'id' => $v->id,
                'plate_number' => $v->plateNumber,
                'brand' => $v->brand,
                'model' => $v->model,
                'year' => $v->year,
                'color' => $v->color,
                'vin' => $v->vin,
                'mileage' => $v->mileage,
                'fuel_type' => $v->fuelType,
                'transmission' => $v->transmission,
                'insurance_expiry' => $v->insuranceExpiry,
                'registration_expiry' => $v->registrationExpiry
            ];
        }
        
        $this->json($data);
    }

    public function saveVehicle(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Security validation failed.';
            header('Location: /customers');
            exit;
        }

        $customerId = (int) ($_POST['customer_id'] ?? 0);
        $id = (int) ($_POST['vehicle_id'] ?? 0);
        $plateNumber = trim($_POST['plate_number'] ?? '');
        $brand = trim($_POST['brand'] ?? '');
        $model = trim($_POST['model'] ?? '');
        $year = $_POST['year'] !== '' ? (int) $_POST['year'] : null;
        $color = trim($_POST['color'] ?? '');
        $vin = trim($_POST['vin'] ?? '');
        $mileage = $_POST['mileage'] !== '' ? (int) $_POST['mileage'] : null;
        $fuelType = trim($_POST['fuel_type'] ?? '');
        $transmission = trim($_POST['transmission'] ?? '');
        $insExpiry = $_POST['insurance_expiry'] ?: null;
        $regExpiry = $_POST['registration_expiry'] ?: null;

        if (empty($plateNumber) || empty($brand) || empty($model)) {
            $_SESSION['error'] = 'Plate, brand, and model are required for vehicle.';
            header('Location: /customers');
            exit;
        }

        $vehicle = new Vehicle(
            $id,
            $customerId,
            $plateNumber,
            $vin,
            $brand,
            $model,
            $year,
            $color,
            $mileage,
            $fuelType,
            $transmission,
            $insExpiry,
            $regExpiry
        );

        if ($this->customerService->saveVehicle($vehicle)) {
            $_SESSION['success'] = 'Vehicle saved successfully.';
        } else {
            $_SESSION['error'] = 'Failed to save vehicle.';
        }

        header('Location: /customers');
        exit;
    }
}
