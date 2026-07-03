<?php
$errors = \App\Core\Session::peekFlash('errors', []);
$isEdit = $user !== null;
$selected = old('roles', $selectedRoles ?? []);
$selected = is_array($selected) ? array_map('intval', $selected) : [];
?>
<form class="card border-0 shadow-sm" method="post" action="<?= e(url($action)) ?>" novalidate>
    <?= csrf_field() ?>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="name">Name</label>
                <input class="form-control" id="name" name="name" value="<?= e(old('name', $user['name'] ?? '')) ?>" required>
                <?= field_error($errors, 'name') ?>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="email">Email</label>
                <input class="form-control" id="email" name="email" type="email" value="<?= e(old('email', $user['email'] ?? '')) ?>" required>
                <?= field_error($errors, 'email') ?>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="phone">Phone</label>
                <input class="form-control" id="phone" name="phone" value="<?= e(old('phone', $user['phone'] ?? '')) ?>">
                <?= field_error($errors, 'phone') ?>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="status">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <?php foreach (['active', 'inactive', 'locked'] as $status): ?>
                        <option value="<?= e($status) ?>" <?= selected(old('status', $user['status'] ?? 'active'), $status) ?>><?= e(ucfirst($status)) ?></option>
                    <?php endforeach; ?>
                </select>
                <?= field_error($errors, 'status') ?>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="password"><?= $isEdit ? 'New Password' : 'Password' ?></label>
                <input class="form-control" id="password" name="password" type="password" autocomplete="new-password" <?= $isEdit ? '' : 'required' ?>>
                <?= field_error($errors, 'password') ?>
            </div>
            <div class="col-12">
                <label class="form-label">Roles</label>
                <div class="row g-2">
                    <?php foreach ($roles as $role): ?>
                        <div class="col-sm-6 col-lg-4">
                            <label class="form-check border rounded p-3 h-100">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="roles[]" value="<?= e($role['id']) ?>" <?= checked(in_array((int) $role['id'], $selected, true)) ?>>
                                <span class="form-check-label"><?= e($role['name']) ?></span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer bg-transparent d-flex justify-content-end gap-2">
        <a class="btn btn-outline-secondary" href="<?= e(url('/users')) ?>">Cancel</a>
        <button class="btn btn-primary d-inline-flex align-items-center gap-2" type="submit">
            <i data-lucide="save"></i>
            <span>Save</span>
        </button>
    </div>
</form>

