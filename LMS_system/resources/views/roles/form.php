<?php
$errors = \App\Core\Session::peekFlash('errors', []);
$isSystem = $role !== null && (bool) $role['is_system'];
$selected = old('permissions', $selectedPermissions ?? []);
$selected = is_array($selected) ? array_map('intval', $selected) : [];
?>
<form class="card border-0 shadow-sm" method="post" action="<?= e(url($action)) ?>" novalidate>
    <?= csrf_field() ?>
    <div class="card-body">
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label" for="name">Name</label>
                <input class="form-control" id="name" name="name" value="<?= e(old('name', $role['name'] ?? '')) ?>" required>
                <?= field_error($errors, 'name') ?>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="slug">Slug</label>
                <input class="form-control" id="slug" name="slug" value="<?= e(old('slug', $role['slug'] ?? '')) ?>" <?= $isSystem ? 'readonly' : '' ?> required>
                <?= field_error($errors, 'slug') ?>
            </div>
            <div class="col-12">
                <label class="form-label" for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= e(old('description', $role['description'] ?? '')) ?></textarea>
                <?= field_error($errors, 'description') ?>
            </div>
        </div>

        <div class="row g-2">
            <?php foreach ($permissions as $permission): ?>
                <div class="col-sm-6 col-lg-4">
                    <label class="form-check border rounded p-3 h-100">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="permissions[]" value="<?= e($permission['id']) ?>" <?= checked(in_array((int) $permission['id'], $selected, true)) ?>>
                        <span class="form-check-label"><code><?= e($permission['name']) ?></code></span>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="card-footer bg-transparent d-flex justify-content-end gap-2">
        <a class="btn btn-outline-secondary" href="<?= e(url('/roles')) ?>">Cancel</a>
        <button class="btn btn-primary d-inline-flex align-items-center gap-2" type="submit">
            <i data-lucide="save"></i>
            <span>Save</span>
        </button>
    </div>
</form>

