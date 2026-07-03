<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class TeacherRepository
{
    public function paginate(string $search = '', int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $perPage = max(5, min($perPage, 100));
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = 'WHERE 1 = 1';

        if ($search !== '') {
            $where .= ' AND (users.name LIKE :search OR users.email LIKE :search OR teachers.employee_number LIKE :search OR teachers.specialization LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        $countStatement = Database::connection()->prepare(
            "SELECT COUNT(*)
             FROM teachers
             INNER JOIN users ON users.id = teachers.user_id
             {$where}"
        );
        $countStatement->execute($params);
        $total = (int) $countStatement->fetchColumn();

        $statement = Database::connection()->prepare(
            "SELECT teachers.*, users.name, users.email, users.phone
             FROM teachers
             INNER JOIN users ON users.id = teachers.user_id
             {$where}
             ORDER BY teachers.created_at DESC
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
            'SELECT teachers.*, users.name, users.email, users.phone
             FROM teachers
             INNER JOIN users ON users.id = teachers.user_id
             WHERE teachers.id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $teacher = $statement->fetch(PDO::FETCH_ASSOC);

        return $teacher ?: null;
    }

    public function findByEmployeeNumber(string $employeeNumber): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT * FROM teachers WHERE employee_number = :employee_number LIMIT 1'
        );
        $statement->execute(['employee_number' => strtoupper(trim($employeeNumber))]);
        $teacher = $statement->fetch(PDO::FETCH_ASSOC);

        return $teacher ?: null;
    }

    public function create(array $data): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO teachers (user_id, employee_number, specialization, qualification, hire_date, status, created_at, updated_at)
             VALUES (:user_id, :employee_number, :specialization, :qualification, :hire_date, :status, NOW(), NOW())'
        );
        $statement->execute($data);

        return (int) Database::connection()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $statement = Database::connection()->prepare(
            'UPDATE teachers
             SET employee_number = :employee_number,
                 specialization = :specialization,
                 qualification = :qualification,
                 hire_date = :hire_date,
                 status = :status,
                 updated_at = NOW()
             WHERE id = :id'
        );
        $statement->execute($data);
    }

    public function deactivate(int $id): void
    {
        $statement = Database::connection()->prepare(
            "UPDATE teachers SET status = 'inactive', updated_at = NOW() WHERE id = :id"
        );
        $statement->execute(['id' => $id]);
    }

    public function countActive(): int
    {
        $statement = Database::connection()->query("SELECT COUNT(*) FROM teachers WHERE status = 'active'");

        return (int) $statement->fetchColumn();
    }
}
