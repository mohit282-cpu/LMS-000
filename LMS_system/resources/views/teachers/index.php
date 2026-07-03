<div class="d-flex flex-column flex-lg-row gap-3 align-items-lg-center justify-content-between mb-3">
    <form class="d-flex gap-2" method="get" action="<?= e(url('/teachers')) ?>">
        <input class="form-control" type="search" name="search" value="<?= e($search) ?>" placeholder="Search teachers">
        <button class="btn btn-outline-secondary btn-icon" type="submit" aria-label="Search">
            <i data-lucide="search"></i>
        </button>
    </form>
    <?php if (\App\Core\Auth::can('teachers.create')): ?>
        <a class="btn btn-primary d-inline-flex align-items-center gap-2" href="<?= e(url('/teachers/create')) ?>">
            <i data-lucide="plus"></i>
            <span>Teacher</span>
        </a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Employee No.</th>
                    <th>Specialization</th>
                    <th>Hire Date</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($teachers['data'] === []): ?>
                    <tr><td colspan="6" class="text-center text-secondary py-4">No teachers found.</td></tr>
                <?php endif; ?>
                <?php foreach ($teachers['data'] as $teacher): ?>
                    <tr>
                        <td>
                            <div class="fw-medium"><?= e($teacher['name']) ?></div>
                            <div class="small text-secondary"><?= e($teacher['email']) ?></div>
                        </td>
                        <td><?= e($teacher['employee_number']) ?></td>
                        <td><?= e($teacher['specialization'] ?? '-') ?></td>
                        <td><?= $teacher['hire_date'] ? e(date('M j, Y', strtotime($teacher['hire_date']))) : '<span class="text-secondary">-</span>' ?></td>
                        <td><span class="badge text-bg-<?= $teacher['status'] === 'active' ? 'success' : 'secondary' ?>"><?= e($teacher['status']) ?></span></td>
                        <td>
                            <div class="table-actions">
                                <?php if (\App\Core\Auth::can('teachers.update')): ?>
                                    <a class="btn btn-outline-secondary btn-icon" href="<?= e(url('/teachers/' . $teacher['id'] . '/edit')) ?>" aria-label="Edit">
                                        <i data-lucide="pencil"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (\App\Core\Auth::can('teachers.delete') && $teacher['status'] === 'active'): ?>
                                    <form method="post" action="<?= e(url('/teachers/' . $teacher['id'] . '/deactivate')) ?>" data-confirm="Deactivate this teacher?">
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
        <span class="small text-secondary"><?= e($teachers['total']) ?> records</span>
        <?= pagination_links($teachers, '/teachers', ['search' => $search]) ?>
    </div>
</div>

