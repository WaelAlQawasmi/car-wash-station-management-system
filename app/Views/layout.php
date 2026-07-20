<?php
use App\Services\LanguageService;
use App\Core\Csrf;

$locale = LanguageService::getLocale();
$isRtl = LanguageService::isRtl();
$user = $_SESSION['user'] ?? ['name' => 'Staff', 'role' => 'employee'];
?>
<!doctype html>
<html lang="<?= $locale ?>" dir="<?= $isRtl ? 'rtl' : 'ltr' ?>" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= Csrf::getToken() ?>">
    <title><?= __('app_name') ?></title>
    
    <!-- Dynamic Bootstrap loading based on RTL/LTR -->
    <?php if ($isRtl): ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <?php else: ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php endif; ?>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
</head>
<body class="h-100">
<div class="container-fluid h-100 p-0">
    <div class="row h-100 g-0">
        
        <!-- Sidebar Navigation Drawer -->
        <aside class="col-lg-2 col-md-3 sidebar p-3 d-flex flex-column justify-content-between d-none d-md-flex">
            <div>
                <div class="d-flex align-items-center gap-2 mb-4 mt-2">
                    <div class="stat-icon bg-primary bg-opacity-10 text-white rounded-circle"><i class="bi bi-car-front-fill fs-4 text-primary"></i></div>
                    <div>
                        <div class="fw-bold text-white fs-5">Car Stashen</div>
                        <small class="text-white-50">ERP v1.2</small>
                    </div>
                </div>
                <nav class="nav flex-column gap-1">
                    <a class="nav-link rounded px-3 py-2 <?= ($contentTemplate === 'dashboard/index') ? 'active' : '' ?>" href="/dashboard">
                        <i class="bi bi-speedometer2"></i>
                        <span><?= __('dashboard') ?></span>
                    </a>
                    <a class="nav-link rounded px-3 py-2 <?= ($contentTemplate === 'work_orders/index') ? 'active' : '' ?>" href="/work_orders">
                        <i class="bi bi-calendar2-range"></i>
                        <span><?= __('work_order_pipeline') ?></span>
                    </a>
                    <a class="nav-link rounded px-3 py-2 <?= ($contentTemplate === 'pos/index') ? 'active' : '' ?>" href="/pos">
                        <i class="bi bi-receipt-cutoff"></i>
                        <span><?= __('sales') ?></span>
                    </a>
                    <a class="nav-link rounded px-3 py-2 <?= ($contentTemplate === 'customers/index') ? 'active' : '' ?>" href="/customers">
                        <i class="bi bi-people"></i>
                        <span><?= __('customers') ?></span>
                    </a>
                    <a class="nav-link rounded px-3 py-2 <?= ($contentTemplate === 'services/index') ? 'active' : '' ?>" href="/services">
                        <i class="bi bi-tools"></i>
                        <span><?= __('services') ?></span>
                    </a>
                    <a class="nav-link rounded px-3 py-2 <?= ($contentTemplate === 'inventory/index') ? 'active' : '' ?>" href="/inventory">
                        <i class="bi bi-box-seam"></i>
                        <span><?= __('inventory') ?></span>
                    </a>
                    <a class="nav-link rounded px-3 py-2 <?= ($contentTemplate === 'settings/index') ? 'active' : '' ?>" href="/settings">
                        <i class="bi bi-sliders2"></i>
                        <span><?= __('settings') ?></span>
                    </a>
                    <?php if (($user['role'] ?? '') === 'super_admin'): ?>
                    <a class="nav-link rounded px-3 py-2 <?= ($contentTemplate === 'users/index') ? 'active' : '' ?>" href="/users">
                        <i class="bi bi-people-fill"></i>
                        <span><?= __('user_management') ?></span>
                    </a>
                    <?php endif; ?>
                </nav>
            </div>
            
            <div class="mt-auto border-top border-secondary border-opacity-25 pt-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-white text-truncate fw-medium" style="max-width: 120px;"><?= htmlspecialchars($user['name']) ?></div>
                        <small class="text-white-50 text-capitalize fs-7"><?= str_replace('_', ' ', htmlspecialchars($user['role'])) ?></small>
                    </div>
                </div>
                <a href="/logout" class="btn btn-outline-danger btn-sm w-100 mt-2"><i class="bi bi-box-arrow-left me-1"></i><?= __('logout') ?></a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="col-lg-10 col-md-9 d-flex flex-column h-100">
            
            <!-- Global Top Navigation Header -->
            <header class="navbar navbar-expand-lg border-bottom px-4 py-2 sticky-top bg-body">
                <div class="container-fluid p-0">
                    <button class="btn btn-outline-secondary d-md-none me-2" id="sidebarToggle"><i class="bi bi-list"></i></button>
                    
                    <div class="d-flex align-items-center gap-2 d-md-none">
                        <i class="bi bi-car-front-fill text-primary fs-3"></i>
                        <span class="fw-bold">Car Stashen</span>
                    </div>

                    <div class="d-none d-lg-block">
                        <h4 class="fw-bold mb-0 text-capitalize"><?= __('app_name') ?></h4>
                    </div>

                    <div class="<?= $isRtl ? 'ms-auto' : 'me-auto' ?>"></div>
                    
                    <div class="d-flex gap-3 align-items-center">
                        <!-- Theme Toggle -->
                        <button class="btn btn-outline-secondary btn-sm" id="themeToggle"><i class="bi bi-moon-stars-fill"></i></button>

                        <!-- Language Switcher -->
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-globe me-1"></i><?= $locale === 'ar' ? 'العربية' : 'English' ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-menu-item dropdown-item" href="/settings/set_lang?lang=en">English</a></li>
                                <li><a class="dropdown-menu-item dropdown-item" href="/settings/set_lang?lang=ar">العربية</a></li>
                            </ul>
                        </div>

                        <!-- User Info Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-light d-flex align-items-center gap-2 border" type="button" data-bs-toggle="dropdown">
                                <span class="d-none d-md-inline fw-semibold"><?= htmlspecialchars($user['name']) ?></span>
                                <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                </div>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item text-danger" href="/logout"><i class="bi bi-box-arrow-left me-2"></i><?= __('logout') ?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Inner Page Content container -->
            <section class="flex-grow-1 overflow-auto p-4">
                
                <!-- Success and Error Session Alerts -->
                <?php if (!empty($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show card-soft mb-4 border-start border-4 border-success" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i><?= $_SESSION['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (!empty($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show card-soft mb-4 border-start border-4 border-danger" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $_SESSION['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Include Page Content Template -->
                <div class="fade-in">
                    <?php include __DIR__ . '/' . $contentTemplate . '.php'; ?>
                </div>
            </section>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
