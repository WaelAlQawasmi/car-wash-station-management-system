<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\WorkOrderRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\UserRepository;
use App\Models\WorkOrder;
use App\Core\Csrf;

class WorkOrderController extends Controller
{
    public function __construct(
        private readonly WorkOrderRepository $workOrderRepository,
        private readonly CustomerRepository $customerRepository,
        private readonly ServiceRepository $serviceRepository,
        private readonly UserRepository $userRepository
    ) {
    }

    public function index(): void
    {
        $status = $_GET['status'] ?? '';
        $priority = $_GET['priority'] ?? '';

        $orders = $this->workOrderRepository->getAll(['status' => $status, 'priority' => $priority]);
        $bays = $this->workOrderRepository->getBaysStatus();
        $employees = $this->userRepository->getAllStaff();
        $customers = $this->customerRepository->getAll();
        $services = $this->serviceRepository->getAll();

        $this->view('work_orders/index', [
            'orders' => $orders,
            'bays' => $bays,
            'employees' => $employees,
            'customers' => $customers,
            'services' => $services,
            'activeStatus' => $status,
            'activePriority' => $priority
        ]);
    }

    public function save(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Security validation failed.';
            header('Location: /work_orders');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $customerId = (int) ($_POST['customer_id'] ?? 0);
        $vehicleId = (int) ($_POST['vehicle_id'] ?? 0);
        $serviceId = (int) ($_POST['service_id'] ?? 0);
        $status = trim($_POST['status'] ?? 'waiting');
        $employeeId = $_POST['assigned_employee_id'] !== '' ? (int) $_POST['assigned_employee_id'] : null;
        $bay = $_POST['assigned_bay'] !== '' ? (int) $_POST['assigned_bay'] : null;
        $priority = trim($_POST['priority'] ?? 'normal');
        $notes = trim($_POST['notes'] ?? '');

        if ($customerId === 0 || $vehicleId === 0 || $serviceId === 0) {
            $_SESSION['error'] = 'Customer, Vehicle, and Service are required fields.';
            header('Location: /work_orders');
            exit;
        }

        $wo = new WorkOrder($id, $customerId, $vehicleId, $serviceId, $status, $employeeId, $bay, $priority, $notes);
        if ($this->workOrderRepository->save($wo)) {
            $_SESSION['success'] = 'Work order saved successfully.';
        } else {
            $_SESSION['error'] = 'Failed to save work order.';
        }

        header('Location: /work_orders');
        exit;
    }

    public function updateStatus(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Security validation failed.';
            header('Location: /work_orders');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $status = trim($_POST['status'] ?? '');
        $employeeId = $_POST['assigned_employee_id'] !== '' ? (int) $_POST['assigned_employee_id'] : null;
        $bay = $_POST['assigned_bay'] !== '' ? (int) $_POST['assigned_bay'] : null;

        if ($id === 0 || empty($status)) {
            $_SESSION['error'] = 'Invalid request parameters.';
            header('Location: /work_orders');
            exit;
        }

        if ($this->workOrderRepository->updateStatus($id, $status, $employeeId, $bay)) {
            $_SESSION['success'] = 'Work order status updated successfully.';
        } else {
            $_SESSION['error'] = 'Failed to update status.';
        }

        header('Location: /work_orders');
        exit;
    }

    public function delete(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Security validation failed.';
            header('Location: /work_orders');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($this->workOrderRepository->delete($id)) {
            $_SESSION['success'] = 'Work order deleted successfully.';
        } else {
            $_SESSION['error'] = 'Failed to delete work order.';
        }

        header('Location: /work_orders');
        exit;
    }
}
