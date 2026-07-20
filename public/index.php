<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Autoload global helpers (translation functions)
require_once __DIR__ . '/../app/Core/helpers.php';

// Load .env variables
loadEnv(__DIR__ . '/../.env');

// 2. Register PSR-4 Autoloader
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
use App\Controllers\WorkOrderController;
use App\Controllers\POSController;
use App\Controllers\InventoryController;
use App\Controllers\SettingsController;
use App\Core\Container;
use App\Core\Router;

// 3. Initialize Dependency Injection Container & Router
$container = new Container();
$router = new Router($container);

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

// 4. Authentication check
if (!isset($_SESSION['user']) && !in_array($uri, ['/login', '/logout'], true)) {
    header('Location: /login');
    exit;
}

// 5. Register Routes

// Auth
$router->add('GET', '/login', [AuthController::class, 'login']);
$router->add('POST', '/login', [AuthController::class, 'authenticate']);
$router->add('GET', '/logout', [AuthController::class, 'logout']);

// Dashboard
$router->add('GET', '/dashboard', [DashboardController::class, 'index']);
$router->add('GET', '/', [DashboardController::class, 'index']);
$router->add('GET', '/api/dashboard/summary', [DashboardController::class, 'summary']);

// Customers
$router->add('GET', '/customers', [CustomerController::class, 'index']);
$router->add('POST', '/customers/save', [CustomerController::class, 'save']);
$router->add('POST', '/customers/delete', [CustomerController::class, 'delete']);
$router->add('GET', '/customers/vehicles', [CustomerController::class, 'vehicles']);
$router->add('POST', '/customers/vehicles/save', [CustomerController::class, 'saveVehicle']);

// Services
$router->add('GET', '/services', [ServiceController::class, 'index']);
$router->add('POST', '/services/save', [ServiceController::class, 'save']);
$router->add('POST', '/services/delete', [ServiceController::class, 'delete']);

// Work Orders & Queue
$router->add('GET', '/work_orders', [WorkOrderController::class, 'index']);
$router->add('POST', '/work_orders/save', [WorkOrderController::class, 'save']);
$router->add('POST', '/work_orders/update_status', [WorkOrderController::class, 'updateStatus']);
$router->add('POST', '/work_orders/delete', [WorkOrderController::class, 'delete']);

// POS / Invoicing
$router->add('GET', '/pos', [POSController::class, 'index']);
$router->add('POST', '/pos/checkout', [POSController::class, 'checkout']);

// Inventory
$router->add('GET', '/inventory', [InventoryController::class, 'index']);
$router->add('POST', '/inventory/save', [InventoryController::class, 'save']);
$router->add('POST', '/inventory/adjust', [InventoryController::class, 'adjust']);

// Settings / Utilities
$router->add('GET', '/settings', [SettingsController::class, 'index']);
$router->add('GET', '/settings/set_lang', [SettingsController::class, 'setLanguage']);
$router->add('POST', '/settings/backup', [SettingsController::class, 'backup']);
$router->add('POST', '/settings/restore', [SettingsController::class, 'restore']);

// 6. Dispatch Request
$router->dispatch($_SERVER['REQUEST_URI'] ?? '/');
