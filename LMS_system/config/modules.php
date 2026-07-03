<?php

declare(strict_types=1);

return [
    ['name' => 'Dashboard', 'route' => '/', 'permission' => 'dashboard.view', 'icon' => 'layout-dashboard'],
    ['name' => 'Users', 'route' => '/users', 'permission' => 'users.view', 'icon' => 'users'],
    ['name' => 'Roles', 'route' => '/roles', 'permission' => 'roles.view', 'icon' => 'shield-check'],
    ['name' => 'Students', 'route' => '/students', 'permission' => 'students.view', 'icon' => 'graduation-cap'],
    ['name' => 'Teachers', 'route' => '/teachers', 'permission' => 'teachers.view', 'icon' => 'presentation'],
    ['name' => 'Courses', 'route' => '/courses', 'permission' => 'courses.view', 'icon' => 'book-open'],
];

