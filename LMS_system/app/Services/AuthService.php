<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Session;
use App\Repositories\AuditLogRepository;
use App\Repositories\UserRepository;

final class AuthService
{
    public function __construct(
        private readonly UserRepository $users = new UserRepository(),
        private readonly AuditLogRepository $auditLogs = new AuditLogRepository()
    ) {
    }

    public function attempt(string $email, string $password): bool
    {
        $user = $this->users->findByEmail($email);

        if ($user === null || $user['status'] !== 'active') {
            $this->auditLogs->record(null, 'auth.login_failed', 'user', null, ['email' => $email]);
            return false;
        }

        if (! password_verify($password, $user['password_hash'])) {
            $this->auditLogs->record((int) $user['id'], 'auth.login_failed', 'user', (int) $user['id']);
            return false;
        }

        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            $this->users->updatePasswordHash((int) $user['id'], password_hash($password, PASSWORD_DEFAULT));
        }

        Auth::login((int) $user['id']);
        $this->users->updateLastLogin((int) $user['id']);
        $this->auditLogs->record((int) $user['id'], 'auth.login_success', 'user', (int) $user['id']);

        return true;
    }

    public function logout(): void
    {
        $userId = Auth::id();

        if ($userId !== null) {
            $this->auditLogs->record($userId, 'auth.logout', 'user', $userId);
        }

        Auth::logoutLocal();
        Session::destroy();
    }
}

