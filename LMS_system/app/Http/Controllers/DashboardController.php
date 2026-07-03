<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Repositories\AuditLogRepository;
use App\Repositories\CourseRepository;
use App\Repositories\StudentRepository;
use App\Repositories\TeacherRepository;
use App\Repositories\UserRepository;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('dashboard.view');

        $auditLogs = new AuditLogRepository();

        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'stats' => [
                'users' => (new UserRepository())->countActive(),
                'students' => (new StudentRepository())->countActive(),
                'teachers' => (new TeacherRepository())->countActive(),
                'courses' => (new CourseRepository())->countActive(),
                'audit_today' => $auditLogs->countToday(),
            ],
            'recentLogs' => $auditLogs->recent(8),
        ]);
    }
}

