<?php

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

try {
    $pdo = Database::getConnection();
    echo "Connected to database. Starting seeding...\n";

    // 1. Seed Users (passwords: 'password123')
    $passHash = password_hash('password123', PASSWORD_DEFAULT);
    
    $users = [
        ['Admin User', 'admin@carstashen.com', $passHash, 'super_admin'],
        ['Branch Manager', 'manager@carstashen.com', $passHash, 'branch_manager'],
        ['Cashier Staff', 'cashier@carstashen.com', $passHash, 'cashier'],
        ['Reception Staff', 'reception@carstashen.com', $passHash, 'reception'],
        ['Supervisor Staff', 'supervisor@carstashen.com', $passHash, 'supervisor'],
        ['Car Wash Employee', 'employee@carstashen.com', $passHash, 'employee'],
        ['Oil Technician', 'oiltech@carstashen.com', $passHash, 'employee'],
    ];

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
    foreach ($users as $user) {
        $stmt->execute($user);
    }
    echo "Users seeded.\n";

    // Get user IDs
    $adminId = $pdo->query("SELECT id FROM users WHERE role='super_admin' LIMIT 1")->fetchColumn();
    $employeeId = $pdo->query("SELECT id FROM users WHERE email='employee@carstashen.com' LIMIT 1")->fetchColumn();
    $oilTechId = $pdo->query("SELECT id FROM users WHERE email='oiltech@carstashen.com' LIMIT 1")->fetchColumn();

    // 2. Seed Branches
    $branches = [
        ['Riyadh Main Branch', 'Olaya Street, Riyadh', $adminId],
        ['Jeddah North Branch', 'King Road, Jeddah', $adminId],
        ['Dammam Central Branch', 'Khobar Road, Dammam', $adminId],
    ];
    $stmt = $pdo->prepare("INSERT INTO branches (name, location, manager_id) VALUES (?, ?, ?)");
    foreach ($branches as $branch) {
        $stmt->execute($branch);
    }
    echo "Branches seeded.\n";

    $branchId = $pdo->query("SELECT id FROM branches LIMIT 1")->fetchColumn();

    // 3. Seed Customers
    $customers = [
        ['Ahmed Saleh', '+966500111111', 'ahmed@example.com', 320, 'gold', 'Regular VIP customer', $branchId],
        ['Nora Alqahtani', '+966500222222', 'nora@example.com', 185, 'silver', 'Prefers environment-friendly shampoo', $branchId],
        ['Khalid Omar', '+966500333333', 'khalid@example.com', 420, 'platinum', 'Loyal customer since 2024', $branchId],
        ['Fatima Al-Amri', '+966500444444', 'fatima@example.com', 50, 'standard', 'New customer', $branchId],
        ['Saeed Al-Ghamdi', '+966500555555', 'saeed@example.com', 80, 'standard', null, $branchId],
    ];
    $stmt = $pdo->prepare("INSERT INTO customers (full_name, phone, email, loyalty_points, membership_type, notes, branch_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($customers as $customer) {
        $stmt->execute($customer);
    }
    echo "Customers seeded.\n";

    // Get Customer IDs
    $ahmedId = $pdo->query("SELECT id FROM customers WHERE full_name='Ahmed Saleh' LIMIT 1")->fetchColumn();
    $noraId = $pdo->query("SELECT id FROM customers WHERE full_name='Nora Alqahtani' LIMIT 1")->fetchColumn();
    $khalidId = $pdo->query("SELECT id FROM customers WHERE full_name='Khalid Omar' LIMIT 1")->fetchColumn();
    $fatimaId = $pdo->query("SELECT id FROM customers WHERE full_name='Fatima Al-Amri' LIMIT 1")->fetchColumn();

    // 4. Seed Vehicles
    $vehicles = [
        [$ahmedId, 'AAA 1111', 'VIN9876543210ABC', 'Toyota', 'Land Cruiser', 2022, 'White', 45000, 'petrol', 'automatic', '2027-01-01', '2027-01-01'],
        [$noraId, 'BBB 2222', 'VIN1234567890XYZ', 'Lexus', 'RX 350', 2023, 'Silver', 25000, 'hybrid', 'automatic', '2026-12-15', '2026-12-15'],
        [$khalidId, 'CCC 3333', 'VIN555566667777', 'Mercedes', 'S-Class', 2024, 'Black', 12000, 'petrol', 'automatic', '2027-05-20', '2027-05-20'],
        [$fatimaId, 'DDD 4444', 'VIN111222333444', 'Hyundai', 'Tucson', 2021, 'Red', 78000, 'petrol', 'automatic', '2026-10-10', '2026-10-10'],
    ];
    $stmt = $pdo->prepare("INSERT INTO vehicles (customer_id, plate_number, vin, brand, model, year, color, mileage, fuel_type, transmission, insurance_expiry, registration_expiry) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($vehicles as $vehicle) {
        $stmt->execute($vehicle);
    }
    echo "Vehicles seeded.\n";

    // Get Vehicle IDs
    $ahmedVehicleId = $pdo->query("SELECT id FROM vehicles WHERE customer_id=$ahmedId LIMIT 1")->fetchColumn();
    $noraVehicleId = $pdo->query("SELECT id FROM vehicles WHERE customer_id=$noraId LIMIT 1")->fetchColumn();
    $khalidVehicleId = $pdo->query("SELECT id FROM vehicles WHERE customer_id=$khalidId LIMIT 1")->fetchColumn();
    $fatimaVehicleId = $pdo->query("SELECT id FROM vehicles WHERE customer_id=$fatimaId LIMIT 1")->fetchColumn();

    // 5. Seed Services
    $services = [
        ['Exterior Wash', 'wash', 35.00, 20, 15.00, 10.00, 1],
        ['Interior Wash', 'wash', 25.00, 15, 15.00, 10.00, 1],
        ['VIP Wash', 'wash', 75.00, 40, 15.00, 10.00, 1],
        ['Steam Wash', 'wash', 50.00, 30, 15.00, 10.00, 1],
        ['Oil Change', 'oil', 120.00, 30, 15.00, 15.00, 1],
        ['Filter Change', 'oil', 40.00, 15, 15.00, 15.00, 1],
        ['Polish & Wax', 'detail', 150.00, 60, 15.00, 20.00, 1],
        ['Ceramic Coating', 'detail', 450.00, 180, 15.00, 25.00, 1],
        ['AC Service', 'oil', 180.00, 45, 15.00, 15.00, 1],
    ];
    $stmt = $pdo->prepare("INSERT INTO services (name, category, price, duration_minutes, tax_rate, commission_rate, active) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($services as $service) {
        $stmt->execute($service);
    }
    echo "Services seeded.\n";

    // Get Service IDs
    $exteriorWashId = $pdo->query("SELECT id FROM services WHERE name='Exterior Wash' LIMIT 1")->fetchColumn();
    $vipWashId = $pdo->query("SELECT id FROM services WHERE name='VIP Wash' LIMIT 1")->fetchColumn();
    $oilChangeId = $pdo->query("SELECT id FROM services WHERE name='Oil Change' LIMIT 1")->fetchColumn();
    $ceramicId = $pdo->query("SELECT id FROM services WHERE name='Ceramic Coating' LIMIT 1")->fetchColumn();
    $acServiceId = $pdo->query("SELECT id FROM services WHERE name='AC Service' LIMIT 1")->fetchColumn();

    // 6. Seed Work Orders
    // We want a few completed ones for revenue chart
    // Completed work orders (some today, some yesterday, some earlier in the week)
    $today = date('Y-m-d H:i:s');
    $yesterday = date('Y-m-d H:i:s', strtotime('-1 day'));
    $twoDaysAgo = date('Y-m-d H:i:s', strtotime('-2 days'));
    $threeDaysAgo = date('Y-m-d H:i:s', strtotime('-3 days'));
    $fourDaysAgo = date('Y-m-d H:i:s', strtotime('-4 days'));
    $fiveDaysAgo = date('Y-m-d H:i:s', strtotime('-5 days'));
    $sixDaysAgo = date('Y-m-d H:i:s', strtotime('-6 days'));

    $workOrders = [
        [$ahmedId, $ahmedVehicleId, $ceramicId, 'delivered', $oilTechId, 1, 'vip', 'High quality execution requested', $sixDaysAgo, $sixDaysAgo],
        [$noraId, $noraVehicleId, $oilChangeId, 'delivered', $oilTechId, 2, 'normal', 'Oil level check', $fiveDaysAgo, $fiveDaysAgo],
        [$khalidId, $khalidVehicleId, $vipWashId, 'delivered', $employeeId, 1, 'vip', 'Car detailing check', $fourDaysAgo, $fourDaysAgo],
        [$fatimaId, $fatimaVehicleId, $exteriorWashId, 'delivered', $employeeId, 3, 'normal', null, $threeDaysAgo, $threeDaysAgo],
        [$ahmedId, $ahmedVehicleId, $acServiceId, 'delivered', $oilTechId, 2, 'high', 'Refill AC gas', $yesterday, $yesterday],
        [$noraId, $noraVehicleId, $vipWashId, 'delivered', $employeeId, 1, 'normal', null, $today, $today],
        
        // Active/Pending ones for today
        [$khalidId, $khalidVehicleId, $oilChangeId, 'in_progress', $oilTechId, 2, 'vip', 'Regular oil change', $today, $today],
        [$fatimaId, $fatimaVehicleId, $exteriorWashId, 'waiting', null, 3, 'normal', 'Quick wash', $today, $today],
        [$ahmedId, $ahmedVehicleId, $vipWashId, 'waiting', null, 1, 'vip', 'Weekly VIP service', $today, $today],
    ];

    $stmt = $pdo->prepare("INSERT INTO work_orders (customer_id, vehicle_id, service_id, status, assigned_employee_id, assigned_bay, priority, notes, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($workOrders as $wo) {
        $stmt->execute($wo);
    }
    echo "Work orders seeded.\n";

    // 7. Seed Inventory
    $inventoryItems = [
        ['Fully Synthetic Motor Oil 5W-30 (1L)', 'OIL-5W30-1L', '6281001234567', 'Oil', 50, 10, 45.00, 'Shell Saudi Arabia'],
        ['Premium Oil Filter - Type A', 'FIL-A-992', '6281007890123', 'Filters', 35, 8, 25.00, 'Bosch Gulf'],
        ['High Foam Car Shampoo (5L)', 'SHM-FOAM-5L', '6281004567890', 'Shampoo', 15, 3, 60.00, 'Meguiar\'s Dist.'],
        ['Liquid Carnauba Wax (500ml)', 'WAX-CARN-500', '6281001112223', 'Wax', 8, 4, 85.00, 'Turtle Wax Inc.'],
        ['Microfiber Towels (Pack of 10)', 'ACC-TOWL-10', '6281003334445', 'Accessories', 3, 5, 30.00, 'Local Textiles Co.'], // Low stock alert trigger
        ['Heavy Duty Degreaser (10L)', 'CHM-DEG-10L', '6281005556667', 'Chemicals', 2, 3, 120.00, 'SABIC Chemical'], // Low stock alert trigger
    ];

    $stmt = $pdo->prepare("INSERT INTO inventory (name, sku, barcode, category, stock_quantity, min_stock_level, unit_price, supplier_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($inventoryItems as $item) {
        $stmt->execute($item);
    }
    echo "Inventory items seeded.\n";

    // Get inventory IDs
    $oilInvId = $pdo->query("SELECT id FROM inventory WHERE sku='OIL-5W30-1L' LIMIT 1")->fetchColumn();
    $degreaserInvId = $pdo->query("SELECT id FROM inventory WHERE sku='CHM-DEG-10L' LIMIT 1")->fetchColumn();

    // 8. Seed Stock Movements
    $movements = [
        [$oilInvId, 50, 'Restock', $adminId],
        [$degreaserInvId, 2, 'Restock', $adminId],
    ];
    $stmt = $pdo->prepare("INSERT INTO stock_movements (inventory_id, quantity_changed, type, user_id) VALUES (?, ?, ?, ?)");
    foreach ($movements as $mov) {
        $stmt->execute($mov);
    }
    echo "Stock movements seeded.\n";

    // 9. Seed Audit Logs
    $auditLogs = [
        [$adminId, 'login', 'users', $adminId, json_encode(['ip' => '127.0.0.1'])],
        [$adminId, 'create', 'branches', $branchId, json_encode(['branch_name' => 'Riyadh Main Branch'])],
        [$adminId, 'import', 'database', null, json_encode(['file' => 'schema.sql'])],
    ];
    $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, entity_type, entity_id, details) VALUES (?, ?, ?, ?, ?)");
    foreach ($auditLogs as $log) {
        $stmt->execute($log);
    }
    echo "Audit logs seeded.\n";

    echo "Seeding completed successfully!\n";

} catch (Exception $e) {
    echo "Seeding failed: " . $e->getMessage() . "\n";
    exit(1);
}
