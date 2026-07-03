<?php $errors = \App\Core\Session::peekFlash('errors', []); ?>
<form class="card border-0 shadow-sm" method="post" action="<?= e(url($action)) ?>" novalidate>
    <?= csrf_field() ?>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="code">Code</label>
                <input class="form-control" id="code" name="code" value="<?= e(old('code', $course['code'] ?? '')) ?>" required>
                <?= field_error($errors, 'code') ?>
            </div>
            <div class="col-md-8">
                <label class="form-label" for="title">Title</label>
                <input class="form-control" id="title" name="title" value="<?= e(old('title', $course['title'] ?? '')) ?>" required>
                <?= field_error($errors, 'title') ?>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="level">Level</label>
                <input class="form-control" id="level" name="level" value="<?= e(old('level', $course['level'] ?? '')) ?>">
                <?= field_error($errors, 'level') ?>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="start_date">Start Date</label>
                <input class="form-control" id="start_date" name="start_date" type="date" value="<?= e(old('start_date', $course['start_date'] ?? '')) ?>">
                <?= field_error($errors, 'start_date') ?>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="end_date">End Date</label>
                <input class="form-control" id="end_date" name="end_date" type="date" value="<?= e(old('end_date', $course['end_date'] ?? '')) ?>">
                <?= field_error($errors, 'end_date') ?>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="status">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <?php foreach (['active', 'draft', 'archived'] as $status): ?>
                        <option value="<?= e($status) ?>" <?= selected(old('status', $course['status'] ?? 'draft'), $status) ?>><?= e(ucfirst($status)) ?></option>
                    <?php endforeach; ?>
                </select>
                <?= field_error($errors, 'status') ?>
            </div>
            <div class="col-12">
                <label class="form-label" for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5"><?= e(old('description', $course['description'] ?? '')) ?></textarea>
                <?= field_error($errors, 'description') ?>
            </div>
        </div>
    </div>
    <div class="card-footer bg-transparent d-flex justify-content-end gap-2">
        <a class="btn btn-outline-secondary" href="<?= e(url('/courses')) ?>">Cancel</a>
        <button class="btn btn-primary d-inline-flex align-items-center gap-2" type="submit">
            <i data-lucide="save"></i>
            <span>Save</span>
        </button>
    </div>
</form>

