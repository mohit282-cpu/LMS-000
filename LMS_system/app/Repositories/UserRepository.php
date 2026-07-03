<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class UserRepository
{
    public function paginate(string $search = '', int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $perPage = max(5, min($perPage, 100));
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = 'WHERE 1 = 1';

        if ($search !== '') {
            $where .= ' AND (users.name LIKE :search OR users.email LIKE :search OR users.phone LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        $countStatement = Database::connection()->prepare("SELECT COUNT(*) FROM users {$where}");
        $countStatement->execute($params);
        $total = (int) $countStatement->fetchColumn();

        $statement = Database::connection()->prepare(
            "SELECT users.id, users.name, users.email, users.phone, users.status, users.last_login_at, users.created_at,
                    GROUP_CONCAT(roles.name ORDER BY roles.name SEPARATOR ', ') AS roles
             FROM users
             LEFT JOIN user_roles ON user_roles.user_id = users.id
             LEFT JOIN roles ON roles.id = user_roles.role_id
             {$where}
             GROUP BY users.id
             ORDER BY users.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}"
        );
        $statement->execute($params);

        return [
            'data' => $statement->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    public function find(int $id): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, organization_id, name, email, password_hash, phone, status, email_verified_at, last_login_at, created_at, updated_at
             FROM users
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, organization_id, name, email, password_hash, phone, status, email_verified_at, last_login_at, created_at, updated_at
             FROM users
             WHERE email = :email
             LIMIT 1'
        );
        $statement->execute(['email' => strtolower(trim($email))]);
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function create(array $data): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO users (organization_id, name, email, password_hash, phone, status, email_verified_at, created_at, updated_at)
             VALUES (:organization_id, :name, :email, :password_hash, :phone, :status, :email_verified_at, NOW(), NOW())'
        );
        $statement->execute([
            'organization_id' => $data['organization_id'] ?? null,
            'name' => $data['name'],
            'email' => strtolower(trim($data['email'])),
            'password_hash' => $data['password_hash'],
            'phone' => $data['phone'] ?? null,
            'status' => $data['status'] ?? 'active',
            'email_verified_at' => $data['email_verified_at'] ?? null,
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $fields = [
            'name = :name',
            'email = :email',
            'phone = :phone',
            'status = :status',
            'updated_at = NOW()',
        ];

        $params = [
            'id' => $id,
            'name' => $data['name'],
            'email' => strtolower(trim($data['email'])),
            'phone' => $data['phone'] ?? null,
            'status' => $data['status'] ?? 'active',
        ];

        if (! empty($data['password_hash'])) {
            $fields[] = 'password_hash = :password_hash';
            $params['password_hash'] = $data['password_hash'];
        }

        $statement = Database::connection()->prepare(
            'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id'
        );
        $statement->execute($params);
    }

    public function updateLastLogin(int $id): void
    {
        $statement = Database::connection()->prepare(
            'UPDATE users SET last_login_at = NOW(), updated_at = NOW() WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
    }

    public function updatePasswordHash(int $id, string $passwordHash): void
    {
        $statement = Database::connection()->prepare(
            'UPDATE users SET password_hash = :password_hash, updated_at = NOW() WHERE id = :id'
        );
        $statement->execute([
            'id' => $id,
            'password_hash' => $passwordHash,
        ]);
    }

    public function setRoles(int $userId, array $roleIds): void
    {
        Database::connection()->prepare('DELETE FROM user_roles WHERE user_id = :user_id')
            ->execute(['user_id' => $userId]);

        if ($roleIds === []) {
            return;
        }

        $statement = Database::connection()->prepare(
            'INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)'
        );

        foreach (array_unique(array_map('intval', $roleIds)) as $roleId) {
            $statement->execute([
                'user_id' => $userId,
                'role_id' => $roleId,
            ]);
        }
    }

    public function roleIds(int $userId): array
    {
        $statement = Database::connection()->prepare('SELECT role_id FROM user_roles WHERE user_id = :user_id');
        $statement->execute(['user_id' => $userId]);

        return array_map('intval', $statement->fetchAll(PDO::FETCH_COLUMN));
    }

    public function permissions(int $userId): array
    {
        $statement = Database::connection()->prepare(
            "SELECT DISTINCT permissions.name
             FROM permissions
             INNER JOIN role_permissions ON role_permissions.permission_id = permissions.id
             INNER JOIN user_roles ON user_roles.role_id = role_permissions.role_id
             INNER JOIN users ON users.id = user_roles.user_id
             WHERE users.id = :user_id AND users.status = 'active'"
        );
        $statement->execute(['user_id' => $userId]);

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    public function deactivate(int $id): void
    {
        $statement = Database::connection()->prepare(
            "UPDATE users SET status = 'inactive', updated_at = NOW() WHERE id = :id"
        );
        $statement->execute(['id' => $id]);
    }

    public function countActive(): int
    {
        $statement = Database::connection()->query("SELECT COUNT(*) FROM users WHERE status = 'active'");

        return (int) $statement->fetchColumn();
    }
}
