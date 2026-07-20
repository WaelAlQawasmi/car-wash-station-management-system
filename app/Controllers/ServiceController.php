<?php

namespace App\Controllers;

use App\Core\Controller;

class ServiceController extends Controller
{
    public function index(): void
    {
        $services = [
            ['name' => 'Exterior Wash', 'price' => 35, 'duration' => 30],
            ['name' => 'Oil Change', 'price' => 80, 'duration' => 45],
            ['name' => 'Ceramic', 'price' => 120, 'duration' => 60],
        ];

        $this->view('services/index', ['services' => $services]);
    }
}
