<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\CustomerService;

class CustomerController extends Controller
{
    public function __construct(private readonly CustomerService $customerService)
    {
    }

    public function index(): void
    {
        $customers = $this->customerService->getAll();
        $this->view('customers/index', ['customers' => $customers]);
    }
}
