<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\CustomerRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\InventoryRepository;
use App\Repositories\AuditRepository;
use App\Core\Csrf;

class POSController extends Controller
{
    public function __construct(
        private readonly CustomerRepository $customerRepository,
        private readonly ServiceRepository $serviceRepository,
        private readonly InventoryRepository $inventoryRepository,
        private readonly AuditRepository $auditRepository
    ) {
    }

    public function index(): void
    {
        $customers = $this->customerRepository->getAll();
        $services = $this->serviceRepository->getAll();
        $inventory = $this->inventoryRepository->getAll();

        $this->view('pos/index', [
            'customers' => $customers,
            'services' => $services,
            'inventory' => $inventory
        ]);
    }

    public function checkout(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Security validation failed.';
            header('Location: /pos');
            exit;
        }

        $customerId = (int) ($_POST['customer_id'] ?? 0);
        $itemsJson = $_POST['items'] ?? '[]';
        $discount = (float) ($_POST['discount'] ?? 0.0);
        $paymentMethod = trim($_POST['payment_method'] ?? 'cash');
        $splitMethod = trim($_POST['split_method'] ?? '');

        $items = json_decode($itemsJson, true);

        if ($customerId === 0 || empty($items)) {
            $_SESSION['error'] = 'Checkout failed: Customer or items missing.';
            header('Location: /pos');
            exit;
        }

        $customer = $this->customerRepository->getById($customerId);
        if (!$customer) {
            $_SESSION['error'] = 'Customer not found.';
            header('Location: /pos');
            exit;
        }

        $subtotal = 0.0;
        $tax = 0.0;
        $resolvedItems = [];

        foreach ($items as $item) {
            $itemId = (int) $item['id'];
            $itemType = trim($item['type']); // 'service' or 'product'
            $qty = max(1, (int) $item['qty']);

            if ($itemType === 'service') {
                $srv = $this->serviceRepository->getById($itemId);
                if ($srv) {
                    $itemPrice = $srv->price * $qty;
                    $itemTax = $itemPrice * ($srv->taxRate / 100.0);
                    $subtotal += $itemPrice;
                    $tax += $itemTax;
                    $resolvedItems[] = [
                        'name' => $srv->name,
                        'type' => 'Service',
                        'price' => $srv->price,
                        'qty' => $qty,
                        'total' => $itemPrice
                    ];
                }
            } elseif ($itemType === 'product') {
                $prod = $this->inventoryRepository->getById($itemId);
                if ($prod) {
                    if ($prod['stock_quantity'] < $qty) {
                        $_SESSION['error'] = "Insufficient stock for product: {$prod['name']}. Available: {$prod['stock_quantity']}";
                        header('Location: /pos');
                        exit;
                    }
                    
                    // Deduct stock
                    $userId = $_SESSION['user']['id'] ?? null;
                    $this->inventoryRepository->adjustStock($itemId, -$qty, 'Sale', $userId);

                    $itemPrice = (float) $prod['unit_price'] * $qty;
                    $itemTax = $itemPrice * 0.15; // Standard 15% VAT on merchandise
                    $subtotal += $itemPrice;
                    $tax += $itemTax;
                    
                    $resolvedItems[] = [
                        'name' => $prod['name'],
                        'type' => 'Product',
                        'price' => (float) $prod['unit_price'],
                        'qty' => $qty,
                        'total' => $itemPrice
                    ];
                }
            }
        }

        $totalBeforeDiscount = $subtotal + $tax;
        $total = max(0.0, $totalBeforeDiscount - $discount);

        // Add Loyalty Points (1 point for every $10 spent)
        $pointsEarned = (int) ($total / 10);
        $customer->loyaltyPoints += $pointsEarned;
        $this->customerRepository->save($customer);

        // Audit log
        $userId = $_SESSION['user']['id'] ?? null;
        $this->auditRepository->log($userId, 'sale_checkout', 'customers', $customerId, [
            'total' => $total,
            'payment_method' => $paymentMethod,
            'items_count' => count($resolvedItems),
            'loyalty_points_earned' => $pointsEarned
        ]);

        // Put receipt in session for display modal
        $_SESSION['receipt'] = [
            'invoice_no' => 'INV-' . time() . '-' . rand(100, 999),
            'date' => date('Y-m-d H:i:s'),
            'customer_name' => $customer->fullName,
            'customer_phone' => $customer->phone,
            'items' => $resolvedItems,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total,
            'points_earned' => $pointsEarned,
            'new_points' => $customer->loyaltyPoints,
            'payment_method' => $paymentMethod . ($splitMethod ? " / $splitMethod" : '')
        ];

        $_SESSION['success'] = 'Checkout completed successfully.';
        header('Location: /pos');
        exit;
    }
}
