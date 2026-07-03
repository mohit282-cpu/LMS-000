<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class CourseRepository
{
    public function paginate(string $search = '', int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $perPage = max(5, min($perPage, 100));
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = 'WHERE 1 = 1';

        if ($search !== '') {
            $where .= ' AND (code LIKE :search OR title LIKE :search OR level LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        $countStatement = Database::connection()->prepare("SELECT COUNT(*) FROM courses {$where}");
        $countStatement->execute($params);
        $total = (int) $countStatement->fetchColumn();

        $statement = Database::connection()->prepare(
            "SELECT courses.*, users.name AS creator_name
             FROM courses
             LEFT JOIN users ON users.id = courses.created_by
             {$where}
             ORDER BY courses.created_at DESC
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
        $statement = Database::connection()->prepare('SELECT * FROM courses WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $course = $statement->fetch(PDO::FETCH_ASSOC);

        return $course ?: null;
    }

    public function findByCode(string $code): ?array
    {
        $statement = Database::connection()->prepare('SELECT * FROM courses WHERE code = :code LIMIT 1');
        $statement->execute(['code' => strtoupper(trim($code))]);
        $course = $statement->fetch(PDO::FETCH_ASSOC);

        return $course ?: null;
    }

    public function create(array $data): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO courses (code, title, description, level, status, start_date, end_date, created_by, created_at, updated_at)
             VALUES (:code, :title, :description, :level, :status, :start_date, :end_date, :created_by, NOW(), NOW())'
        );

        $statement->execute($data);

        return (int) Database::connection()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $statement = Database::connection()->prepare(
            'UPDATE courses
             SET code = :code,
                 title = :title,
                 description = :description,
                 level = :level,
                 status = :status,
                 start_date = :start_date,
                 end_date = :end_date,
                 updated_at = NOW()
             WHERE id = :id'
        );
        $statement->execute($data);
    }

    public function archive(int $id): void
    {
        $statement = Database::connection()->prepare(
            "UPDATE courses SET status = 'archived', updated_at = NOW() WHERE id = :id"
        );
        $statement->execute(['id' => $id]);
    }

    public function countActive(): int
    {
        $statement = Database::connection()->query("SELECT COUNT(*) FROM courses WHERE status = 'active'");

        return (int) $statement->fetchColumn();
    }
}
