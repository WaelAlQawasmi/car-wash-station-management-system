<?php
$summary = $summary ?? [];
$isRtl = \App\Services\LanguageService::isRtl();
?>
<div class="row g-4 mb-4">
    <!-- Daily Revenue Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-soft h-100 border-start border-4 border-primary">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 fw-medium"><?= __('daily_revenue') ?></p>
                        <h3 class="fw-bold mb-0 text-gradient">$<?= number_format($summary['daily_revenue'] ?? 0, 2) ?></h3>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-cash-coin"></i></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Monthly Revenue Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-soft h-100 border-start border-4 border-success">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 fw-medium"><?= __('monthly_revenue') ?></p>
                        <h3 class="fw-bold mb-0">$<?= number_format($summary['monthly_revenue'] ?? 0, 2) ?></h3>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-graph-up-arrow"></i></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Today's Cars Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-soft h-100 border-start border-4 border-warning">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 fw-medium"><?= __('todays_cars') ?></p>
                        <h3 class="fw-bold mb-0"><?= $summary['todays_cars'] ?? 0 ?></h3>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-car-front"></i></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pending Orders Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-soft h-100 border-start border-4 border-info">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 fw-medium"><?= __('pending_orders') ?></p>
                        <h3 class="fw-bold mb-0"><?= $summary['pending_orders'] ?? 0 ?></h3>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-clock-history"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts & Services Section -->
<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card card-soft h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-activity me-2 text-primary"></i><?= __('revenue_trends') ?></h5>
                    <span class="badge bg-primary-subtle text-primary py-2 px-3"><?= __('active') ?></span>
                </div>
                <canvas id="revenueChart" style="max-height: 280px;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4">
        <div class="card card-soft h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-star-fill me-2 text-warning"></i><?= __('top_services') ?></h5>
                <div class="d-flex flex-column gap-3">
                    <?php if (empty($summary['top_services'])): ?>
                        <div class="text-center py-4 text-muted"><?= __('no_transaction_data') ?></div>
                    <?php else: ?>
                        <?php foreach (($summary['top_services'] ?? []) as $service): ?>
                            <div class="d-flex justify-content-between align-items-center p-3 rounded bg-body-tertiary">
                                <span class="fw-medium"><?= htmlspecialchars($service['name']) ?></span>
                                <span class="badge bg-primary px-3 py-2"><?= (int) $service['count'] ?> <?= __('orders') ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Section -->
<div class="card card-soft mb-4">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-lightning-charge-fill me-2 text-primary"></i><?= __('quick_actions') ?></h5>
        <div class="row g-3">
            <div class="col-md-3">
                <a href="/work_orders" class="btn btn-outline-primary w-100 btn-action-premium"><i class="bi bi-plus-circle me-2"></i><?= __('new_order') ?></a>
            </div>
            <div class="col-md-3">
                <a href="/customers" class="btn btn-outline-success w-100 btn-action-premium"><i class="bi bi-person-plus me-2"></i><?= __('add_customer') ?></a>
            </div>
            <div class="col-md-3">
                <a href="/inventory" class="btn btn-outline-warning w-100 btn-action-premium"><i class="bi bi-box-seam me-2"></i><?= __('restock') ?></a>
            </div>
            <div class="col-md-3">
                <a href="/settings" class="btn btn-outline-info w-100 btn-action-premium"><i class="bi bi-sliders2 me-2"></i><?= __('settings') ?></a>
            </div>
        </div>
    </div>
</div>

<!-- Performance Lists Section -->
<div class="row g-4 mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="card card-soft h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-heart-fill me-2 text-danger"></i><?= __('best_customers') ?></h5>
                <div class="d-flex flex-column gap-2">
                    <?php if (empty($summary['best_customers'])): ?>
                        <div class="text-center py-4 text-muted"><?= __('no_sales_data') ?></div>
                    <?php else: ?>
                        <?php foreach (($summary['best_customers'] ?? []) as $customer): ?>
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="fw-semibold text-truncate"><?= htmlspecialchars($customer['name']) ?></span>
                                <span class="fw-bold text-success">$<?= number_format((float) $customer['spent'], 2) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-md-6">
        <div class="card card-soft h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-award-fill me-2 text-warning"></i><?= __('top_employees') ?></h5>
                <div class="d-flex flex-column gap-2">
                    <?php foreach (($summary['top_employees'] ?? []) as $emp): ?>
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="fw-semibold"><?= htmlspecialchars($emp['name']) ?></span>
                            <span class="badge bg-success bg-opacity-10 text-success"><?= (int) $emp['score'] ?>% <?= __('score') ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-md-12">
        <div class="card card-soft h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-info"></i><?= __('audit_logs') ?></h5>
                <div class="d-flex flex-column gap-3">
                    <?php if (empty($summary['recent_activities'])): ?>
                        <div class="text-center py-4 text-muted"><?= __('no_system_log') ?></div>
                    <?php else: ?>
                        <?php foreach (($summary['recent_activities'] ?? []) as $log): ?>
                            <div class="d-flex gap-2">
                                <div class="stat-icon bg-secondary bg-opacity-10 text-secondary border rounded-circle flex-shrink-0" style="width:32px; height:32px;"><i class="bi bi-info-circle small"></i></div>
                                <div class="overflow-hidden">
                                    <div class="text-truncate text-sm fw-medium"><?= htmlspecialchars($log['user_name'] ?? 'System') ?>: <?= htmlspecialchars($log['action']) ?></div>
                                    <small class="text-muted text-xs"><?= date('h:i A', strtotime($log['created_at'])) ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
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
      labels: ['<?= __('mon') ?>','<?= __('tue') ?>','<?= __('wed') ?>','<?= __('thu') ?>','<?= __('fri') ?>','<?= __('sat') ?>','<?= __('sun') ?>'],
      datasets: [{
        label: '<?= __('revenue_label') ?>',
        data: [4200, 5100, 4700, 6200, 5900, 7600, 8900],
        borderColor: '#2563eb',
        backgroundColor: 'rgba(37,99,235,0.08)',
        borderWidth: 3,
        tension: 0.4,
        fill: true,
        pointBackgroundColor: '#2563eb',
        pointHoverRadius: 8
      }]
    },
    options: { 
        responsive: true, 
        plugins: { legend: { display: false } },
        scales: {
            y: { grid: { color: 'rgba(128,128,128,0.1)' } },
            x: { grid: { display: false } }
        }
    }
  });
}
</script>
