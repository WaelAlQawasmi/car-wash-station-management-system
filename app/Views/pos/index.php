<?php
use App\Core\Csrf;
?>
<div class="row g-4">
    <!-- Items Catalog Selector -->
    <div class="col-lg-7">
        <div class="card card-soft h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-grid-fill me-2 text-primary"></i><?= __('items_catalog') ?></h5>
                    <input type="text" class="form-control form-control-sm" style="max-width: 220px;" placeholder="<?= __('search_catalog') ?>" id="posCatalogSearch">
                </div>
                
                <!-- Category Tabs -->
                <ul class="nav nav-pills nav-fill gap-2 mb-3 bg-body-tertiary p-1 rounded-3" id="posCategoryTabs">
                    <li class="nav-item">
                        <button class="nav-link active btn-sm" onclick="filterPOSCatalog('all')"><?= __('all') ?></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link btn-sm" onclick="filterPOSCatalog('service')"><?= __('service_tab') ?></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link btn-sm" onclick="filterPOSCatalog('product')"><?= __('product_tab') ?></button>
                    </li>
                </ul>

                <div class="pos-items-grid row g-3" id="posItemsContainer">
                    <!-- Services -->
                    <?php foreach ($services as $s): ?>
                        <div class="col-md-6 pos-item-card" data-type="service" data-name="<?= htmlspecialchars(strtolower($s->name)) ?>">
                            <div class="border p-3 rounded-3 h-100 d-flex flex-column justify-content-between shadow-sm bg-body">
                                <div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="badge bg-primary bg-opacity-10 text-primary text-xs"><?= __('service_label') ?></span>
                                        <small class="text-muted"><i class="bi bi-clock me-1"></i><?= $s->durationMinutes ?> min</small>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-2"><?= htmlspecialchars($s->name) ?></h6>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="fw-bold text-primary">دينار<?= number_format($s->price, 2) ?></span>
                                    <button class="btn btn-outline-primary btn-sm rounded-circle p-1" style="width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center;" onclick="addToCart(<?= $s->id ?>, 'service', '<?= htmlspecialchars(addslashes($s->name)) ?>', <?= $s->price ?>)"><i class="bi bi-plus"></i></button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Products -->
                    <?php foreach ($inventory as $i): ?>
                        <div class="col-md-6 pos-item-card" data-type="product" data-name="<?= htmlspecialchars(strtolower($i['name'])) ?>">
                            <div class="border p-3 rounded-3 h-100 d-flex flex-column justify-content-between shadow-sm bg-body">
                                <div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="badge bg-success bg-opacity-10 text-success text-xs"><?= __('product_label') ?></span>
                                        <small class="text-muted"><?= __('stock_label') ?>: <?= $i['stock_quantity'] ?></small>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-2"><?= htmlspecialchars($i['name']) ?></h6>
                                    <small class="text-muted text-xs d-block mb-1"><?= __('barcode') ?>: <?= htmlspecialchars($i['barcode']) ?></small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="fw-bold text-success">دينار<?= number_format((float) $i['unit_price'], 2) ?></span>
                                    <?php if ($i['stock_quantity'] > 0): ?>
                                        <button class="btn btn-outline-success btn-sm rounded-circle p-1" style="width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center;" onclick="addToCart(<?= $i['id'] ?>, 'product', '<?= htmlspecialchars(addslashes($i['name'])) ?>', <?= (float) $i['unit_price'] ?>)"><i class="bi bi-plus"></i></button>
                                    <?php else: ?>
                                        <span class="text-danger text-xs fw-bold"><?= __('out_of_stock') ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- POS Checkout Panel -->
    <div class="col-lg-5">
        <div class="card card-soft h-100 border-top border-4 border-primary">
            <form method="post" action="/pos/checkout" id="posCheckoutForm">
                <?= Csrf::tokenField() ?>
                <!-- Hidden input containing JSON array of cart items -->
                <input type="hidden" name="items" id="posItemsInput" value="[]">
                
                <div class="card-body p-4 d-flex flex-column justify-content-between" style="min-height:550px;">
                    <div>
                        <h5 class="fw-bold mb-3"><i class="bi bi-cart3 me-2 text-primary"></i><?= __('cart') ?></h5>
                        
                        <!-- Customer Selector -->
                        <div class="mb-3">
                            <label class="form-label small fw-medium"><?= __('select_customer') ?></label>
                            <select class="form-select" name="customer_id" required>
                                <option value=""><?= __('select_customer_req') ?></option>
                                <?php foreach ($customers as $c): ?>
                                    <option value="<?= $c->id ?>"><?= htmlspecialchars($c->fullName) ?> (<?= htmlspecialchars($c->phone) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Cart Items Container -->
                        <div class="pos-cart-list border rounded-3 p-2 bg-body-tertiary mb-3">
                            <div class="text-center text-muted py-4 small" id="emptyCartMessage"><?= __('cart_empty') ?></div>
                            <table class="table table-sm align-middle d-none" id="cartTable">
                                <thead>
                                    <tr>
                                        <th><?= __('item') ?></th>
                                        <th style="width:80px;"><?= __('qty') ?></th>
                                        <th><?= __('price') ?></th>
                                        <th class="text-end"><?= __('action') ?></th>
                                    </tr>
                                </thead>
                                <tbody id="cartTableBody">
                                    <!-- Dynamic rows -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Payment options and totals -->
                    <div class="border-top pt-3 mt-auto">
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="form-label small fw-medium"><?= __('pay_method') ?></label>
                                <select class="form-select form-select-sm" name="payment_method" id="payMethod" onchange="toggleSplitPayment()" required>
                                    <option value="cash"><?= __('cash') ?></option>
                                    <option value="visa"><?= __('visa') ?></option>
                                    <option value="mastercard"><?= __('mastercard') ?></option>
                                    <option value="apple_pay"><?= __('apple_pay') ?></option>
                                    <option value="wallet"><?= __('wallet') ?></option>
                                </select>
                            </div>
                            <div class="col-md-6" id="splitPaymentCol" style="display:none;">
                                <label class="form-label small fw-medium"><?= __('split_payment') ?></label>
                                <select class="form-select form-select-sm" name="split_method">
                                    <option value="">-- None --</option>
                                    <option value="cash"><?= __('cash') ?></option>
                                    <option value="visa"><?= __('visa') ?></option>
                                    <option value="apple_pay"><?= __('apple_pay') ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-medium"><?= __('discount') ?> ($)</label>
                            <input type="number" step="0.01" class="form-control form-control-sm" name="discount" id="posDiscount" value="0.00" onkeyup="recalculatePOSCart()">
                        </div>

                        <!-- Calculations summary -->
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted"><?= __('subtotal') ?>:</span>
                            <span id="posSubtotal">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted"><?= __('tax') ?> (VAT 15%):</span>
                            <span id="posTax">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-2">
                            <span class="text-muted"><?= __('discount') ?>:</span>
                            <span id="posDiscountVal">-$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold fs-5 border-top pt-2">
                            <span><?= __('total') ?>:</span>
                            <span class="text-primary" id="posTotal">$0.00</span>
                        </div>

                        <button class="btn btn-primary w-100 mt-3 py-2 btn-action-premium" id="posSubmitBtn" disabled><i class="bi bi-credit-card me-2"></i><?= __('checkout') ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Receipt Modal (triggers if receipt exists in session) -->
<?php if (!empty($_SESSION['receipt'])): ?>
    <?php $r = $_SESSION['receipt']; ?>
    <div class="modal fade" id="receiptModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-printer-fill me-2"></i><?= __('sale_invoice_title') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="printableReceiptArea">
                    <div class="text-center mb-4">
                        <h4 class="fw-bold"><?= __('app_name') ?></h4>
                        <p class="text-muted small"><?= __('branch_name') ?><br><?= __('branch_phone') ?></p>
                        <div class="border-top border-bottom py-2 mt-2">
                            <div class="fw-semibold"><?= __('tax_invoice_receipt') ?></div>
                            <small class="text-muted"><?= __('invoice_no') ?>: <?= htmlspecialchars($r['invoice_no']) ?></small><br>
                            <small class="text-muted"><?= __('date') ?>: <?= htmlspecialchars($r['date']) ?></small>
                        </div>
                    </div>

                    <div class="mb-3 small">
                        <strong><?= __('receipt_customer') ?>:</strong> <?= htmlspecialchars($r['customer_name']) ?><br>
                        <strong><?= __('receipt_phone') ?>:</strong> <?= htmlspecialchars($r['customer_phone']) ?><br>
                        <strong><?= __('receipt_payment') ?>:</strong> <?= htmlspecialchars(strtoupper($r['payment_method'])) ?>
                    </div>

                    <table class="table table-sm table-borderless text-sm mb-4">
                        <thead>
                            <tr class="border-bottom">
                                <th><?= __('item') ?></th>
                                <th class="text-center"><?= __('qty') ?></th>
                                <th class="text-end"><?= __('price') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($r['items'] as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['name']) ?></td>
                                    <td class="text-center"><?= $item['qty'] ?></td>
                                    <td class="text-end">دينار<?= number_format($item['price'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="border-top pt-2 text-sm">
                        <div class="d-flex justify-content-between mb-1">
                            <span><?= __('subtotal') ?>:</span>
                            <span>دينار<?= number_format($r['subtotal'], 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span><?= __('vat_15') ?>:</span>
                            <span>دينار<?= number_format($r['tax'], 2) ?></span>
                        </div>
                        <?php if ($r['discount'] > 0): ?>
                            <div class="d-flex justify-content-between mb-1 text-danger">
                                <span><?= __('discount') ?>:</span>
                                <span>-$<?= number_format($r['discount'], 2) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between fw-bold border-top pt-2 fs-6">
                            <span><?= __('receipt_total') ?>:</span>
                            <span>دينار<?= number_format($r['total'], 2) ?></span>
                        </div>
                    </div>

                    <div class="border-top border-secondary border-opacity-10 mt-3 pt-3 text-center small text-muted">
                        <?= __('loyalty_earned') ?>: <?= $r['points_earned'] ?> <?= __('loyalty_points_unit') ?><br>
                        <?= __('loyalty_balance') ?>: <?= $r['new_points'] ?> <?= __('points_unit') ?><br>
                        <strong class="text-dark d-block mt-2"><?= __('thank_you') ?></strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('close') ?></button>
                    <button class="btn btn-primary" onclick="printReceipt()"><i class="bi bi-printer me-1"></i><?= __('print_invoice') ?></button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = new bootstrap.Modal(document.getElementById('receiptModal'));
        modal.show();
    });

    function printReceipt() {
        const printContent = document.getElementById('printableReceiptArea').innerHTML;
        const originalContent = document.body.innerHTML;
        document.body.innerHTML = printContent;
        window.print();
        document.body.innerHTML = originalContent;
        window.location.reload(); // Reload to restore UI scripts bindings
    }
    </script>
    <?php unset($_SESSION['receipt']); ?>
<?php endif; ?>

<script>
let cart = [];

function filterPOSCatalog(category) {
    const tabs = document.querySelectorAll('#posCategoryTabs button');
    tabs.forEach(btn => btn.classList.remove('active'));
    
    // Set active tab
    const eventTarget = window.event?.target;
    if (eventTarget) eventTarget.classList.add('active');

    const cards = document.querySelectorAll('.pos-item-card');
    cards.forEach(card => {
        const type = card.getAttribute('data-type');
        if (category === 'all' || type === category) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// Catalog live search
document.getElementById('posCatalogSearch')?.addEventListener('keyup', function() {
    const term = this.value.toLowerCase();
    const cards = document.querySelectorAll('.pos-item-card');
    cards.forEach(card => {
        const name = card.getAttribute('data-name');
        if (name.includes(term)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});

function addToCart(id, type, name, price) {
    const existing = cart.find(item => item.id === id && item.type === type);
    if (existing) {
        existing.qty++;
    } else {
        cart.push({ id, type, name, price, qty: 1 });
    }
    updatePOSCart();
}

function removeFromCart(id, type) {
    cart = cart.filter(item => !(item.id === id && item.type === type));
    updatePOSCart();
}

function adjustQty(id, type, change) {
    const item = cart.find(item => item.id === id && item.type === type);
    if (item) {
        item.qty += change;
        if (item.qty <= 0) {
            removeFromCart(id, type);
            return;
        }
    }
    updatePOSCart();
}

function updatePOSCart() {
    const emptyMsg = document.getElementById('emptyCartMessage');
    const table = document.getElementById('cartTable');
    const tbody = document.getElementById('cartTableBody');
    const submitBtn = document.getElementById('posSubmitBtn');

    if (cart.length === 0) {
        emptyMsg.classList.remove('d-none');
        table.classList.add('d-none');
        submitBtn.disabled = true;
    } else {
        emptyMsg.classList.add('d-none');
        table.classList.remove('d-none');
        submitBtn.disabled = false;
        
        tbody.innerHTML = '';
        cart.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <div class="fw-bold">${item.name}</div>
                    <small class="text-muted text-uppercase text-xs">${item.type}</small>
                </td>
                <td>
                    <div class="d-flex align-items-center gap-1">
                        <button type="button" class="btn btn-light btn-xs p-1" style="width:20px; height:20px; line-height:10px;" onclick="adjustQty(${item.id}, '${item.type}', -1)">-</button>
                        <span class="mx-1 small">${item.qty}</span>
                        <button type="button" class="btn btn-light btn-xs p-1" style="width:20px; height:20px; line-height:10px;" onclick="adjustQty(${item.id}, '${item.type}', 1)">+</button>
                    </div>
                </td>
                <td>$${(item.price * item.qty).toFixed(2)}</td>
                <td class="text-end">
                    <button type="button" class="btn btn-outline-danger btn-xs" onclick="removeFromCart(${item.id}, '${item.type}')"><i class="bi bi-trash"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    recalculatePOSCart();
}

function recalculatePOSCart() {
    let subtotal = 0.0;
    cart.forEach(item => {
        subtotal += item.price * item.qty;
    });

    const taxRate = 0.15; // standard VAT
    const tax = subtotal * taxRate;
    
    const discountInput = document.getElementById('posDiscount');
    const discount = parseFloat(discountInput.value) || 0.0;
    
    const total = Math.max(0.0, (subtotal + tax) - discount);

    document.getElementById('posSubtotal').textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById('posTax').textContent = `$${tax.toFixed(2)}`;
    document.getElementById('posDiscountVal').textContent = `-$${discount.toFixed(2)}`;
    document.getElementById('posTotal').textContent = `$${total.toFixed(2)}`;

    // Inject JSON cart into form
    document.getElementById('posItemsInput').value = JSON.stringify(cart);
}

function toggleSplitPayment() {
    const payMethod = document.getElementById('payMethod').value;
    const splitCol = document.getElementById('splitPaymentCol');
    if (payMethod === 'wallet') {
        splitCol.style.display = '';
    } else {
        splitCol.style.display = 'none';
        document.querySelector('#splitPaymentCol select').value = '';
    }
}
</script>
