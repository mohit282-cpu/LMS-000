<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Repositories\AuditLogRepository;
use App\Repositories\CourseRepository;
use InvalidArgumentException;

final class CourseService
{
    public function __construct(
        private readonly CourseRepository $courses = new CourseRepository(),
        private readonly AuditLogRepository $auditLogs = new AuditLogRepository()
    ) {
    }

    public function create(array $data): int
    {
        if ($this->courses->findByCode((string) $data['code']) !== null) {
            throw new InvalidArgumentException('A course with this code already exists.');
        }

        $courseId = $this->courses->create([
            'code' => strtoupper(trim((string) $data['code'])),
            'title' => trim((string) $data['title']),
            'description' => $data['description'] !== '' ? trim((string) $data['description']) : null,
            'level' => $data['level'] !== '' ? trim((string) $data['level']) : null,
            'status' => $data['status'],
            'start_date' => $data['start_date'] !== '' ? $data['start_date'] : null,
            'end_date' => $data['end_date'] !== '' ? $data['end_date'] : null,
            'created_by' => Auth::id(),
        ]);

        $this->auditLogs->record(Auth::id(), 'courses.created', 'course', $courseId);

        return $courseId;
    }

    public function update(int $id, array $data): void
    {
        $existing = $this->courses->findByCode((string) $data['code']);

        if ($existing !== null && (int) $existing['id'] !== $id) {
            throw new InvalidArgumentException('A course with this code already exists.');
        }

        $this->courses->update($id, [
            'code' => strtoupper(trim((string) $data['code'])),
            'title' => trim((string) $data['title']),
            'description' => $data['description'] !== '' ? trim((string) $data['description']) : null,
            'level' => $data['level'] !== '' ? trim((string) $data['level']) : null,
            'status' => $data['status'],
            'start_date' => $data['start_date'] !== '' ? $data['start_date'] : null,
            'end_date' => $data['end_date'] !== '' ? $data['end_date'] : null,
        ]);

        $this->auditLogs->record(Auth::id(), 'courses.updated', 'course', $id);
    }

    public function archive(int $id): void
    {
        $this->courses->archive($id);
        $this->auditLogs->record(Auth::id(), 'courses.archived', 'course', $id);
    }
}
