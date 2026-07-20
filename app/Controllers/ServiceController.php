<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ServiceRepository;
use App\Models\Service;
use App\Core\Csrf;

class ServiceController extends Controller
{
    public function __construct(private readonly ServiceRepository $serviceRepository)
    {
    }

    public function index(): void
    {
        $services = $this->serviceRepository->getAll();
        $this->view('services/index', ['services' => $services]);
    }

    public function save(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Security validation failed.';
            header('Location: /services');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $price = (float) ($_POST['price'] ?? 0.0);
        $duration = (int) ($_POST['duration'] ?? 30);
        $tax = (float) ($_POST['tax_rate'] ?? 15.0);
        $commission = (float) ($_POST['commission_rate'] ?? 10.0);
        $active = (bool) ($_POST['active'] ?? true);

        if (empty($name) || empty($category)) {
            $_SESSION['error'] = 'Name and Category are required.';
            header('Location: /services');
            exit;
        }

        $service = new Service($id, $name, $category, $price, $duration, $tax, $commission, $active);
        if ($this->serviceRepository->save($service)) {
            $_SESSION['success'] = 'Service saved successfully.';
        } else {
            $_SESSION['error'] = 'Failed to save service.';
        }

        header('Location: /services');
        exit;
    }

    public function delete(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Security validation failed.';
            header('Location: /services');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($this->serviceRepository->delete($id)) {
            $_SESSION['success'] = 'Service deleted successfully.';
        } else {
            $_SESSION['error'] = 'Failed to delete service.';
        }

        header('Location: /services');
        exit;
    }
}
