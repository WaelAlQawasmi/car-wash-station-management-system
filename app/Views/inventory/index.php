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
                        <p class="text-muted mb-0 small"><?= __('inventory_desc') ?></p>
                    </div>
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control form-control-sm" style="max-width: 200px;" placeholder="<?= __('search_inventory') ?>" data-search-table="inventoryTable">
                        <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#itemModal" onclick="openNewItemModal()"><i class="bi bi-plus-circle me-1"></i><?= __('new_inventory_item') ?></button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle" id="inventoryTable">
                        <thead>
                            <tr>
                                <th><?= __('item_col') ?></th>
                                <th><?= __('category') ?></th>
                                <th><?= __('barcode_sku_col') ?></th>
                                <th><?= __('stock_level_col') ?></th>
                                <th><?= __('unit_price_col') ?></th>
                                <th class="text-end"><?= __('actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($items)): ?>
                                <tr><td colspan="6" class="text-center text-muted py-4"><?= __('no_inventory') ?></td></tr>
                            <?php else: ?>
                                <?php foreach ($items as $item): ?>
                                    <?php 
                                    $isLow = $item['stock_quantity'] <= $item['min_stock_level'];
                                    ?>
                                    <tr class="<?= $isLow ? 'table-warning border-start border-4 border-warning' : '' ?>">
                                        <td>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($item['name']) ?></div>
                                            <small class="text-muted"><?= __('supplier') ?>: <?= htmlspecialchars($item['supplier_name']) ?: '-' ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary text-capitalize px-3 py-1"><?= htmlspecialchars($item['category']) ?></span>
                                        </td>
                                        <td>
                                            <small class="d-block fw-semibold text-dark"><?= __('barcode') ?>: <?= htmlspecialchars($item['barcode']) ?: '-' ?></small>
                                            <small class="text-muted">SKU: <?= htmlspecialchars($item['sku']) ?: '-' ?></small>
                                        </td>
                                        <td>
                                            <div class="fw-bold fs-6 <?= $isLow ? 'text-danger' : 'text-success' ?>">
                                                <?= (int) $item['stock_quantity'] ?>
                                            </div>
                                            <small class="text-muted text-xs"><?= __('min_limit') ?>: <?= (int) $item['min_stock_level'] ?></small>
                                        </td>
                                        <td class="fw-bold text-primary">$<?= number_format((float) $item['unit_price'], 2) ?></td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <!-- Adjust Stock Trigger -->
                                                <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#adjustModal" onclick="prepareAdjustModal(<?= $item['id'] ?>, '<?= htmlspecialchars(addslashes($item['name'])) ?>')">
                                                    <i class="bi bi-arrow-left-right me-1"></i><?= __('adjust_label') ?>
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
                        <div class="text-center py-4 text-muted small"><i class="bi bi-check-circle text-success me-1"></i><?= __('all_stock_ok') ?></div>
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
                <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-primary"></i><?= __('stock_movements') ?></h5>
                <div class="d-flex flex-column gap-3" style="max-height: 380px; overflow-y: auto;">
                    <?php if (empty($movements)): ?>
                        <div class="text-center py-4 text-muted small"><?= __('no_movements') ?></div>
                    <?php else: ?>
                        <?php foreach ($movements as $m): ?>
                            <div class="d-flex gap-2 text-sm border-bottom pb-2">
                                <div class="stat-icon bg-secondary bg-opacity-10 text-secondary border rounded-circle flex-shrink-0" style="width:30px; height:30px;"><i class="bi bi-box-arrow-in-down small"></i></div>
                                <div>
                                    <div class="fw-semibold text-dark text-truncate"><?= htmlspecialchars($m['item_name']) ?></div>
                                    <div class="text-muted text-xs">
                                        <?= __('change_label') ?>: <strong class="<?= $m['quantity_changed'] > 0 ? 'text-success' : 'text-danger' ?>"><?= $m['quantity_changed'] > 0 ? '+' : '' ?><?= $m['quantity_changed'] ?></strong> 
                                        <?= __('type_label') ?>: <?= htmlspecialchars($m['type']) ?> <?= __('by_label') ?> <?= htmlspecialchars($m['user_name'] ?? 'System') ?>
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
                <h5 class="modal-title fw-bold" id="itemModalTitle"><?= __('new_item_modal_title') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="/inventory/save">
                <?= Csrf::tokenField() ?>
                <input type="hidden" name="id" id="itemIdField" value="0">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium"><?= __('item_name') ?></label>
                        <input type="text" class="form-control" name="name" id="itemNameField" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium"><?= __('sku_code') ?></label>
                            <input type="text" class="form-control" name="sku" id="itemSkuField" placeholder="OIL-5W30-1L">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium"><?= __('barcode_ean') ?></label>
                            <input type="text" class="form-control" name="barcode" id="itemBarcodeField" placeholder="628100000000">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium"><?= __('category') ?></label>
                            <select class="form-select" name="category" id="itemCategoryField">
                                <option value="Oil"><?= __('cat_oil_inv') ?></option>
                                <option value="Filters"><?= __('cat_filters') ?></option>
                                <option value="Shampoo"><?= __('cat_shampoo') ?></option>
                                <option value="Chemicals"><?= __('cat_chemicals') ?></option>
                                <option value="Accessories"><?= __('cat_accessories') ?></option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium"><?= __('min_stock_level') ?></label>
                            <input type="number" class="form-control" name="min_stock_level" id="itemMinField" value="5" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium"><?= __('unit_price') ?> ($)</label>
                            <input type="number" step="0.01" class="form-control" name="unit_price" id="itemPriceField" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium"><?= __('supplier_name') ?></label>
                            <input type="text" class="form-control" name="supplier_name" id="itemSupplierField" placeholder="Supplier Dist. Co.">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('cancel') ?></button>
                    <button class="btn btn-primary px-4"><?= __('save_item') ?></button>
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
                <h5 class="modal-title fw-bold"><?= __('adjust_item_stock') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="/inventory/adjust">
                <?= Csrf::tokenField() ?>
                <input type="hidden" name="inventory_id" id="adjustItemId" value="0">
                <div class="modal-body">
                    <p class="mb-3"><?= __('item_label') ?>: <strong id="adjustItemName"></strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-medium"><?= __('adjustment_type') ?></label>
                        <select class="form-select" name="type" required>
                            <option value="Restock"><?= __('adjust_restock') ?></option>
                            <option value="Damage"><?= __('adjust_damage') ?></option>
                            <option value="Correction"><?= __('adjust_correction') ?></option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium"><?= __('qty_change') ?></label>
                        <input type="number" class="form-control" name="quantity" required placeholder="Use negative numbers for reductions (e.g. -5)">
                        <small class="text-muted text-xs"><?= __('qty_change_hint') ?></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('cancel') ?></button>
                    <button class="btn btn-success px-4"><?= __('adjust_btn') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openNewItemModal() {
    document.getElementById('itemModalTitle').textContent = '<?= __('new_item_modal_title') ?>';
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
    document.getElementById('itemModalTitle').textContent = '<?= __('edit_item_modal_title') ?>';
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
