<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/', [DashboardController::class, 'index']);

$router->get('/users', [UserController::class, 'index']);
$router->get('/users/create', [UserController::class, 'create']);
$router->post('/users', [UserController::class, 'store']);
$router->get('/users/{id}/edit', [UserController::class, 'edit']);
$router->post('/users/{id}', [UserController::class, 'update']);
$router->post('/users/{id}/deactivate', [UserController::class, 'deactivate']);

$router->get('/roles', [RoleController::class, 'index']);
$router->get('/roles/create', [RoleController::class, 'create']);
$router->post('/roles', [RoleController::class, 'store']);
$router->get('/roles/{id}/edit', [RoleController::class, 'edit']);
$router->post('/roles/{id}', [RoleController::class, 'update']);

$router->get('/students', [StudentController::class, 'index']);
$router->get('/students/create', [StudentController::class, 'create']);
$router->post('/students', [StudentController::class, 'store']);
$router->get('/students/{id}/edit', [StudentController::class, 'edit']);
$router->post('/students/{id}', [StudentController::class, 'update']);
$router->post('/students/{id}/deactivate', [StudentController::class, 'deactivate']);

$router->get('/teachers', [TeacherController::class, 'index']);
$router->get('/teachers/create', [TeacherController::class, 'create']);
$router->post('/teachers', [TeacherController::class, 'store']);
$router->get('/teachers/{id}/edit', [TeacherController::class, 'edit']);
$router->post('/teachers/{id}', [TeacherController::class, 'update']);
$router->post('/teachers/{id}/deactivate', [TeacherController::class, 'deactivate']);

$router->get('/courses', [CourseController::class, 'index']);
$router->get('/courses/create', [CourseController::class, 'create']);
$router->post('/courses', [CourseController::class, 'store']);
$router->get('/courses/{id}/edit', [CourseController::class, 'edit']);
$router->post('/courses/{id}', [CourseController::class, 'update']);
$router->post('/courses/{id}/archive', [CourseController::class, 'archive']);

