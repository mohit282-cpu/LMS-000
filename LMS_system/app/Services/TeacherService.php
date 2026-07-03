<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Repositories\AuditLogRepository;
use App\Repositories\RoleRepository;
use App\Repositories\TeacherRepository;
use App\Repositories\UserRepository;
use InvalidArgumentException;

final class TeacherService
{
    public function __construct(
        private readonly TeacherRepository $teachers = new TeacherRepository(),
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

        if ($this->teachers->findByEmployeeNumber((string) $data['employee_number']) !== null) {
            throw new InvalidArgumentException('A teacher with this employee number already exists.');
        }

        $teacherRole = $this->roles->findBySlug('teacher');

        if ($teacherRole === null) {
            throw new InvalidArgumentException('The teacher role is missing. Run the database seed first.');
        }

        return (int) Database::transaction(function () use ($data, $teacherRole): int {
            $userId = $this->users->create([
                'name' => trim((string) $data['name']),
                'email' => trim((string) $data['email']),
                'phone' => $data['phone'] !== '' ? trim((string) $data['phone']) : null,
                'password_hash' => password_hash((string) $data['password'], PASSWORD_DEFAULT),
                'status' => $this->userStatus((string) $data['status']),
            ]);
            $this->users->setRoles($userId, [(int) $teacherRole['id']]);

            $teacherId = $this->teachers->create([
                'user_id' => $userId,
                'employee_number' => strtoupper(trim((string) $data['employee_number'])),
                'specialization' => $data['specialization'] !== '' ? trim((string) $data['specialization']) : null,
                'qualification' => $data['qualification'] !== '' ? trim((string) $data['qualification']) : null,
                'hire_date' => $data['hire_date'] !== '' ? $data['hire_date'] : null,
                'status' => $data['status'],
            ]);

            $this->auditLogs->record(Auth::id(), 'teachers.created', 'teacher', $teacherId);

            return $teacherId;
        });
    }

    public function update(int $id, array $data): void
    {
        $teacher = $this->teachers->find($id);

        if ($teacher === null) {
            throw new InvalidArgumentException('Teacher not found.');
        }

        $existing = $this->users->findByEmail((string) $data['email']);

        if ($existing !== null && (int) $existing['id'] !== (int) $teacher['user_id']) {
            throw new InvalidArgumentException('A user with this email already exists.');
        }

        $duplicateEmployee = $this->teachers->findByEmployeeNumber((string) $data['employee_number']);

        if ($duplicateEmployee !== null && (int) $duplicateEmployee['id'] !== $id) {
            throw new InvalidArgumentException('A teacher with this employee number already exists.');
        }

        Database::transaction(function () use ($id, $data, $teacher): void {
            $userData = [
                'name' => trim((string) $data['name']),
                'email' => trim((string) $data['email']),
                'phone' => $data['phone'] !== '' ? trim((string) $data['phone']) : null,
                'status' => $this->userStatus((string) $data['status']),
            ];

            if (! empty($data['password'])) {
                $userData['password_hash'] = password_hash((string) $data['password'], PASSWORD_DEFAULT);
            }

            $this->users->update((int) $teacher['user_id'], $userData);
            $this->teachers->update($id, [
                'employee_number' => strtoupper(trim((string) $data['employee_number'])),
                'specialization' => $data['specialization'] !== '' ? trim((string) $data['specialization']) : null,
                'qualification' => $data['qualification'] !== '' ? trim((string) $data['qualification']) : null,
                'hire_date' => $data['hire_date'] !== '' ? $data['hire_date'] : null,
                'status' => $data['status'],
            ]);

            $this->auditLogs->record(Auth::id(), 'teachers.updated', 'teacher', $id);
        });
    }

    public function deactivate(int $id): void
    {
        $teacher = $this->teachers->find($id);

        if ($teacher === null) {
            throw new InvalidArgumentException('Teacher not found.');
        }

        Database::transaction(function () use ($id, $teacher): void {
            $this->teachers->deactivate($id);
            $this->users->deactivate((int) $teacher['user_id']);
            $this->auditLogs->record(Auth::id(), 'teachers.deactivated', 'teacher', $id);
        });
    }

    private function userStatus(string $teacherStatus): string
    {
        return $teacherStatus === 'active' ? 'active' : 'inactive';
    }
}
