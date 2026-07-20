<!doctype html>
<html lang="en" dir="ltr" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Car Stashen ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <style>
        :root { color-scheme: light; }
        body { background: #f4f7fb; font-family: Inter, Segoe UI, sans-serif; }
        .sidebar { min-height: 100vh; background: linear-gradient(135deg, #111827, #1f2937); color: white; }
        .nav-link { color: rgba(255,255,255,0.8); }
        .nav-link:hover, .nav-link.active { color: white; background: rgba(255,255,255,0.1); }
        .card-soft { border: 0; border-radius: 1rem; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08); }
        .stat-icon { width: 48px; height: 48px; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; }
    </style>
</head>
<body class="h-100">
<div class="container-fluid h-100">
    <div class="row h-100">
        <aside class="col-lg-2 sidebar p-3">
            <div class="d-flex align-items-center gap-2 mb-4">
                <div class="stat-icon bg-white text-dark"><i class="bi bi-car-front-fill"></i></div>
                <div>
                    <div class="fw-bold">Car Stashen</div>
                    <small class="text-white-50">ERP Platform</small>
                </div>
            </div>
            <nav class="nav flex-column gap-1">
                <a class="nav-link rounded px-3 py-2 <?= ($contentTemplate === 'dashboard/index') ? 'active' : '' ?>" href="/dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                <a class="nav-link rounded px-3 py-2 <?= ($contentTemplate === 'customers/index') ? 'active' : '' ?>" href="/customers"><i class="bi bi-people me-2"></i>Customers</a>
                <a class="nav-link rounded px-3 py-2 <?= ($contentTemplate === 'services/index') ? 'active' : '' ?>" href="/services"><i class="bi bi-tools me-2"></i>Services</a>
                <a class="nav-link rounded px-3 py-2" href="#"><i class="bi bi-box-seam me-2"></i>Inventory</a>
                <a class="nav-link rounded px-3 py-2" href="#"><i class="bi bi-receipt me-2"></i>Sales</a>
                <a class="nav-link rounded px-3 py-2" href="#"><i class="bi bi-graph-up me-2"></i>Reports</a>
                <a class="nav-link rounded px-3 py-2" href="#"><i class="bi bi-gear me-2"></i>Settings</a>
            </nav>
        </aside>
        <main class="col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Operations Overview</h2>
                    <p class="text-muted mb-0">Premium car wash and oil change management dashboard</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <button class="btn btn-outline-secondary"><i class="bi bi-bell"></i></button>
                    <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
                    <a href="/logout" class="btn btn-outline-danger">Logout</a>
                    <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">A</div>
                </div>
            </div>
            <?php include __DIR__ . '/' . $contentTemplate . '.php'; ?>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
