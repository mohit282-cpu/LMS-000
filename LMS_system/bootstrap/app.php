<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Request;
use App\Core\Router;
use App\Core\Session;

require __DIR__ . '/autoload.php';
require dirname(__DIR__) . '/app/helpers.php';

$basePath = dirname(__DIR__);

Config::load($basePath);

date_default_timezone_set((string) Config::get('app.timezone', 'UTC'));

Session::start();

$router = new Router(new Request());

require $basePath . '/routes/web.php';

return $router;

