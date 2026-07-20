<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService)
    {
    }

    public function index(): void
    {
        $summary = $this->dashboardService->getSummary();
        $this->view('dashboard/index', ['summary' => $summary]);
    }

    public function summary(): void
    {
        $this->json($this->dashboardService->getSummary());
    }
}
