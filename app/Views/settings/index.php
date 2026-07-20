<?php use App\Core\Csrf; ?>
<div class="row g-4">
    <!-- Main Configuration panel -->
    <div class="col-lg-6">
        <!-- Language / Branch select card -->
        <div class="card card-soft mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-gear-fill me-2 text-primary"></i><?= __('system_config') ?></h5>
                
                <div class="mb-4">
                    <label class="form-label small fw-medium"><?= __('active_branch') ?></label>
                    <select class="form-select" disabled>
                        <option>Riyadh Main Branch (Olaya Street)</option>
                        <option>Jeddah North Branch (King Road)</option>
                        <option>Dammam Central Branch (Khobar Road)</option>
                    </select>
                    <small class="text-muted text-xs"><?= __('branch_locked_hint') ?></small>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-medium"><?= __('system_locale') ?></label>
                    <div class="d-flex gap-2">
                        <a href="/settings/set_lang?lang=en" class="btn btn-outline-secondary flex-grow-1 <?= \App\Services\LanguageService::getLocale() === 'en' ? 'active' : '' ?>">English</a>
                        <a href="/settings/set_lang?lang=ar" class="btn btn-outline-secondary flex-grow-1 <?= \App\Services\LanguageService::getLocale() === 'ar' ? 'active' : '' ?>">العربية</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Database Backups -->
        <div class="card card-soft">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-database-fill-gear me-2 text-warning"></i><?= __('db_backups') ?></h5>
                    <form method="post" action="/settings/backup" style="display:inline;">
                        <?= Csrf::tokenField() ?>
                        <button class="btn btn-warning btn-sm"><i class="bi bi-cloud-arrow-down-fill me-1"></i><?= __('create_backup') ?></button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm align-middle text-sm">
                        <thead>
                            <tr>
                                <th><?= __('backup_file') ?></th>
                                <th><?= __('size') ?></th>
                                <th><?= __('date') ?></th>
                                <th class="text-end"><?= __('action') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($backups)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-3"><?= __('no_backups') ?></td></tr>
                            <?php else: ?>
                                <?php foreach ($backups as $b): ?>
                                    <tr>
                                        <td class="fw-semibold text-truncate" style="max-width:180px;"><i class="bi bi-file-earmark-code me-1 text-primary"></i><?= htmlspecialchars($b['filename']) ?></td>
                                        <td><?= htmlspecialchars($b['size']) ?></td>
                                        <td class="text-muted text-xs"><?= htmlspecialchars($b['created_at']) ?></td>
                                        <td class="text-end">
                                            <form method="post" action="/settings/restore" style="display:inline;" onsubmit="return confirm('<?= __('restore_confirm') ?>');">
                                                <?= Csrf::tokenField() ?>
                                                <input type="hidden" name="filename" value="<?= $b['filename'] ?>">
                                                <button class="btn btn-danger btn-xs"><i class="bi bi-arrow-counterclockwise me-1"></i><?= __('restore') ?></button>
                                            </form>
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

    <!-- Audit Logs Section -->
    <div class="col-lg-6">
        <div class="card card-soft">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-shield-check me-2 text-success"></i><?= __('audit_activity') ?></h5>
                <div class="d-flex flex-column gap-3" style="max-height: 480px; overflow-y: auto;">
                    <?php if (empty($auditLogs)): ?>
                        <div class="text-center py-5 text-muted"><?= __('no_audit_logs') ?></div>
                    <?php else: ?>
                        <?php foreach ($auditLogs as $log): ?>
                            <div class="d-flex gap-2 text-sm border-bottom pb-2">
                                <div class="stat-icon bg-secondary bg-opacity-10 text-secondary border rounded-circle flex-shrink-0" style="width:34px; height:34px;"><i class="bi bi-shield-lock small"></i></div>
                                <div>
                                    <div>
                                        <strong><?= htmlspecialchars($log['user_name'] ?? 'System') ?></strong> 
                                        (<span class="text-capitalize text-muted text-xs"><?= str_replace('_', ' ', htmlspecialchars($log['user_role'] ?? 'Guest')) ?></span>)
                                    </div>
                                    <div class="text-dark my-1 text-xs">
                                        <?= __('audit_action_on') ?>: <span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($log['action']) ?></span> on <code><?= htmlspecialchars($log['entity_type']) ?></code>
                                    </div>
                                    <?php if ($log['details']): ?>
                                        <div class="bg-body-tertiary p-2 rounded text-xs text-muted font-monospace text-wrap mb-1" style="max-width:380px;">
                                            <?= htmlspecialchars($log['details']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <small class="text-muted text-xs d-block"><?= date('Y-m-d H:i:s', strtotime($log['created_at'])) ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
