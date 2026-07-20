<?php

session_start();
$_SESSION['user'] = ['id' => 1, 'name' => 'Test Admin', 'email' => 'admin@carstashen.com', 'role' => 'super_admin'];

$_SERVER['REQUEST_URI'] = '/customers';
$_SERVER['REQUEST_METHOD'] = 'GET';

ob_start();
require __DIR__ . '/../public/index.php';
$output = ob_get_clean();

if (str_contains($output, 'Customers') || str_contains($output, 'العملاء')) {
    echo "Smoke test passed!\n";
    exit(0);
} else {
    echo "Smoke test failed. Output was:\n" . substr($output, 0, 500) . "\n";
    exit(1);
}
