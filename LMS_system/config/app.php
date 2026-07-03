<?php

declare(strict_types=1);

return [
    'name' => getenv('APP_NAME') ?: 'Enterprise LMS',
    'env' => getenv('APP_ENV') ?: 'production',
    'debug' => filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOL),
    'url' => rtrim((string) (getenv('APP_URL') ?: ''), '/'),
    'timezone' => getenv('APP_TIMEZONE') ?: 'UTC',
];

