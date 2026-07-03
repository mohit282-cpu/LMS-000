<?php

use App\Core\Config;
use App\Core\Session;

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
    <title><?= e(($title ?? 'Sign in') . ' - ' . Config::get('app.name', 'Enterprise LMS')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= e(asset('css/app.css')) ?>" rel="stylesheet">
</head>
<body>
<main class="auth-page p-3">
    <section class="auth-panel">
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
    </section>
</main>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>

