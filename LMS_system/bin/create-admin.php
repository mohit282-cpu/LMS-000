<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Database;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from the command line.\n");
    exit(1);
}

$basePath = dirname(__DIR__);

require $basePath . '/bootstrap/autoload.php';

Config::load($basePath);

function ask(string $question): string
{
    $value = readline($question . ': ');

    return trim((string) $value);
}

$envPath = $basePath . DIRECTORY_SEPARATOR . '.env';

if (! is_file($envPath)) {
    fwrite(STDERR, "Create .env from .env.example before running this script.\n");
    exit(1);
}

$name = ask('Name');
$email = ask('Email');
$password = ask('Password');
$confirmPassword = ask('Confirm password');

if ($name === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    fwrite(STDERR, "A valid name and email are required.\n");
    exit(1);
}

if (strlen($password) < 8 || $password !== $confirmPassword) {
    fwrite(STDERR, "Password must be at least 8 characters and match confirmation.\n");
    exit(1);
}

$users = new UserRepository();
$roles = new RoleRepository();

if ($users->findByEmail($email) !== null) {
    fwrite(STDERR, "A user with this email already exists.\n");
    exit(1);
}

$role = $roles->findBySlug('super_admin');

if ($role === null) {
    fwrite(STDERR, "The super_admin role is missing. Import database/seed.sql first.\n");
    exit(1);
}

Database::transaction(function () use ($users, $role, $name, $email, $password): void {
    $userId = $users->create([
        'organization_id' => 1,
        'name' => $name,
        'email' => $email,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'phone' => null,
        'status' => 'active',
        'email_verified_at' => date('Y-m-d H:i:s'),
    ]);

    $users->setRoles($userId, [(int) $role['id']]);
});

fwrite(STDOUT, "Super administrator created successfully.\n");

