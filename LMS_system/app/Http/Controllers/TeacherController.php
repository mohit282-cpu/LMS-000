<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Repositories\TeacherRepository;
use App\Services\TeacherService;
use InvalidArgumentException;

final class TeacherController extends Controller
{
    private TeacherRepository $teachers;

    public function __construct(\App\Core\Request $request)
    {
        parent::__construct($request);
        $this->teachers = new TeacherRepository();
    }

    public function index(): void
    {
        $this->requirePermission('teachers.view');

        $search = trim((string) $this->request->query('search', ''));
        $page = (int) $this->request->query('page', 1);

        $this->view('teachers/index', [
            'title' => 'Teachers',
            'teachers' => $this->teachers->paginate($search, $page),
            'search' => $search,
        ]);
    }

    public function create(): void
    {
        $this->requirePermission('teachers.create');

        $this->view('teachers/form', [
            'title' => 'Create Teacher',
            'teacher' => null,
            'action' => '/teachers',
        ]);
    }

    public function store(): void
    {
        $this->requirePermission('teachers.create');
        $this->validateCsrf();

        $data = $this->payload();
        $errors = $this->validate($data, true);

        if ($errors !== []) {
            unset($data['password']);
            $this->backWithErrors($errors, $data, '/teachers/create');
        }

        try {
            (new TeacherService())->create($data);
            Session::flash('success', 'Teacher created successfully.');
            $this->redirect('/teachers');
        } catch (InvalidArgumentException $exception) {
            unset($data['password']);
            $this->backWithErrors(['general' => [$exception->getMessage()]], $data, '/teachers/create');
        }
    }

    public function edit(string $id): void
    {
        $this->requirePermission('teachers.update');

        $teacher = $this->teachers->find((int) $id);

        if ($teacher === null) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Teacher not found']);
            return;
        }

        $this->view('teachers/form', [
            'title' => 'Edit Teacher',
            'teacher' => $teacher,
            'action' => '/teachers/' . (int) $id,
        ]);
    }

    public function update(string $id): void
    {
        $this->requirePermission('teachers.update');
        $this->validateCsrf();

        $data = $this->payload();
        $errors = $this->validate($data, false);

        if ($errors !== []) {
            unset($data['password']);
            $this->backWithErrors($errors, $data, '/teachers/' . (int) $id . '/edit');
        }

        try {
            (new TeacherService())->update((int) $id, $data);
            Session::flash('success', 'Teacher updated successfully.');
            $this->redirect('/teachers');
        } catch (InvalidArgumentException $exception) {
            unset($data['password']);
            $this->backWithErrors(['general' => [$exception->getMessage()]], $data, '/teachers/' . (int) $id . '/edit');
        }
    }

    public function deactivate(string $id): void
    {
        $this->requirePermission('teachers.delete');
        $this->validateCsrf();

        try {
            (new TeacherService())->deactivate((int) $id);
            Session::flash('success', 'Teacher deactivated successfully.');
        } catch (InvalidArgumentException $exception) {
            Session::flash('error', $exception->getMessage());
        }

        $this->redirect('/teachers');
    }

    private function payload(): array
    {
        return $this->request->only([
            'name',
            'email',
            'phone',
            'password',
            'employee_number',
            'specialization',
            'qualification',
            'hire_date',
            'status',
        ]);
    }

    private function validate(array $data, bool $creating): array
    {
        return Validator::make($data, [
            'name' => 'required|max:190',
            'email' => 'required|email|max:190',
            'phone' => 'nullable|max:40',
            'password' => $creating ? 'required|min:8|max:255' : 'nullable|min:8|max:255',
            'employee_number' => 'required|max:60',
            'specialization' => 'nullable|max:190',
            'qualification' => 'nullable|max:190',
            'hire_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,on_leave,terminated',
        ]);
    }
}

