<?php

namespace App\Services;

use App\Repositories\DashboardRepository;

class DashboardService
{
    public function __construct(private readonly DashboardRepository $repository)
    {
    }

    public function getSummary(): array
    {
        return $this->repository->getSummary();
    }
}
