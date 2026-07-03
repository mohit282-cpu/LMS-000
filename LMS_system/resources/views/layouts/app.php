<?php

use App\Core\Auth;
use App\Core\Config;
use App\Core\Session;

$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$user = Auth::user();
$modules = Config::get('modules', []);
$success = Session::getFlash('success');
$error = Session::getFlash('error');
$warning = Session::getFlash('warning');
$formErrors = Session::getFlash('errors', []);
Session::getFlash('old', []);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(($title ?? 'Dashboard') . ' - ' . Config::get('app.name', 'Enterprise LMS')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= e(asset('css/app.css')) ?>" rel="stylesheet">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar sidebar-fixed d-none d-lg-flex flex-column p-3">
        <div class="sidebar-brand d-flex align-items-center gap-2 px-2 fw-bold">
            <span class="stat-icon"><i data-lucide="school"></i></span>
            <span><?= e(Config::get('app.name', 'Enterprise LMS')) ?></span>
        </div>
        <nav class="nav flex-column gap-1 mt-3">
            <?php foreach ($modules as $module): ?>
                <?php if (Auth::can($module['permission'])): ?>
                    <?php $active = $module['route'] === '/' ? $currentPath === url('/') : str_starts_with($currentPath, url($module['route'])); ?>
                    <a class="nav-link <?= $active ? 'active' : '' ?>" href="<?= e(url($module['route'])) ?>">
                        <i data-lucide="<?= e($module['icon']) ?>"></i>
                        <span><?= e($module['name']) ?></span>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
    </aside>

    <div class="main-content">
        <header class="topbar sticky-top">
            <div class="container-fluid content-wrap px-4 h-100 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-secondary btn-icon d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-label="Open navigation">
                        <i data-lucide="menu"></i>
                    </button>
                    <div>
                        <h1 class="h5 mb-0"><?= e($title ?? 'Dashboard') ?></h1>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-secondary btn-icon" type="button" data-theme-toggle aria-label="Toggle theme">
                        <i data-lucide="moon"></i>
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary d-flex align-items-center gap-2" data-bs-toggle="dropdown" type="button">
                            <i data-lucide="circle-user-round"></i>
                            <span class="d-none d-sm-inline"><?= e($user['name'] ?? 'Account') ?></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <span class="dropdown-item-text small text-secondary"><?= e($user['email'] ?? '') ?></span>
                            <div class="dropdown-divider"></div>
                            <form method="post" action="<?= e(url('/logout')) ?>">
                                <?= csrf_field() ?>
                                <button class="dropdown-item" type="submit">Sign out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="container-fluid content-wrap p-4">
            <?php if ($success): ?>
                <div class="alert alert-success"><?= e($success) ?></div>
            <?php endif; ?>
            <?php if ($warning): ?>
                <div class="alert alert-warning"><?= e($warning) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>
            <?php if (isset($formErrors['general'][0])): ?>
                <div class="alert alert-danger"><?= e($formErrors['general'][0]) ?></div>
            <?php endif; ?>

            <?= $content ?>
        </main>
    </div>
</div>

<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileNav" aria-labelledby="mobileNavLabel">
    <div class="offcanvas-header">
        <h2 class="h6 mb-0" id="mobileNavLabel"><?= e(Config::get('app.name', 'Enterprise LMS')) ?></h2>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <nav class="sidebar nav flex-column gap-1 w-100 border-0 p-0">
            <?php foreach ($modules as $module): ?>
                <?php if (Auth::can($module['permission'])): ?>
                    <a class="nav-link" href="<?= e(url($module['route'])) ?>">
                        <i data-lucide="<?= e($module['icon']) ?>"></i>
                        <span><?= e($module['name']) ?></span>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>

