<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Repositories\AuditLogRepository;
use App\Repositories\UserRepository;
use InvalidArgumentException;

final class UserService
{
    public function __construct(
        private readonly UserRepository $users = new UserRepository(),
        private readonly AuditLogRepository $auditLogs = new AuditLogRepository()
    ) {
    }

    public function create(array $data): int
    {
        if ($this->users->findByEmail((string) $data['email']) !== null) {
            throw new InvalidArgumentException('A user with this email already exists.');
        }

        return (int) Database::transaction(function () use ($data): int {
            $userId = $this->users->create([
                'name' => trim((string) $data['name']),
                'email' => trim((string) $data['email']),
                'phone' => $data['phone'] !== '' ? trim((string) $data['phone']) : null,
                'password_hash' => password_hash((string) $data['password'], PASSWORD_DEFAULT),
                'status' => $data['status'],
            ]);

            $this->users->setRoles($userId, $data['roles'] ?? []);
            $this->auditLogs->record(Auth::id(), 'users.created', 'user', $userId);

            return $userId;
        });
    }

    public function update(int $id, array $data): void
    {
        $existing = $this->users->findByEmail((string) $data['email']);

        if ($existing !== null && (int) $existing['id'] !== $id) {
            throw new InvalidArgumentException('A user with this email already exists.');
        }

        Database::transaction(function () use ($id, $data): void {
            $userData = [
                'name' => trim((string) $data['name']),
                'email' => trim((string) $data['email']),
                'phone' => $data['phone'] !== '' ? trim((string) $data['phone']) : null,
                'status' => $data['status'],
            ];

            if (! empty($data['password'])) {
                $userData['password_hash'] = password_hash((string) $data['password'], PASSWORD_DEFAULT);
            }

            $this->users->update($id, $userData);
            $this->users->setRoles($id, $data['roles'] ?? []);
            $this->auditLogs->record(Auth::id(), 'users.updated', 'user', $id);
        });
    }

    public function deactivate(int $id): void
    {
        if (Auth::id() === $id) {
            throw new InvalidArgumentException('You cannot deactivate your own account.');
        }

        $this->users->deactivate($id);
        $this->auditLogs->record(Auth::id(), 'users.deactivated', 'user', $id);
    }
}

