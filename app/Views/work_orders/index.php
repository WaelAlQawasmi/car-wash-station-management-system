<?php
use App\Core\Csrf;
?>
<div class="row g-4 mb-4">
    <!-- Washing/Oil Bays status widgets -->
    <div class="col-12">
        <div class="card card-soft">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-cpu me-2 text-primary"></i>Service Bays Timer Status</h6>
                    <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#newOrderModal"><i class="bi bi-plus-circle me-1"></i><?= __('new_order') ?></button>
                </div>
                <div class="row g-3">
                    <?php foreach ($bays as $bayNum => $bay): ?>
                        <div class="col-md col-sm-6">
                            <div class="bay-timer-widget border p-3 rounded bg-body-tertiary text-center">
                                <div class="text-xs text-muted mb-1">Bay #<?= $bayNum ?></div>
                                <?php if ($bay['status'] === 'occupied'): ?>
                                    <div class="badge bg-danger mb-2">Occupied</div>
                                    <div class="fw-bold text-sm text-truncate"><?= htmlspecialchars($bay['order']['plate_number']) ?></div>
                                    <small class="text-xs d-block text-muted">ID: #<?= $bay['order']['id'] ?></small>
                                    <div class="d-flex gap-1 justify-content-center mt-2">
                                        <form method="post" action="/work_orders/update_status">
                                            <?= Csrf::tokenField() ?>
                                            <input type="hidden" name="id" value="<?= $bay['order']['id'] ?>">
                                            <input type="hidden" name="status" value="completed">
                                            <button class="btn btn-success btn-xs py-0 px-2" title="Mark Completed"><i class="bi bi-check2"></i></button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <div class="badge bg-success mb-2">Available</div>
                                    <div class="text-muted text-xs py-1">Ready for car</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pipeline board column layout -->
