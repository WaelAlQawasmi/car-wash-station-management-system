<?php
use App\Core\Csrf;
?>
<div class="card card-soft">
    <div class="card-body p-4">
        
        <!-- Header search and create options -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h5 class="fw-bold mb-1"><?= __('services') ?></h5>
                <p class="text-muted mb-0 small"><?= __('services_desc') ?></p>
            </div>
            <div class="d-flex gap-2">
                <input type="text" class="form-control form-control-sm" style="max-width: 250px;" placeholder="<?= __('search') ?>" data-search-table="servicesListTable">
                <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#serviceModal" onclick="openNewServiceModal()"><i class="bi bi-plus-circle me-1"></i><?= __('new_service') ?></button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle" id="servicesListTable">
                <thead>
                    <tr>
                        <th><?= __('name') ?></th>
                        <th><?= __('category') ?></th>
                        <th><?= __('duration') ?></th>
                        <th><?= __('price') ?></th>
                        <th><?= __('tax_rate') ?></th>
                        <th><?= __('commission_rate') ?></th>
                        <th><?= __('status') ?></th>
                        <th class="text-end"><?= __('actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($services)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4"><?= __('no_services') ?></td></tr>
                    <?php else: ?>
                        <?php foreach ($services as $s): ?>
                            <tr>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($s->name) ?></td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary text-capitalize px-3 py-1"><?= htmlspecialchars($s->category) ?></span>
                                </td>
                                <td><i class="bi bi-clock me-1 text-muted"></i><?= (int) $s->durationMinutes ?> min</td>
                                <td class="fw-bold text-primary">دينار<?= number_format($s->price, 2) ?></td>
                                <td><?= (float) $s->taxRate ?>%</td>
                                <td><?= (float) $s->commissionRate ?>%</td>
                                <td>
                                    <span class="badge bg-<?= $s->active ? 'success' : 'danger' ?> bg-opacity-10 text-<?= $s->active ? 'success' : 'danger' ?>">
                                        <?= $s->active ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <!-- Edit Button -->
                                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#serviceModal" onclick="openEditServiceModal(<?= htmlspecialchars(json_encode([
                                            'id' => $s->id,
                                            'name' => $s->name,
                                            'category' => $s->category,
                                            'price' => $s->price,
                                            'duration' => $s->durationMinutes,
                                            'tax_rate' => $s->taxRate,
                                            'commission_rate' => $s->commissionRate,
                                            'active' => $s->active
                                        ])) ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <!-- Delete Button -->
                                        <form method="post" action="/services/delete" style="display:inline;" onsubmit="return confirm('<?= __('confirm_delete_service') ?>');">
                                            <?= Csrf::tokenField() ?>
                                            <input type="hidden" name="id" value="<?= $s->id ?>">
                                            <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Service Definition Modal -->
<div class="modal fade" id="serviceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="serviceModalTitle"><?= __('new_service') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="/services/save">
                <?= Csrf::tokenField() ?>
                <input type="hidden" name="id" id="serviceIdField" value="0">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium"><?= __('name') ?></label>
                        <input type="text" class="form-control" name="name" id="serviceNameField" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium"><?= __('category') ?></label>
                        <select class="form-select" name="category" id="serviceCategoryField">
                            <option value="wash"><?= __('cat_wash') ?></option>
                            <option value="oil"><?= __('cat_oil') ?></option>
                            <option value="detail"><?= __('cat_detail') ?></option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium"><?= __('price') ?></label>
                            <input type="number" step="0.01" class="form-control" name="price" id="servicePriceField" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium"><?= __('duration') ?></label>
                            <input type="number" class="form-control" name="duration" id="serviceDurationField" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium"><?= __('tax_rate') ?></label>
                            <input type="number" step="0.01" class="form-control" name="tax_rate" id="serviceTaxField" value="15.00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium"><?= __('commission_rate') ?></label>
                            <input type="number" step="0.01" class="form-control" name="commission_rate" id="serviceCommissionField" value="10.00">
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="active" value="1" id="serviceActiveField" checked>
                            <label class="form-check-label fw-medium" for="serviceActiveField"><?= __('service_active_label') ?></label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('cancel') ?></button>
                    <button class="btn btn-primary px-4"><?= __('save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openNewServiceModal() {
    document.getElementById('serviceModalTitle').textContent = '<?= __('new_service') ?>';
    document.getElementById('serviceIdField').value = 0;
    document.getElementById('serviceNameField').value = '';
    document.getElementById('serviceCategoryField').value = 'wash';
    document.getElementById('servicePriceField').value = '';
    document.getElementById('serviceDurationField').value = 30;
    document.getElementById('serviceTaxField').value = 15.00;
    document.getElementById('serviceCommissionField').value = 10.00;
    document.getElementById('serviceActiveField').checked = true;
}

function openEditServiceModal(service) {
    document.getElementById('serviceModalTitle').textContent = '<?= __('edit_service') ?>';
    document.getElementById('serviceIdField').value = service.id;
    document.getElementById('serviceNameField').value = service.name;
    document.getElementById('serviceCategoryField').value = service.category;
    document.getElementById('servicePriceField').value = service.price;
    document.getElementById('serviceDurationField').value = service.duration;
    document.getElementById('serviceTaxField').value = service.tax_rate;
    document.getElementById('serviceCommissionField').value = service.commission_rate;
    document.getElementById('serviceActiveField').checked = service.active;
}
</script>
