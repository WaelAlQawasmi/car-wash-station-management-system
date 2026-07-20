<div class="card card-soft">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Service Catalog</h5>
            <button class="btn btn-primary">+ New Service</button>
        </div>
        <div class="row g-3">
            <?php foreach ($services as $service): ?>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <h6 class="fw-bold"><?= htmlspecialchars($service['name']) ?></h6>
                        <p class="text-muted mb-2">Duration: <?= (int) $service['duration'] ?> min</p>
                        <div class="fw-bold text-primary">$<?= number_format((float) $service['price'], 2) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
