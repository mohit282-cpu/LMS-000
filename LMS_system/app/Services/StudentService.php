<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Repositories\AuditLogRepository;
use App\Repositories\RoleRepository;
use App\Repositories\StudentRepository;
use App\Repositories\UserRepository;
use InvalidArgumentException;

final class StudentService
{
    public function __construct(
        private readonly StudentRepository $students = new StudentRepository(),
        private readonly UserRepository $users = new UserRepository(),
        private readonly RoleRepository $roles = new RoleRepository(),
        private readonly AuditLogRepository $auditLogs = new AuditLogRepository()
    ) {
    }

    public function create(array $data): int
    {
        if ($this->users->findByEmail((string) $data['email']) !== null) {
            throw new InvalidArgumentException('A user with this email already exists.');
        }

        if ($this->students->findByAdmissionNumber((string) $data['admission_number']) !== null) {
            throw new InvalidArgumentException('A student with this admission number already exists.');
        }

        $studentRole = $this->roles->findBySlug('student');

        if ($studentRole === null) {
            throw new InvalidArgumentException('The student role is missing. Run the database seed first.');
        }

        return (int) Database::transaction(function () use ($data, $studentRole): int {
            $userId = $this->users->create([
                'name' => trim((string) $data['name']),
                'email' => trim((string) $data['email']),
                'phone' => $data['phone'] !== '' ? trim((string) $data['phone']) : null,
                'password_hash' => password_hash((string) $data['password'], PASSWORD_DEFAULT),
                'status' => $this->userStatus((string) $data['status']),
            ]);
            $this->users->setRoles($userId, [(int) $studentRole['id']]);

            $studentId = $this->students->create([
                'user_id' => $userId,
                'admission_number' => strtoupper(trim((string) $data['admission_number'])),
                'program' => trim((string) $data['program']),
                'batch' => $data['batch'] !== '' ? trim((string) $data['batch']) : null,
                'roll_number' => $data['roll_number'] !== '' ? trim((string) $data['roll_number']) : null,
                'date_of_birth' => $data['date_of_birth'] !== '' ? $data['date_of_birth'] : null,
                'gender' => $data['gender'] !== '' ? $data['gender'] : null,
                'address' => $data['address'] !== '' ? trim((string) $data['address']) : null,
                'status' => $data['status'],
            ]);

            $this->auditLogs->record(Auth::id(), 'students.created', 'student', $studentId);

            return $studentId;
        });
    }

    public function update(int $id, array $data): void
    {
        $student = $this->students->find($id);

        if ($student === null) {
            throw new InvalidArgumentException('Student not found.');
        }

        $existing = $this->users->findByEmail((string) $data['email']);

        if ($existing !== null && (int) $existing['id'] !== (int) $student['user_id']) {
            throw new InvalidArgumentException('A user with this email already exists.');
        }

        $duplicateAdmission = $this->students->findByAdmissionNumber((string) $data['admission_number']);

        if ($duplicateAdmission !== null && (int) $duplicateAdmission['id'] !== $id) {
            throw new InvalidArgumentException('A student with this admission number already exists.');
        }

        Database::transaction(function () use ($id, $data, $student): void {
            $userData = [
                'name' => trim((string) $data['name']),
                'email' => trim((string) $data['email']),
                'phone' => $data['phone'] !== '' ? trim((string) $data['phone']) : null,
                'status' => $this->userStatus((string) $data['status']),
            ];

            if (! empty($data['password'])) {
                $userData['password_hash'] = password_hash((string) $data['password'], PASSWORD_DEFAULT);
            }

            $this->users->update((int) $student['user_id'], $userData);
            $this->students->update($id, [
                'admission_number' => strtoupper(trim((string) $data['admission_number'])),
                'program' => trim((string) $data['program']),
                'batch' => $data['batch'] !== '' ? trim((string) $data['batch']) : null,
                'roll_number' => $data['roll_number'] !== '' ? trim((string) $data['roll_number']) : null,
                'date_of_birth' => $data['date_of_birth'] !== '' ? $data['date_of_birth'] : null,
                'gender' => $data['gender'] !== '' ? $data['gender'] : null,
                'address' => $data['address'] !== '' ? trim((string) $data['address']) : null,
                'status' => $data['status'],
            ]);

            $this->auditLogs->record(Auth::id(), 'students.updated', 'student', $id);
        });
    }

    public function deactivate(int $id): void
    {
        $student = $this->students->find($id);

        if ($student === null) {
            throw new InvalidArgumentException('Student not found.');
        }

        Database::transaction(function () use ($id, $student): void {
            $this->students->deactivate($id);
            $this->users->deactivate((int) $student['user_id']);
            $this->auditLogs->record(Auth::id(), 'students.deactivated', 'student', $id);
        });
    }

    private function userStatus(string $studentStatus): string
    {
        return $studentStatus === 'active' ? 'active' : 'inactive';
    }
}
