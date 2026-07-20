<?php
use App\Core\Csrf;
?>
<div class="row g-4">
    <!-- Inventory list card -->
    <div class="col-lg-8">
        <div class="card card-soft">
            <div class="card-body p-4">
                
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <h5 class="fw-bold mb-1"><?= __('inventory') ?></h5>
                        <p class="text-muted mb-0 small">Track motor oils, filters, washing chemicals, and spare accessories</p>
                    </div>
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control form-control-sm" style="max-width: 200px;" placeholder="Search inventory..." data-search-table="inventoryTable">
                        <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#itemModal" onclick="openNewItemModal()"><i class="bi bi-plus-circle me-1"></i>New Item</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle" id="inventoryTable">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Category</th>
                                <th>Barcode / SKU</th>
                                <th>Stock Level</th>
                                <th>Unit Price</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($items)): ?>
                                <tr><td colspan="6" class="text-center text-muted py-4">No inventory items declared.</td></tr>
                            <?php else: ?>
                                <?php foreach ($items as $item): ?>
                                    <?php 
                                    $isLow = $item['stock_quantity'] <= $item['min_stock_level'];
                                    ?>
                                    <tr class="<?= $isLow ? 'table-warning border-start border-4 border-warning' : '' ?>">
                                        <td>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($item['name']) ?></div>
                                            <small class="text-muted">Supplier: <?= htmlspecialchars($item['supplier_name']) ?: '-' ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary text-capitalize px-3 py-1"><?= htmlspecialchars($item['category']) ?></span>
                                        </td>
                                        <td>
                                            <small class="d-block fw-semibold text-dark">Barcode: <?= htmlspecialchars($item['barcode']) ?: '-' ?></small>
                                            <small class="text-muted">SKU: <?= htmlspecialchars($item['sku']) ?: '-' ?></small>
                                        </td>
                                        <td>
                                            <div class="fw-bold fs-6 <?= $isLow ? 'text-danger' : 'text-success' ?>">
                                                <?= (int) $item['stock_quantity'] ?>
                                            </div>
                                            <small class="text-muted text-xs">Min limit: <?= (int) $item['min_stock_level'] ?></small>
                                        </td>
                                        <td class="fw-bold text-primary">$<?= number_format((float) $item['unit_price'], 2) ?></td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <!-- Adjust Stock Trigger -->
                                                <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#adjustModal" onclick="prepareAdjustModal(<?= $item['id'] ?>, '<?= htmlspecialchars(addslashes($item['name'])) ?>')">
                                                    <i class="bi bi-arrow-left-right me-1"></i>Adjust
                                                </button>
                                                <!-- Edit Definition -->
                                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#itemModal" onclick="openEditItemModal(<?= htmlspecialchars(json_encode([
                                                    'id' => $item['id'],
                                                    'name' => $item['name'],
                                                    'sku' => $item['sku'],
                                                    'barcode' => $item['barcode'],
                                                    'category' => $item['category'],
                                                    'min_stock_level' => $item['min_stock_level'],
                                                    'unit_price' => $item['unit_price'],
                                                    'supplier_name' => $item['supplier_name']
                                                ])) ?>)">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
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
    </div>

    <!-- Right Sidebar logs and warnings -->
    <div class="col-lg-4">
        <!-- Low Stock Alerts -->
        <div class="card card-soft mb-4 border-start border-4 border-danger">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3 text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= __('low_stock_alerts') ?></h5>
                <div class="d-flex flex-column gap-2">
                    <?php if (empty($lowStock)): ?>
                        <div class="text-center py-4 text-muted small"><i class="bi bi-check-circle text-success me-1"></i>All stock levels are optimal.</div>
                    <?php else: ?>
                        <?php foreach ($lowStock as $ls): ?>
                            <div class="d-flex justify-content-between align-items-center p-2 rounded bg-danger-subtle text-danger text-sm">
                                <span class="fw-semibold text-truncate"><?= htmlspecialchars($ls['name']) ?></span>
                                <span class="fw-bold"><?= $ls['stock_quantity'] ?> left (min <?= $ls['min_stock_level'] ?>)</span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Stock Movements log -->
        <div class="card card-soft">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-primary"></i>Stock Movements</h5>
                <div class="d-flex flex-column gap-3" style="max-height: 380px; overflow-y: auto;">
                    <?php if (empty($movements)): ?>
                        <div class="text-center py-4 text-muted small">No movements logged yet.</div>
                    <?php else: ?>
                        <?php foreach ($movements as $m): ?>
                            <div class="d-flex gap-2 text-sm border-bottom pb-2">
                                <div class="stat-icon bg-secondary bg-opacity-10 text-secondary border rounded-circle flex-shrink-0" style="width:30px; height:30px;"><i class="bi bi-box-arrow-in-down small"></i></div>
                                <div>
                                    <div class="fw-semibold text-dark text-truncate"><?= htmlspecialchars($m['item_name']) ?></div>
                                    <div class="text-muted text-xs">
                                        Change: <strong class="<?= $m['quantity_changed'] > 0 ? 'text-success' : 'text-danger' ?>"><?= $m['quantity_changed'] > 0 ? '+' : '' ?><?= $m['quantity_changed'] ?></strong> 
                                        Type: <?= htmlspecialchars($m['type']) ?> by <?= htmlspecialchars($m['user_name'] ?? 'System') ?>
                                    </div>
                                    <small class="text-muted text-xs d-block mt-1"><?= date('Y-m-d H:i A', strtotime($m['created_at'])) ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="itemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="itemModalTitle">New Inventory Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="/inventory/save">
                <?= Csrf::tokenField() ?>
                <input type="hidden" name="id" id="itemIdField" value="0">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Item Name</label>
                        <input type="text" class="form-control" name="name" id="itemNameField" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">SKU Code</label>
                            <input type="text" class="form-control" name="sku" id="itemSkuField" placeholder="OIL-5W30-1L">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Barcode (EAN)</label>
                            <input type="text" class="form-control" name="barcode" id="itemBarcodeField" placeholder="628100000000">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Category</label>
                            <select class="form-select" name="category" id="itemCategoryField">
                                <option value="Oil">Oils</option>
                                <option value="Filters">Filters</option>
                                <option value="Shampoo">Shampoo & Wax</option>
                                <option value="Chemicals">Chemicals</option>
                                <option value="Accessories">Accessories</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Min Stock Level</label>
                            <input type="number" class="form-control" name="min_stock_level" id="itemMinField" value="5" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Unit Price ($)</label>
                            <input type="number" step="0.01" class="form-control" name="unit_price" id="itemPriceField" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Supplier Name</label>
                            <input type="text" class="form-control" name="supplier_name" id="itemSupplierField" placeholder="Supplier Dist. Co.">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary px-4">Save Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Adjust Stock Modal -->
