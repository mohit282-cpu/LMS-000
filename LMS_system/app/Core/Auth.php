<?php

declare(strict_types=1);

namespace App\Core;

use App\Repositories\UserRepository;

final class Auth
{
    private static ?array $user = null;
    private static ?array $permissions = null;

    public static function id(): ?int
    {
        $id = Session::get('user_id');

        return $id === null ? null : (int) $id;
    }

    public static function check(): bool
    {
        return self::id() !== null && self::user() !== null;
    }

    public static function user(): ?array
    {
        if (self::$user !== null) {
            return self::$user;
        }

        $id = self::id();

        if ($id === null) {
            return null;
        }

        self::$user = (new UserRepository())->find($id);

        if (self::$user === null || self::$user['status'] !== 'active') {
            self::logoutLocal();
            return null;
        }

        return self::$user;
    }

    public static function can(string $permission): bool
    {
        if (! self::check()) {
            return false;
        }

        if (self::$permissions === null) {
            self::$permissions = (new UserRepository())->permissions((int) self::id());
        }

        return in_array($permission, self::$permissions, true);
    }

    public static function login(int $userId): void
    {
        Session::regenerate();
        Session::put('user_id', $userId);
        self::$user = null;
        self::$permissions = null;
    }

    public static function logoutLocal(): void
    {
        Session::forget('user_id');
        self::$user = null;
        self::$permissions = null;
    }
}

