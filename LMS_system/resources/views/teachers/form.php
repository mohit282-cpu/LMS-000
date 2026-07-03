<?php
$errors = \App\Core\Session::peekFlash('errors', []);
$isEdit = $teacher !== null;
?>
<form class="card border-0 shadow-sm" method="post" action="<?= e(url($action)) ?>" novalidate>
    <?= csrf_field() ?>
    <div class="card-body">
        <h2 class="h6 mb-3">Account</h2>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label" for="name">Name</label>
                <input class="form-control" id="name" name="name" value="<?= e(old('name', $teacher['name'] ?? '')) ?>" required>
                <?= field_error($errors, 'name') ?>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="email">Email</label>
                <input class="form-control" id="email" name="email" type="email" value="<?= e(old('email', $teacher['email'] ?? '')) ?>" required>
                <?= field_error($errors, 'email') ?>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="phone">Phone</label>
                <input class="form-control" id="phone" name="phone" value="<?= e(old('phone', $teacher['phone'] ?? '')) ?>">
                <?= field_error($errors, 'phone') ?>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="password"><?= $isEdit ? 'New Password' : 'Password' ?></label>
                <input class="form-control" id="password" name="password" type="password" autocomplete="new-password" <?= $isEdit ? '' : 'required' ?>>
                <?= field_error($errors, 'password') ?>
            </div>
        </div>

        <h2 class="h6 mb-3">Teacher Profile</h2>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="employee_number">Employee Number</label>
                <input class="form-control" id="employee_number" name="employee_number" value="<?= e(old('employee_number', $teacher['employee_number'] ?? '')) ?>" required>
                <?= field_error($errors, 'employee_number') ?>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="specialization">Specialization</label>
                <input class="form-control" id="specialization" name="specialization" value="<?= e(old('specialization', $teacher['specialization'] ?? '')) ?>">
                <?= field_error($errors, 'specialization') ?>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="qualification">Qualification</label>
                <input class="form-control" id="qualification" name="qualification" value="<?= e(old('qualification', $teacher['qualification'] ?? '')) ?>">
                <?= field_error($errors, 'qualification') ?>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="hire_date">Hire Date</label>
                <input class="form-control" id="hire_date" name="hire_date" type="date" value="<?= e(old('hire_date', $teacher['hire_date'] ?? '')) ?>">
                <?= field_error($errors, 'hire_date') ?>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="status">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <?php foreach (['active', 'inactive', 'on_leave', 'terminated'] as $status): ?>
                        <option value="<?= e($status) ?>" <?= selected(old('status', $teacher['status'] ?? 'active'), $status) ?>><?= e(str_replace('_', ' ', ucfirst($status))) ?></option>
                    <?php endforeach; ?>
                </select>
                <?= field_error($errors, 'status') ?>
            </div>
        </div>
    </div>
    <div class="card-footer bg-transparent d-flex justify-content-end gap-2">
        <a class="btn btn-outline-secondary" href="<?= e(url('/teachers')) ?>">Cancel</a>
        <button class="btn btn-primary d-inline-flex align-items-center gap-2" type="submit">
            <i data-lucide="save"></i>
            <span>Save</span>
        </button>
    </div>
</form>

