<div class="d-flex flex-column flex-lg-row gap-3 align-items-lg-center justify-content-between mb-3">
    <form class="d-flex gap-2" method="get" action="<?= e(url('/courses')) ?>">
        <input class="form-control" type="search" name="search" value="<?= e($search) ?>" placeholder="Search courses">
        <button class="btn btn-outline-secondary btn-icon" type="submit" aria-label="Search">
            <i data-lucide="search"></i>
        </button>
    </form>
    <?php if (\App\Core\Auth::can('courses.create')): ?>
        <a class="btn btn-primary d-inline-flex align-items-center gap-2" href="<?= e(url('/courses/create')) ?>">
            <i data-lucide="plus"></i>
            <span>Course</span>
        </a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Title</th>
                    <th>Level</th>
                    <th>Dates</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($courses['data'] === []): ?>
                    <tr><td colspan="6" class="text-center text-secondary py-4">No courses found.</td></tr>
                <?php endif; ?>
                <?php foreach ($courses['data'] as $course): ?>
                    <tr>
                        <td><code><?= e($course['code']) ?></code></td>
                        <td>
                            <div class="fw-medium"><?= e($course['title']) ?></div>
                            <div class="small text-secondary"><?= e($course['creator_name'] ?? 'System') ?></div>
                        </td>
                        <td><?= e($course['level'] ?? '-') ?></td>
                        <td>
                            <?php if ($course['start_date'] || $course['end_date']): ?>
                                <span><?= $course['start_date'] ? e(date('M j, Y', strtotime($course['start_date']))) : 'Open' ?></span>
                                <span class="text-secondary">to</span>
                                <span><?= $course['end_date'] ? e(date('M j, Y', strtotime($course['end_date']))) : 'Open' ?></span>
                            <?php else: ?>
                                <span class="text-secondary">Open</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge text-bg-<?= $course['status'] === 'active' ? 'success' : ($course['status'] === 'draft' ? 'warning' : 'secondary') ?>"><?= e($course['status']) ?></span></td>
                        <td>
                            <div class="table-actions">
                                <?php if (\App\Core\Auth::can('courses.update')): ?>
                                    <a class="btn btn-outline-secondary btn-icon" href="<?= e(url('/courses/' . $course['id'] . '/edit')) ?>" aria-label="Edit">
                                        <i data-lucide="pencil"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (\App\Core\Auth::can('courses.delete') && $course['status'] !== 'archived'): ?>
                                    <form method="post" action="<?= e(url('/courses/' . $course['id'] . '/archive')) ?>" data-confirm="Archive this course?">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-outline-danger btn-icon" type="submit" aria-label="Archive">
                                            <i data-lucide="archive"></i>
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
        <span class="small text-secondary"><?= e($courses['total']) ?> records</span>
        <?= pagination_links($courses, '/courses', ['search' => $search]) ?>
    </div>
</div>

