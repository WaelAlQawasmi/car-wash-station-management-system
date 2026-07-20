<?php

use PHPUnit\Framework\TestCase;

class DashboardTest extends TestCase
{
    public function testDashboardRepositorySummaryContainsExpectedKeys(): void
    {
        $repository = new App\Repositories\DashboardRepository();
        $summary = $repository->getSummary();

        $this->assertArrayHasKey('daily_revenue', $summary);
        $this->assertArrayHasKey('monthly_revenue', $summary);
        $this->assertArrayHasKey('top_services', $summary);
    }
}
