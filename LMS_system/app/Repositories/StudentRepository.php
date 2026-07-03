<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class StudentRepository
{
    public function paginate(string $search = '', int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $perPage = max(5, min($perPage, 100));
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = 'WHERE 1 = 1';

        if ($search !== '') {
            $where .= ' AND (users.name LIKE :search OR users.email LIKE :search OR students.admission_number LIKE :search OR students.program LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        $countStatement = Database::connection()->prepare(
            "SELECT COUNT(*)
             FROM students
             INNER JOIN users ON users.id = students.user_id
             {$where}"
        );
        $countStatement->execute($params);
        $total = (int) $countStatement->fetchColumn();

        $statement = Database::connection()->prepare(
            "SELECT students.*, users.name, users.email, users.phone
             FROM students
             INNER JOIN users ON users.id = students.user_id
             {$where}
             ORDER BY students.created_at DESC
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
            'SELECT students.*, users.name, users.email, users.phone
             FROM students
             INNER JOIN users ON users.id = students.user_id
             WHERE students.id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $student = $statement->fetch(PDO::FETCH_ASSOC);

        return $student ?: null;
    }

    public function findByAdmissionNumber(string $admissionNumber): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT * FROM students WHERE admission_number = :admission_number LIMIT 1'
        );
        $statement->execute(['admission_number' => strtoupper(trim($admissionNumber))]);
        $student = $statement->fetch(PDO::FETCH_ASSOC);

        return $student ?: null;
    }

    public function create(array $data): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO students (user_id, admission_number, program, batch, roll_number, date_of_birth, gender, address, status, created_at, updated_at)
             VALUES (:user_id, :admission_number, :program, :batch, :roll_number, :date_of_birth, :gender, :address, :status, NOW(), NOW())'
        );
        $statement->execute($data);

        return (int) Database::connection()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $statement = Database::connection()->prepare(
            'UPDATE students
             SET admission_number = :admission_number,
                 program = :program,
                 batch = :batch,
                 roll_number = :roll_number,
                 date_of_birth = :date_of_birth,
                 gender = :gender,
                 address = :address,
                 status = :status,
                 updated_at = NOW()
             WHERE id = :id'
        );
        $statement->execute($data);
    }

    public function deactivate(int $id): void
    {
        $statement = Database::connection()->prepare(
            "UPDATE students SET status = 'inactive', updated_at = NOW() WHERE id = :id"
        );
        $statement->execute(['id' => $id]);
    }

    public function countActive(): int
    {
        $statement = Database::connection()->query("SELECT COUNT(*) FROM students WHERE status = 'active'");

        return (int) $statement->fetchColumn();
    }
}
