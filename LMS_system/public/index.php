<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\View;

define('BASE_PATH', dirname(__DIR__));

try {
    $router = require BASE_PATH . '/bootstrap/app.php';
    $router->dispatch();
} catch (Throwable $exception) {
    http_response_code(500);

    $debug = class_exists(Config::class) && Config::get('app.debug', false);

    if ($debug) {
        echo '<pre>';
        echo htmlspecialchars((string) $exception, ENT_QUOTES, 'UTF-8');
        echo '</pre>';
        exit;
    }

    if (class_exists(View::class)) {
        echo View::render('errors/500', ['title' => 'Server Error'], 'auth');
        exit;
    }

    echo 'Server Error';
}

