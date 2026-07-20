<?php
$summary = $summary ?? [];
?>
<div class="row g-4">
    <div class="col-xl-3 col-md-6">
        <div class="card card-soft h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Daily Revenue</p>
                        <h3 class="fw-bold">$<?= number_format($summary['daily_revenue'] ?? 0) ?></h3>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-cash-coin"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-soft h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Monthly Revenue</p>
                        <h3 class="fw-bold">$<?= number_format($summary['monthly_revenue'] ?? 0) ?></h3>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-graph-up-arrow"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-soft h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Today's Cars</p>
                        <h3 class="fw-bold"><?= $summary['todays_cars'] ?? 0 ?></h3>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-car-front"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-soft h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Pending Orders</p>
                        <h3 class="fw-bold"><?= $summary['pending_orders'] ?? 0 ?></h3>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-clock-history"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-xl-8">
        <div class="card card-soft h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Revenue Trends</h5>
                    <span class="badge bg-primary-subtle text-primary">Updated 5 min ago</span>
                </div>
                <canvas id="revenueChart" height="220"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card card-soft h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Top Services</h5>
                    <span class="text-muted">This Week</span>
                </div>
                <?php foreach (($summary['top_services'] ?? []) as $service): ?>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span><?= htmlspecialchars($service['name']) ?></span>
                        <span class="fw-bold text-primary"><?= (int) $service['count'] ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-xl-12">
        <div class="card card-soft">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Quick Actions</h5>
                    <span class="text-muted">Daily operations</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-3"><button class="btn btn-outline-primary w-100"><i class="bi bi-plus-circle me-2"></i>New Order</button></div>
                    <div class="col-md-3"><button class="btn btn-outline-success w-100"><i class="bi bi-person-plus me-2"></i>Add Customer</button></div>
                    <div class="col-md-3"><button class="btn btn-outline-warning w-100"><i class="bi bi-box-seam me-2"></i>Restock</button></div>
                    <div class="col-md-3"><button class="btn btn-outline-info w-100"><i class="bi bi-file-earmark-text me-2"></i>Reports</button></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-xl-6">
        <div class="card card-soft h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Best Customers</h5>
                <?php foreach (($summary['best_customers'] ?? []) as $customer): ?>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span><?= htmlspecialchars($customer['name']) ?></span>
                        <span class="fw-bold">$<?= number_format((int) $customer['spent']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card card-soft h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Top Employees</h5>
                <?php foreach (($summary['top_employees'] ?? []) as $employee): ?>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span><?= htmlspecialchars($employee['name']) ?></span>
                        <span class="fw-bold text-success"><?= (int) $employee['score'] ?>%</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('revenueChart');
if (ctx) {
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
      datasets: [{
        label: 'Revenue',
        data: [4200, 5100, 4700, 6200, 5900, 7600, 8900],
        borderColor: '#2563eb',
        backgroundColor: 'rgba(37,99,235,0.18)',
        tension: 0.35,
        fill: true
      }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
  });
}
</script>
