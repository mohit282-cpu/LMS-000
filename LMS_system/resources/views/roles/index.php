<div class="d-flex justify-content-end mb-3">
    <?php if (\App\Core\Auth::can('roles.manage')): ?>
        <a class="btn btn-primary d-inline-flex align-items-center gap-2" href="<?= e(url('/roles/create')) ?>">
            <i data-lucide="plus"></i>
            <span>Role</span>
        </a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Permissions</th>
                    <th>Type</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($roles === []): ?>
                    <tr><td colspan="5" class="text-center text-secondary py-4">No roles found.</td></tr>
                <?php endif; ?>
                <?php foreach ($roles as $role): ?>
                    <tr>
                        <td class="fw-medium"><?= e($role['name']) ?></td>
                        <td><code><?= e($role['slug']) ?></code></td>
                        <td><?= e($role['permission_count']) ?></td>
                        <td><span class="badge <?= $role['is_system'] ? 'text-bg-primary' : 'badge-soft' ?>"><?= $role['is_system'] ? 'System' : 'Custom' ?></span></td>
                        <td>
                            <div class="table-actions">
                                <?php if (\App\Core\Auth::can('roles.manage')): ?>
                                    <a class="btn btn-outline-secondary btn-icon" href="<?= e(url('/roles/' . $role['id'] . '/edit')) ?>" aria-label="Edit">
                                        <i data-lucide="pencil"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

