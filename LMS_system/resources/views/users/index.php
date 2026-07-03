<div class="d-flex flex-column flex-lg-row gap-3 align-items-lg-center justify-content-between mb-3">
    <form class="d-flex gap-2" method="get" action="<?= e(url('/users')) ?>">
        <input class="form-control" type="search" name="search" value="<?= e($search) ?>" placeholder="Search users">
        <button class="btn btn-outline-secondary btn-icon" type="submit" aria-label="Search">
            <i data-lucide="search"></i>
        </button>
    </form>
    <?php if (\App\Core\Auth::can('users.create')): ?>
        <a class="btn btn-primary d-inline-flex align-items-center gap-2" href="<?= e(url('/users/create')) ?>">
            <i data-lucide="plus"></i>
            <span>User</span>
        </a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($users['data'] === []): ?>
                    <tr><td colspan="6" class="text-center text-secondary py-4">No users found.</td></tr>
                <?php endif; ?>
                <?php foreach ($users['data'] as $user): ?>
                    <tr>
                        <td class="fw-medium"><?= e($user['name']) ?></td>
                        <td><?= e($user['email']) ?></td>
                        <td><?= e($user['roles'] ?: 'Unassigned') ?></td>
                        <td><span class="badge text-bg-<?= $user['status'] === 'active' ? 'success' : 'secondary' ?>"><?= e($user['status']) ?></span></td>
                        <td><?= $user['last_login_at'] ? e(date('M j, Y H:i', strtotime($user['last_login_at']))) : '<span class="text-secondary">Never</span>' ?></td>
                        <td>
                            <div class="table-actions">
                                <?php if (\App\Core\Auth::can('users.update')): ?>
                                    <a class="btn btn-outline-secondary btn-icon" href="<?= e(url('/users/' . $user['id'] . '/edit')) ?>" aria-label="Edit">
                                        <i data-lucide="pencil"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (\App\Core\Auth::can('users.delete') && $user['status'] === 'active'): ?>
                                    <form method="post" action="<?= e(url('/users/' . $user['id'] . '/deactivate')) ?>" data-confirm="Deactivate this user?">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-outline-danger btn-icon" type="submit" aria-label="Deactivate">
                                            <i data-lucide="user-x"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
        <span class="small text-secondary"><?= e($users['total']) ?> records</span>
        <?= pagination_links($users, '/users', ['search' => $search]) ?>
    </div>
</div>

