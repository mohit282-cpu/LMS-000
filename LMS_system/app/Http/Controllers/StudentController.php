<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Repositories\StudentRepository;
use App\Services\StudentService;
use InvalidArgumentException;

final class StudentController extends Controller
{
    private StudentRepository $students;

    public function __construct(\App\Core\Request $request)
    {
        parent::__construct($request);
        $this->students = new StudentRepository();
    }

    public function index(): void
    {
        $this->requirePermission('students.view');

        $search = trim((string) $this->request->query('search', ''));
        $page = (int) $this->request->query('page', 1);

        $this->view('students/index', [
            'title' => 'Students',
            'students' => $this->students->paginate($search, $page),
            'search' => $search,
        ]);
    }

    public function create(): void
    {
        $this->requirePermission('students.create');

        $this->view('students/form', [
            'title' => 'Create Student',
            'student' => null,
            'action' => '/students',
        ]);
    }

    public function store(): void
    {
        $this->requirePermission('students.create');
        $this->validateCsrf();

        $data = $this->payload();
        $errors = $this->validate($data, true);

        if ($errors !== []) {
            unset($data['password']);
            $this->backWithErrors($errors, $data, '/students/create');
        }

        try {
            (new StudentService())->create($data);
            Session::flash('success', 'Student created successfully.');
            $this->redirect('/students');
        } catch (InvalidArgumentException $exception) {
            unset($data['password']);
            $this->backWithErrors(['general' => [$exception->getMessage()]], $data, '/students/create');
        }
    }

    public function edit(string $id): void
    {
        $this->requirePermission('students.update');

        $student = $this->students->find((int) $id);

        if ($student === null) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Student not found']);
            return;
        }

        $this->view('students/form', [
            'title' => 'Edit Student',
            'student' => $student,
            'action' => '/students/' . (int) $id,
        ]);
    }

    public function update(string $id): void
    {
        $this->requirePermission('students.update');
        $this->validateCsrf();

        $data = $this->payload();
        $errors = $this->validate($data, false);

        if ($errors !== []) {
            unset($data['password']);
            $this->backWithErrors($errors, $data, '/students/' . (int) $id . '/edit');
        }

        try {
            (new StudentService())->update((int) $id, $data);
            Session::flash('success', 'Student updated successfully.');
            $this->redirect('/students');
        } catch (InvalidArgumentException $exception) {
            unset($data['password']);
            $this->backWithErrors(['general' => [$exception->getMessage()]], $data, '/students/' . (int) $id . '/edit');
        }
    }

    public function deactivate(string $id): void
    {
        $this->requirePermission('students.delete');
        $this->validateCsrf();

        try {
            (new StudentService())->deactivate((int) $id);
            Session::flash('success', 'Student deactivated successfully.');
        } catch (InvalidArgumentException $exception) {
            Session::flash('error', $exception->getMessage());
        }

        $this->redirect('/students');
    }

    private function payload(): array
    {
        return $this->request->only([
            'name',
            'email',
            'phone',
            'password',
            'admission_number',
            'program',
            'batch',
            'roll_number',
            'date_of_birth',
            'gender',
            'address',
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
            'admission_number' => 'required|max:60',
            'program' => 'required|max:190',
            'batch' => 'nullable|max:60',
            'roll_number' => 'nullable|max:60',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|max:500',
            'status' => 'required|in:active,inactive,graduated,suspended',
        ]);
    }
}

