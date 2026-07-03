<?php $errors = \App\Core\Session::peekFlash('errors', []); ?>
<div class="card shadow-sm border-0">
    <div class="card-body p-4 p-sm-5">
        <div class="d-flex align-items-center gap-3 mb-4">
            <span class="stat-icon"><i data-lucide="school"></i></span>
            <div>
                <h1 class="h4 mb-1"><?= e(\App\Core\Config::get('app.name', 'Enterprise LMS')) ?></h1>
                <p class="text-secondary mb-0">Secure staff portal</p>
            </div>
        </div>

        <form method="post" action="<?= e(url('/login')) ?>" novalidate>
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input class="form-control" id="email" name="email" type="email" value="<?= e(old('email')) ?>" autocomplete="email" required>
                <?= field_error($errors, 'email') ?>
            </div>
            <div class="mb-4">
                <label class="form-label" for="password">Password</label>
                <input class="form-control" id="password" name="password" type="password" autocomplete="current-password" required>
                <?= field_error($errors, 'password') ?>
            </div>
            <button class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-2" type="submit">
                <i data-lucide="log-in"></i>
                <span>Sign in</span>
            </button>
        </form>
    </div>
</div>
