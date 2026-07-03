<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class RoleRepository
{
    public function all(): array
    {
        return Database::connection()
            ->query('SELECT * FROM roles ORDER BY name ASC')
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allWithPermissionCounts(): array
    {
        return Database::connection()
            ->query(
                'SELECT roles.*, COUNT(role_permissions.permission_id) AS permission_count
                 FROM roles
                 LEFT JOIN role_permissions ON role_permissions.role_id = roles.id
                 GROUP BY roles.id
                 ORDER BY roles.name ASC'
            )
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allPermissions(): array
    {
        return Database::connection()
            ->query('SELECT * FROM permissions ORDER BY name ASC')
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $statement = Database::connection()->prepare('SELECT * FROM roles WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $role = $statement->fetch(PDO::FETCH_ASSOC);

        return $role ?: null;
    }

    public function findBySlug(string $slug): ?array
    {
        $statement = Database::connection()->prepare('SELECT * FROM roles WHERE slug = :slug LIMIT 1');
        $statement->execute(['slug' => $slug]);
        $role = $statement->fetch(PDO::FETCH_ASSOC);

        return $role ?: null;
    }

    public function create(array $data): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO roles (name, slug, description, is_system, created_at, updated_at)
             VALUES (:name, :slug, :description, :is_system, NOW(), NOW())'
        );
        $statement->execute([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'is_system' => $data['is_system'] ?? 0,
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $statement = Database::connection()->prepare(
            'UPDATE roles
             SET name = :name,
                 slug = :slug,
                 description = :description,
                 updated_at = NOW()
             WHERE id = :id'
        );
        $statement->execute([
            'id' => $id,
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
        ]);
    }

    public function setPermissions(int $roleId, array $permissionIds): void
    {
        Database::connection()->prepare('DELETE FROM role_permissions WHERE role_id = :role_id')
            ->execute(['role_id' => $roleId]);

        if ($permissionIds === []) {
            return;
        }

        $statement = Database::connection()->prepare(
            'INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)'
        );

        foreach (array_unique(array_map('intval', $permissionIds)) as $permissionId) {
            $statement->execute([
                'role_id' => $roleId,
                'permission_id' => $permissionId,
            ]);
        }
    }

    public function permissionIds(int $roleId): array
    {
        $statement = Database::connection()->prepare(
            'SELECT permission_id FROM role_permissions WHERE role_id = :role_id'
        );
        $statement->execute(['role_id' => $roleId]);

        return array_map('intval', $statement->fetchAll(PDO::FETCH_COLUMN));
    }

    public function count(): int
    {
        $statement = Database::connection()->query('SELECT COUNT(*) FROM roles');

        return (int) $statement->fetchColumn();
    }
}

