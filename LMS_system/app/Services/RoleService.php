<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Repositories\AuditLogRepository;
use App\Repositories\RoleRepository;
use InvalidArgumentException;

final class RoleService
{
    public function __construct(
        private readonly RoleRepository $roles = new RoleRepository(),
        private readonly AuditLogRepository $auditLogs = new AuditLogRepository()
    ) {
    }

    public function create(array $data): int
    {
        $slug = $this->slug((string) $data['slug']);

        if ($this->roles->findBySlug($slug) !== null) {
            throw new InvalidArgumentException('A role with this slug already exists.');
        }

        return (int) Database::transaction(function () use ($data, $slug): int {
            $roleId = $this->roles->create([
                'name' => trim((string) $data['name']),
                'slug' => $slug,
                'description' => $data['description'] !== '' ? trim((string) $data['description']) : null,
                'is_system' => 0,
            ]);

            $this->roles->setPermissions($roleId, $data['permissions'] ?? []);
            $this->auditLogs->record(Auth::id(), 'roles.created', 'role', $roleId);

            return $roleId;
        });
    }

    public function update(int $id, array $data): void
    {
        $existing = $this->roles->find($id);

        if ($existing === null) {
            throw new InvalidArgumentException('Role not found.');
        }

        $slug = $existing['is_system'] ? $existing['slug'] : $this->slug((string) $data['slug']);
        $duplicate = $this->roles->findBySlug($slug);

        if ($duplicate !== null && (int) $duplicate['id'] !== $id) {
            throw new InvalidArgumentException('A role with this slug already exists.');
        }

        Database::transaction(function () use ($id, $data, $slug): void {
            $this->roles->update($id, [
                'name' => trim((string) $data['name']),
                'slug' => $slug,
                'description' => $data['description'] !== '' ? trim((string) $data['description']) : null,
            ]);

            $this->roles->setPermissions($id, $data['permissions'] ?? []);
            $this->auditLogs->record(Auth::id(), 'roles.updated', 'role', $id);
        });
    }

    private function slug(string $value): string
    {
        $slug = strtolower(trim($value));
        $slug = preg_replace('/[^a-z0-9]+/', '_', $slug) ?: '';
        $slug = trim($slug, '_');

        if ($slug === '') {
            throw new InvalidArgumentException('Role slug is required.');
        }

        return $slug;
    }
}