<div class="modal fade" id="adjustModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Adjust Item Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="/inventory/adjust">
                <?= Csrf::tokenField() ?>
                <input type="hidden" name="inventory_id" id="adjustItemId" value="0">
                <div class="modal-body">
                    <p class="mb-3">Item: <strong id="adjustItemName"></strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Adjustment Type</label>
                        <select class="form-select" name="type" required>
                            <option value="Restock">Restock (Positive)</option>
                            <option value="Damage">Damage (Negative)</option>
                            <option value="Correction">Correction Adjustment</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Quantity Change</label>
                        <input type="number" class="form-control" name="quantity" required placeholder="Use negative numbers for reductions (e.g. -5)">
                        <small class="text-muted text-xs">Enter positive value for stock additions (e.g. 20) and negative value for stock deductions (e.g. -5).</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success px-4">Adjust Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openNewItemModal() {
    document.getElementById('itemModalTitle').textContent = 'New Inventory Item';
    document.getElementById('itemIdField').value = 0;
    document.getElementById('itemNameField').value = '';
    document.getElementById('itemSkuField').value = '';
    document.getElementById('itemBarcodeField').value = '';
    document.getElementById('itemCategoryField').value = 'Oil';
    document.getElementById('itemMinField').value = 5;
    document.getElementById('itemPriceField').value = '';
    document.getElementById('itemSupplierField').value = '';
}

function openEditItemModal(item) {
    document.getElementById('itemModalTitle').textContent = 'Edit Inventory Item';
    document.getElementById('itemIdField').value = item.id;
    document.getElementById('itemNameField').value = item.name;
    document.getElementById('itemSkuField').value = item.sku;
    document.getElementById('itemBarcodeField').value = item.barcode;
    document.getElementById('itemCategoryField').value = item.category;
    document.getElementById('itemMinField').value = item.min_stock_level;
    document.getElementById('itemPriceField').value = item.unit_price;
    document.getElementById('itemSupplierField').value = item.supplier_name;
}

function prepareAdjustModal(id, name) {
    document.getElementById('adjustItemId').value = id;
    document.getElementById('adjustItemName').textContent = name;
}
</script>
