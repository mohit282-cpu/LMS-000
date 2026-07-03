<?php
$errors = \App\Core\Session::peekFlash('errors', []);
$isEdit = $student !== null;
?>
<form class="card border-0 shadow-sm" method="post" action="<?= e(url($action)) ?>" novalidate>
    <?= csrf_field() ?>
    <div class="card-body">
        <h2 class="h6 mb-3">Account</h2>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label" for="name">Name</label>
                <input class="form-control" id="name" name="name" value="<?= e(old('name', $student['name'] ?? '')) ?>" required>
                <?= field_error($errors, 'name') ?>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="email">Email</label>
                <input class="form-control" id="email" name="email" type="email" value="<?= e(old('email', $student['email'] ?? '')) ?>" required>
                <?= field_error($errors, 'email') ?>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="phone">Phone</label>
                <input class="form-control" id="phone" name="phone" value="<?= e(old('phone', $student['phone'] ?? '')) ?>">
                <?= field_error($errors, 'phone') ?>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="password"><?= $isEdit ? 'New Password' : 'Password' ?></label>
                <input class="form-control" id="password" name="password" type="password" autocomplete="new-password" <?= $isEdit ? '' : 'required' ?>>
                <?= field_error($errors, 'password') ?>
            </div>
        </div>

        <h2 class="h6 mb-3">Student Profile</h2>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="admission_number">Admission Number</label>
                <input class="form-control" id="admission_number" name="admission_number" value="<?= e(old('admission_number', $student['admission_number'] ?? '')) ?>" required>
                <?= field_error($errors, 'admission_number') ?>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="program">Program</label>
                <input class="form-control" id="program" name="program" value="<?= e(old('program', $student['program'] ?? '')) ?>" required>
                <?= field_error($errors, 'program') ?>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="batch">Batch</label>
                <input class="form-control" id="batch" name="batch" value="<?= e(old('batch', $student['batch'] ?? '')) ?>">
                <?= field_error($errors, 'batch') ?>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="roll_number">Roll Number</label>
                <input class="form-control" id="roll_number" name="roll_number" value="<?= e(old('roll_number', $student['roll_number'] ?? '')) ?>">
                <?= field_error($errors, 'roll_number') ?>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="date_of_birth">Date of Birth</label>
                <input class="form-control" id="date_of_birth" name="date_of_birth" type="date" value="<?= e(old('date_of_birth', $student['date_of_birth'] ?? '')) ?>">
                <?= field_error($errors, 'date_of_birth') ?>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="gender">Gender</label>
                <select class="form-select" id="gender" name="gender">
                    <option value="">Select</option>
                    <?php foreach (['male', 'female', 'other'] as $gender): ?>
                        <option value="<?= e($gender) ?>" <?= selected(old('gender', $student['gender'] ?? ''), $gender) ?>><?= e(ucfirst($gender)) ?></option>
                    <?php endforeach; ?>
                </select>
                <?= field_error($errors, 'gender') ?>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="status">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <?php foreach (['active', 'inactive', 'graduated', 'suspended'] as $status): ?>
                        <option value="<?= e($status) ?>" <?= selected(old('status', $student['status'] ?? 'active'), $status) ?>><?= e(ucfirst($status)) ?></option>
                    <?php endforeach; ?>
                </select>
                <?= field_error($errors, 'status') ?>
            </div>
            <div class="col-12">
                <label class="form-label" for="address">Address</label>
                <textarea class="form-control" id="address" name="address" rows="3"><?= e(old('address', $student['address'] ?? '')) ?></textarea>
                <?= field_error($errors, 'address') ?>
            </div>
        </div>
    </div>
    <div class="card-footer bg-transparent d-flex justify-content-end gap-2">
        <a class="btn btn-outline-secondary" href="<?= e(url('/students')) ?>">Cancel</a>
        <button class="btn btn-primary d-inline-flex align-items-center gap-2" type="submit">
            <i data-lucide="save"></i>
            <span>Save</span>
        </button>
    </div>
</form>