<div class="row g-4">
    <?php
    $columns = [
        'waiting' => ['title' => __('waiting'), 'class' => 'warning'],
        'in_progress' => ['title' => __('in_progress'), 'class' => 'primary'],
        'completed' => ['title' => __('completed'), 'class' => 'success'],
        'delivered' => ['title' => __('delivered'), 'class' => 'info']
    ];
    ?>
    <?php foreach ($columns as $statusKey => $col): ?>
        <div class="col-xl-3 col-md-6">
            <div class="pipeline-column h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-secondary border-opacity-10">
                    <h6 class="fw-bold mb-0 text-capitalize text-<?= $col['class'] ?>"><?= $col['title'] ?></h6>
                    <span class="badge bg-<?= $col['class'] ?> bg-opacity-10 text-<?= $col['class'] ?> rounded-pill">
                        <?= count(array_filter($orders, fn($o) => $o['status'] === $statusKey)) ?>
                    </span>
                </div>
                
                <div class="pipeline-cards-container">
                    <?php foreach ($orders as $o): ?>
                        <?php if ($o['status'] !== $statusKey) continue; ?>
                        
                        <div class="pipeline-card priority-<?= htmlspecialchars($o['priority']) ?> p-3 mb-3 card-soft">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary text-uppercase small" style="font-size:0.7rem;"><?= htmlspecialchars($o['priority']) ?></span>
                                <small class="text-muted">#<?= (int) $o['id'] ?></small>
                            </div>
                            
                            <h6 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($o['plate_number']) ?></h6>
                            <div class="small text-muted mb-2"><?= htmlspecialchars($o['brand']) ?> <?= htmlspecialchars($o['model']) ?></div>
                            
                            <div class="d-flex align-items-center gap-1 small text-primary mb-2">
                                <i class="bi bi-tools"></i>
                                <span class="fw-semibold"><?= htmlspecialchars($o['service_name']) ?></span>
                            </div>
                            
                            <div class="small text-muted mb-3 border-top pt-2">
                                <i class="bi bi-person me-1"></i><?= htmlspecialchars($o['customer_name']) ?><br>
                                <?php if ($o['assigned_employee_id']): ?>
                                    <i class="bi bi-person-badge me-1"></i>Assigned: <?= htmlspecialchars($o['employee_name']) ?> (Bay <?= $o['assigned_bay'] ?>)
                                <?php endif; ?>
                            </div>
                            
                            <!-- Status actions panel -->
                            <div class="d-flex justify-content-end gap-1 mt-2">
                                <?php if ($statusKey === 'waiting'): ?>
                                    <button class="btn btn-primary btn-xs" data-bs-toggle="modal" data-bs-target="#assignModal" onclick="prepareAssignModal(<?= $o['id'] ?>)">
                                        <i class="bi bi-play-fill me-1"></i>Start
                                    </button>
                                <?php elseif ($statusKey === 'in_progress'): ?>
                                    <form method="post" action="/work_orders/update_status">
                                        <?= Csrf::tokenField() ?>
                                        <input type="hidden" name="id" value="<?= $o['id'] ?>">
                                        <input type="hidden" name="status" value="completed">
                                        <button class="btn btn-success btn-xs"><i class="bi bi-check2 me-1"></i>Done</button>
                                    </form>
                                <?php elseif ($statusKey === 'completed'): ?>
                                    <form method="post" action="/work_orders/update_status">
                                        <?= Csrf::tokenField() ?>
                                        <input type="hidden" name="id" value="<?= $o['id'] ?>">
                                        <input type="hidden" name="status" value="delivered">
                                        <button class="btn btn-info btn-xs text-white"><i class="bi bi-truck me-1"></i>Deliver</button>
                                    </form>
                                <?php endif; ?>
                                
                                <form method="post" action="/work_orders/delete" onsubmit="return confirm('Delete work order?');" style="display:inline;">
                                    <?= Csrf::tokenField() ?>
                                    <input type="hidden" name="id" value="<?= $o['id'] ?>">
                                    <button class="btn btn-outline-danger btn-xs"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Assign Employee & Bay Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Start Work Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="/work_orders/update_status">
                <?= Csrf::tokenField() ?>
                <input type="hidden" name="id" id="assignOrderId" value="0">
                <input type="hidden" name="status" value="in_progress">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Assign Employee</label>
                        <select class="form-select" name="assigned_employee_id" required>
                            <option value="">-- Choose Employee --</option>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['name']) ?> (<?= htmlspecialchars($emp['role']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Assign Bay</label>
                        <select class="form-select" name="assigned_bay" required>
                            <option value="">-- Choose Bay --</option>
                            <?php foreach ($bays as $bayNum => $bay): ?>
                                <option value="<?= $bayNum ?>" <?= $bay['status'] === 'occupied' ? 'disabled' : '' ?>>
                                    Bay #<?= $bayNum ?> <?= $bay['status'] === 'occupied' ? '(Occupied)' : '(Available)' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary px-4">Start Service</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- New Work Order Modal -->
<div class="modal fade" id="newOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><?= __('new_work_order') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="/work_orders/save">
                <?= Csrf::tokenField() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium"><?= __('select_customer') ?></label>
                        <select class="form-select" id="newOrderCustomer" name="customer_id" onchange="loadCustomerVehiclesForOrder()" required>
                            <option value="">-- Choose Customer --</option>
                            <?php foreach ($customers as $c): ?>
                                <option value="<?= $c->id ?>"><?= htmlspecialchars($c->fullName) ?> (<?= htmlspecialchars($c->phone) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium"><?= __('select_vehicle') ?></label>
                        <select class="form-select" id="newOrderVehicle" name="vehicle_id" required disabled>
                            <option value="">-- Select Customer First --</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium"><?= __('select_service') ?></label>
                        <select class="form-select" name="service_id" required>
                            <option value="">-- Choose Service --</option>
                            <?php foreach ($services as $s): ?>
                                <option value="<?= $s->id ?>"><?= htmlspecialchars($s->name) ?> ($<?= number_format($s->price, 2) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium"><?= __('priority') ?></label>
                            <select class="form-select" name="priority">
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                                <option value="vip">VIP</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium"><?= __('status') ?></label>
                            <select class="form-select" name="status">
                                <option value="waiting">Waiting</option>
                                <option value="in_progress">In Progress</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium"><?= __('notes') ?></label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Instructions/Vehicles issues..."></textarea>
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
function prepareAssignModal(orderId) {
    document.getElementById('assignOrderId').value = orderId;
}

function loadCustomerVehiclesForOrder() {
    const customerId = document.getElementById('newOrderCustomer').value;
    const vehicleSelect = document.getElementById('newOrderVehicle');
    
    if (!customerId) {
        vehicleSelect.innerHTML = '<option value="">-- Select Customer First --</option>';
        vehicleSelect.disabled = true;
        return;
    }

    vehicleSelect.innerHTML = '<option value="">Loading vehicles...</option>';
    vehicleSelect.disabled = true;

    fetch(`/customers/vehicles?customer_id=${customerId}`)
        .then(res => res.json())
        .then(vehicles => {
            vehicleSelect.innerHTML = '';
            if (vehicles.length === 0) {
                vehicleSelect.innerHTML = '<option value="">No vehicles found - please add vehicle in Customer page first</option>';
                return;
            }
            vehicleSelect.disabled = false;
            vehicles.forEach(v => {
                const opt = document.createElement('option');
                opt.value = v.id;
                opt.textContent = `${v.plate_number} - ${v.brand} ${v.model} (${v.color || ''})`;
                vehicleSelect.appendChild(opt);
            });
        })
        .catch(err => {
            vehicleSelect.innerHTML = '<option value="">Failed to load vehicles</option>';
        });
}
</script>
