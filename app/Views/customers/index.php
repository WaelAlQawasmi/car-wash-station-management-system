<?php
use App\Core\Csrf;
?>
<div class="card card-soft">
    <div class="card-body p-4">
        
        <!-- Header search and create options -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h5 class="fw-bold mb-1"><?= __('customers') ?></h5>
                <p class="text-muted mb-0 small">Create, edit, and view customers and their vehicles history</p>
            </div>
            <div class="d-flex gap-2">
                <input type="text" class="form-control form-control-sm" style="max-width: 250px;" placeholder="<?= __('search') ?>" data-search-table="customersTable">
                <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#customerModal" onclick="openNewCustomerModal()"><i class="bi bi-person-plus me-1"></i><?= __('new_customer') ?></button>
            </div>
        </div>

        <!-- Customer list table -->
        <div class="table-responsive">
            <table class="table align-middle" id="customersTable">
                <thead>
                    <tr>
                        <th><?= __('name') ?></th>
                        <th><?= __('phone') ?></th>
                        <th><?= __('email') ?></th>
                        <th><?= __('loyalty_points') ?></th>
                        <th><?= __('membership') ?></th>
                        <th class="text-end"><?= __('actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($customers)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No customers found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($customers as $c): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($c->fullName) ?></div>
                                    <small class="text-muted">ID: #<?= (int) $c->id ?></small>
                                </td>
                                <td><?= htmlspecialchars($c->phone) ?></td>
                                <td><?= htmlspecialchars($c->email) ?: '-' ?></td>
                                <td>
                                    <span class="badge bg-warning bg-opacity-10 text-dark border-warning-subtle border"><i class="bi bi-star-fill text-warning me-1"></i><?= (int) $c->loyaltyPoints ?></span>
                                </td>
                                <td>
                                    <span class="badge badge-status text-capitalize bg-primary-subtle text-primary"><?= htmlspecialchars($c->membershipType) ?></span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <!-- View Vehicles trigger (AJAX) -->
                                        <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#vehiclesModal" data-load-vehicles="<?= $c->id ?>" data-target-container="vehiclesTableContainer" onclick="setVehicleCustomerId(<?= $c->id ?>)">
                                            <i class="bi bi-car-front me-1"></i><?= __('vehicles') ?>
                                        </button>
                                        <!-- Edit trigger -->
                                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#customerModal" onclick="openEditCustomerModal(<?= htmlspecialchars(json_encode([
                                            'id' => $c->id,
                                            'full_name' => $c->fullName,
                                            'phone' => $c->phone,
                                            'email' => $c->email,
                                            'loyalty_points' => $c->loyaltyPoints,
                                            'membership_type' => $c->membershipType
                                        ])) ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <!-- Delete trigger -->
                                        <form method="post" action="/customers/delete" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                            <?= Csrf::tokenField() ?>
                                            <input type="hidden" name="id" value="<?= $c->id ?>">
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

<!-- Customer Add/Edit Modal -->
<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="customerModalTitle"><?= __('new_customer') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="/customers/save">
                <?= Csrf::tokenField() ?>
                <input type="hidden" name="id" id="customerIdField" value="0">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium"><?= __('full_name') ?></label>
                        <input type="text" class="form-control" name="full_name" id="customerNameField" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium"><?= __('phone') ?></label>
                        <input type="text" class="form-control" name="phone" id="customerPhoneField" placeholder="+966500000000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium"><?= __('email') ?></label>
                        <input type="email" class="form-control" name="email" id="customerEmailField">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium"><?= __('loyalty_points') ?></label>
                            <input type="number" class="form-control" name="loyalty_points" id="customerPointsField" value="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium"><?= __('membership') ?></label>
                            <select class="form-select" name="membership_type" id="customerMembershipField">
                                <option value="standard">Standard</option>
                                <option value="silver">Silver</option>
                                <option value="gold">Gold</option>
                                <option value="platinum">Platinum</option>
                            </select>
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

<!-- Vehicles Listing and Adding Modal -->
<div class="modal fade" id="vehiclesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-car-front-fill me-2 text-primary"></i>Customer Vehicles</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive mb-4">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th><?= __('plate_number') ?></th>
                                <th><?= __('brand') ?> / <?= __('model') ?></th>
                                <th><?= __('year') ?></th>
                                <th><?= __('color') ?></th>
                                <th><?= __('mileage') ?></th>
                            </tr>
                        </thead>
                        <tbody id="vehiclesTableContainer">
                            <!-- Populated dynamically via AJAX -->
                        </tbody>
                    </table>
                </div>

                <hr>

                <!-- Add vehicle form inside modal -->
                <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle me-1"></i><?= __('add_vehicle') ?></h6>
                <form method="post" action="/customers/vehicles/save">
                    <?= Csrf::tokenField() ?>
                    <input type="hidden" name="customer_id" id="vehicleCustomerIdField" value="0">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-medium"><?= __('plate_number') ?></label>
                            <input type="text" class="form-control form-control-sm" name="plate_number" required placeholder="AAA 1111">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-medium"><?= __('brand') ?></label>
                            <input type="text" class="form-control form-control-sm" name="brand" required placeholder="Toyota">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-medium"><?= __('model') ?></label>
                            <input type="text" class="form-control form-control-sm" name="model" required placeholder="Camry">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-medium"><?= __('year') ?></label>
                            <input type="number" class="form-control form-control-sm" name="year" placeholder="2024">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-medium"><?= __('color') ?></label>
                            <input type="text" class="form-control form-control-sm" name="color" placeholder="White">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-medium"><?= __('mileage') ?></label>
                            <input type="number" class="form-control form-control-sm" name="mileage" placeholder="25000">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-medium"><?= __('fuel_type') ?></label>
                            <select class="form-select form-select-sm" name="fuel_type">
                                <option value="petrol">Petrol</option>
                                <option value="diesel">Diesel</option>
                                <option value="hybrid">Hybrid</option>
                                <option value="electric">Electric</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-end">
                        <button class="btn btn-success btn-sm px-4"><i class="bi bi-check2 me-1"></i><?= __('save') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openNewCustomerModal() {
    document.getElementById('customerModalTitle').textContent = '<?= __('new_customer') ?>';
    document.getElementById('customerIdField').value = 0;
    document.getElementById('customerNameField').value = '';
    document.getElementById('customerPhoneField').value = '';
    document.getElementById('customerEmailField').value = '';
    document.getElementById('customerPointsField').value = 0;
    document.getElementById('customerMembershipField').value = 'standard';
}

function openEditCustomerModal(customer) {
    document.getElementById('customerModalTitle').textContent = '<?= __('edit_customer') ?>';
    document.getElementById('customerIdField').value = customer.id;
    document.getElementById('customerNameField').value = customer.full_name;
    document.getElementById('customerPhoneField').value = customer.phone;
    document.getElementById('customerEmailField').value = customer.email;
    document.getElementById('customerPointsField').value = customer.loyalty_points;
    document.getElementById('customerMembershipField').value = customer.membership_type;
}

function setVehicleCustomerId(customerId) {
    document.getElementById('vehicleCustomerIdField').value = customerId;
}
</script>
