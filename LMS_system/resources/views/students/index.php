<div class="d-flex flex-column flex-lg-row gap-3 align-items-lg-center justify-content-between mb-3">
    <form class="d-flex gap-2" method="get" action="<?= e(url('/students')) ?>">
        <input class="form-control" type="search" name="search" value="<?= e($search) ?>" placeholder="Search students">
        <button class="btn btn-outline-secondary btn-icon" type="submit" aria-label="Search">
            <i data-lucide="search"></i>
        </button>
    </form>
    <?php if (\App\Core\Auth::can('students.create')): ?>
        <a class="btn btn-primary d-inline-flex align-items-center gap-2" href="<?= e(url('/students/create')) ?>">
            <i data-lucide="plus"></i>
            <span>Student</span>
        </a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Admission</th>
                    <th>Program</th>
                    <th>Batch</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($students['data'] === []): ?>
                    <tr><td colspan="6" class="text-center text-secondary py-4">No students found.</td></tr>
                <?php endif; ?>
                <?php foreach ($students['data'] as $student): ?>
                    <tr>
                        <td>
                            <div class="fw-medium"><?= e($student['name']) ?></div>
                            <div class="small text-secondary"><?= e($student['email']) ?></div>
                        </td>
                        <td><?= e($student['admission_number']) ?></td>
                        <td><?= e($student['program']) ?></td>
                        <td><?= e($student['batch'] ?? '-') ?></td>
                        <td><span class="badge text-bg-<?= $student['status'] === 'active' ? 'success' : 'secondary' ?>"><?= e($student['status']) ?></span></td>
                        <td>
                            <div class="table-actions">
                                <?php if (\App\Core\Auth::can('students.update')): ?>
                                    <a class="btn btn-outline-secondary btn-icon" href="<?= e(url('/students/' . $student['id'] . '/edit')) ?>" aria-label="Edit">
                                        <i data-lucide="pencil"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (\App\Core\Auth::can('students.delete') && $student['status'] === 'active'): ?>
                                    <form method="post" action="<?= e(url('/students/' . $student['id'] . '/deactivate')) ?>" data-confirm="Deactivate this student?">
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
        <span class="small text-secondary"><?= e($students['total']) ?> records</span>
        <?= pagination_links($students, '/students', ['search' => $search]) ?>
    </div>
</div>

