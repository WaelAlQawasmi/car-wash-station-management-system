<?php use App\Core\Csrf; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0"><i class="bi bi-people-fill me-2 text-primary"></i><?= __('user_management') ?></h2>
        <small class="text-muted"><?= __('user_management_desc') ?></small>
    </div>
    <button class="btn btn-primary" id="btnNewUser">
        <i class="bi bi-person-plus-fill me-1"></i><?= __('new_user') ?>
    </button>
</div>

<!-- Users Table -->
<div class="card card-soft">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="usersTable">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4"><?= __('name') ?></th>
                        <th><?= __('email') ?></th>
                        <th><?= __('role') ?></th>
                        <th><?= __('status') ?></th>
                        <th><?= __('last_login') ?></th>
                        <th class="text-end pe-4"><?= __('actions') ?></th>
                    </tr>
                </thead>
                <tbody id="usersTbody">
                    <?php foreach ($users as $u): ?>
                    <tr id="user-row-<?= $u['id'] ?>">
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:36px;height:36px;">
                                    <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                </div>
                                <span class="fw-semibold"><?= htmlspecialchars($u['name']) ?></span>
                            </div>
                        </td>
                        <td class="text-muted"><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <span class="badge role-badge-<?= $u['role'] ?>"><?= htmlspecialchars(str_replace('_', ' ', $u['role'])) ?></span>
                        </td>
                        <td>
                            <?php if ($u['is_active']): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-circle-fill me-1 fs-8"></i><?= __('active') ?></span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-circle-fill me-1 fs-8"></i><?= __('inactive') ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted text-xs">
                            <?= $u['last_login_at'] ? date('Y-m-d H:i', strtotime($u['last_login_at'])) : '—' ?>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex gap-2 justify-content-end">
                                <button class="btn btn-outline-primary btn-sm btn-edit-user"
                                    data-id="<?= $u['id'] ?>"
                                    data-name="<?= htmlspecialchars($u['name'], ENT_QUOTES) ?>"
                                    data-email="<?= htmlspecialchars($u['email'], ENT_QUOTES) ?>"
                                    data-role="<?= $u['role'] ?>"
                                    data-active="<?= $u['is_active'] ?>">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button class="btn btn-outline-warning btn-sm btn-toggle-user"
                                    data-id="<?= $u['id'] ?>"
                                    title="<?= __('toggle_status') ?>">
                                    <i class="bi bi-toggle-<?= $u['is_active'] ? 'on text-success' : 'off text-warning' ?>"></i>
                                </button>
                                <?php if ($u['id'] != ($_SESSION['user']['id'] ?? 0)): ?>
                                <button class="btn btn-outline-danger btn-sm btn-delete-user"
                                    data-id="<?= $u['id'] ?>"
                                    data-name="<?= htmlspecialchars($u['name'], ENT_QUOTES) ?>">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create / Edit User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="userModalLabel"><i class="bi bi-person-gear me-2 text-primary"></i><?= __('new_user') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <div id="modalAlert" class="alert d-none mb-3" role="alert"></div>
                <form id="userForm" novalidate>
                    <input type="hidden" name="csrf_token" id="modalCsrf" value="<?= Csrf::getToken() ?>">
                    <input type="hidden" name="id" id="userId" value="">

                    <div class="mb-3">
                        <label class="form-label fw-medium"><?= __('full_name') ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="userName" placeholder="<?= __('full_name') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium"><?= __('email') ?> <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" id="userEmail" placeholder="<?= __('email') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium"><?= __('role') ?> <span class="text-danger">*</span></label>
                        <select class="form-select" name="role" id="userRole" required>
                            <option value=""><?= __('select_role') ?></option>
                            <option value="super_admin"><?= __('role_super_admin') ?></option>
                            <option value="admin"><?= __('role_admin') ?></option>
                            <option value="branch_manager"><?= __('role_branch_manager') ?></option>
                            <option value="cashier"><?= __('role_cashier') ?></option>
                            <option value="employee"><?= __('role_employee') ?></option>
                        </select>
                    </div>
                    <div class="mb-3" id="activeWrapper">
                        <label class="form-label fw-medium"><?= __('status') ?></label>
                        <select class="form-select" name="is_active" id="userActive">
                            <option value="1"><?= __('active') ?></option>
                            <option value="0"><?= __('inactive') ?></option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium" id="pwLabel"><?= __('password') ?> <span class="text-danger" id="pwRequired">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="password" id="userPassword" placeholder="<?= __('password_placeholder') ?>">
                            <button class="btn btn-outline-secondary" type="button" id="togglePw"><i class="bi bi-eye"></i></button>
                        </div>
                        <div class="form-text" id="pwHint"><?= __('password_hint_new') ?></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('cancel') ?></button>
                <button type="button" class="btn btn-primary" id="btnSaveUser">
                    <i class="bi bi-check2-circle me-1"></i><span id="btnSaveText"><?= __('save') ?></span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.role-badge-super_admin  { background:#6f42c120; color:#6f42c1; border:1px solid #6f42c140; }
.role-badge-admin        { background:#0d6efd20; color:#0d6efd; border:1px solid #0d6efd40; }
.role-badge-branch_manager { background:#0dcaf020; color:#0dcaf0; border:1px solid #0dcaf040; }
.role-badge-cashier      { background:#fd7e1420; color:#fd7e14; border:1px solid #fd7e1440; }
.role-badge-employee     { background:#19875420; color:#198754; border:1px solid #19875440; }
.fs-8 { font-size: 0.5rem; }
</style>

<script>
(function () {
    const csrfToken = () => document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const modal = new bootstrap.Modal(document.getElementById('userModal'));
    const form  = document.getElementById('userForm');
    const alert = document.getElementById('modalAlert');

    function showAlert(msg, type = 'danger') {
        alert.textContent = msg;
        alert.className = `alert alert-${type} mb-3`;
    }
    function hideAlert() { alert.className = 'alert d-none mb-3'; }

    // ---- Open modal for NEW user ----
    document.getElementById('btnNewUser').addEventListener('click', () => {
        document.getElementById('userModalLabel').innerHTML = '<i class="bi bi-person-plus-fill me-2 text-primary"></i><?= __('new_user') ?>';
        document.getElementById('userId').value = '';
        form.reset();
        document.getElementById('pwRequired').style.display = '';
        document.getElementById('pwHint').textContent = '<?= __('password_hint_new') ?>';
        document.getElementById('activeWrapper').style.display = 'none';
        hideAlert();
        modal.show();
    });

    // ---- Open modal for EDIT user ----
    document.querySelectorAll('.btn-edit-user').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('userModalLabel').innerHTML = '<i class="bi bi-pencil-fill me-2 text-warning"></i><?= __('edit_user') ?>';
            document.getElementById('userId').value   = btn.dataset.id;
            document.getElementById('userName').value = btn.dataset.name;
            document.getElementById('userEmail').value= btn.dataset.email;
            document.getElementById('userRole').value = btn.dataset.role;
            document.getElementById('userActive').value= btn.dataset.active;
            document.getElementById('userPassword').value = '';
            document.getElementById('pwRequired').style.display = 'none';
            document.getElementById('pwHint').textContent = '<?= __('password_hint_edit') ?>';
            document.getElementById('activeWrapper').style.display = '';
            hideAlert();
            modal.show();
        });
    });

    // ---- Toggle password visibility ----
    document.getElementById('togglePw').addEventListener('click', () => {
        const pw = document.getElementById('userPassword');
        const ico = document.querySelector('#togglePw i');
        if (pw.type === 'password') {
            pw.type = 'text';
            ico.className = 'bi bi-eye-slash';
        } else {
            pw.type = 'password';
            ico.className = 'bi bi-eye';
        }
    });

    // ---- Save user ----
    document.getElementById('btnSaveUser').addEventListener('click', async () => {
        hideAlert();
        const btn  = document.getElementById('btnSaveUser');
        const text = document.getElementById('btnSaveText');
        btn.disabled = true;
        text.textContent = '<?= __('saving') ?>...';

        const fd = new FormData(form);
        fd.set('csrf_token', csrfToken());

        try {
            const res = await fetch('/users/save', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: fd
            });
            const data = await res.json();
            if (data.success) {
                modal.hide();
                location.reload();
            } else {
                showAlert(data.message);
            }
        } catch (e) {
            showAlert('Network error. Please try again.');
        } finally {
            btn.disabled = false;
            text.textContent = '<?= __('save') ?>';
        }
    });

    // ---- Toggle active status ----
    document.querySelectorAll('.btn-toggle-user').forEach(btn => {
        btn.addEventListener('click', async () => {
            const fd = new FormData();
            fd.append('csrf_token', csrfToken());
            fd.append('id', btn.dataset.id);
            try {
                const res  = await fetch('/users/toggle', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: fd
                });
                const data = await res.json();
                if (data.success) location.reload();
                else alert(data.message);
            } catch (e) { alert('Network error'); }
        });
    });

    // ---- Delete user ----
    document.querySelectorAll('.btn-delete-user').forEach(btn => {
        btn.addEventListener('click', async () => {
            if (!confirm('<?= __('delete_user_confirm') ?> "' + btn.dataset.name + '"?')) return;
            const fd = new FormData();
            fd.append('csrf_token', csrfToken());
            fd.append('id', btn.dataset.id);
            try {
                const res  = await fetch('/users/delete', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: fd
                });
                const data = await res.json();
                if (data.success) {
                    document.getElementById('user-row-' + btn.dataset.id)?.remove();
                } else {
                    alert(data.message);
                }
            } catch (e) { alert('Network error'); }
        });
    });
})();
</script>
