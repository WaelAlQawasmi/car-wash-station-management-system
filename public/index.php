<?php

session_start();

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (str_starts_with($class, $prefix)) {
        $relative = substr($class, strlen($prefix));
        $path = __DIR__ . '/../app/' . str_replace('\\', '/', $relative) . '.php';
        if (is_file($path)) {
            require_once $path;
        }
    }
});

use App\Controllers\AuthController;
use App\Controllers\CustomerController;
use App\Controllers\DashboardController;
use App\Controllers\ServiceController;
use App\Core\Router;
use App\Repositories\CustomerRepository;
use App\Repositories\DashboardRepository;
use App\Services\AuthService;
use App\Services\CustomerService;
use App\Services\DashboardService;

$router = new Router();

$dashboardService = new DashboardService(new DashboardRepository());
$authService = new AuthService();
$customerService = new CustomerService(new CustomerRepository());

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

if (!isset($_SESSION['user']) && !in_array($uri, ['/login', '/logout'], true)) {
    header('Location: /login');
    exit;
}

$router->add('GET', '/login', [AuthController::class, 'login']);
$router->add('POST', '/login', [AuthController::class, 'authenticate']);
$router->add('GET', '/logout', [AuthController::class, 'logout']);
$router->add('GET', '/dashboard', [DashboardController::class, 'index']);
$router->add('GET', '/', [DashboardController::class, 'index']);
$router->add('GET', '/api/dashboard/summary', [DashboardController::class, 'summary']);
$router->add('GET', '/customers', [CustomerController::class, 'index']);
$router->add('GET', '/services', [ServiceController::class, 'index']);

$router->dispatch($_SERVER['REQUEST_URI'] ?? '/');
