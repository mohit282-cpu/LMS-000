<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Repositories\CourseRepository;
use App\Services\CourseService;
use InvalidArgumentException;

final class CourseController extends Controller
{
    private CourseRepository $courses;

    public function __construct(\App\Core\Request $request)
    {
        parent::__construct($request);
        $this->courses = new CourseRepository();
    }

    public function index(): void
    {
        $this->requirePermission('courses.view');

        $search = trim((string) $this->request->query('search', ''));
        $page = (int) $this->request->query('page', 1);

        $this->view('courses/index', [
            'title' => 'Courses',
            'courses' => $this->courses->paginate($search, $page),
            'search' => $search,
        ]);
    }

    public function create(): void
    {
        $this->requirePermission('courses.create');

        $this->view('courses/form', [
            'title' => 'Create Course',
            'course' => null,
            'action' => '/courses',
        ]);
    }

    public function store(): void
    {
        $this->requirePermission('courses.create');
        $this->validateCsrf();

        $data = $this->payload();
        $errors = $this->validate($data);

        if ($errors !== []) {
            $this->backWithErrors($errors, $data, '/courses/create');
        }

        try {
            (new CourseService())->create($data);
            Session::flash('success', 'Course created successfully.');
            $this->redirect('/courses');
        } catch (InvalidArgumentException $exception) {
            $this->backWithErrors(['general' => [$exception->getMessage()]], $data, '/courses/create');
        }
    }

    public function edit(string $id): void
    {
        $this->requirePermission('courses.update');

        $course = $this->courses->find((int) $id);

        if ($course === null) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Course not found']);
            return;
        }

        $this->view('courses/form', [
            'title' => 'Edit Course',
            'course' => $course,
            'action' => '/courses/' . (int) $id,
        ]);
    }

    public function update(string $id): void
    {
        $this->requirePermission('courses.update');
        $this->validateCsrf();

        $data = $this->payload();
        $errors = $this->validate($data);

        if ($errors !== []) {
            $this->backWithErrors($errors, $data, '/courses/' . (int) $id . '/edit');
        }

        try {
            (new CourseService())->update((int) $id, $data);
            Session::flash('success', 'Course updated successfully.');
            $this->redirect('/courses');
        } catch (InvalidArgumentException $exception) {
            $this->backWithErrors(['general' => [$exception->getMessage()]], $data, '/courses/' . (int) $id . '/edit');
        }
    }

    public function archive(string $id): void
    {
        $this->requirePermission('courses.delete');
        $this->validateCsrf();

        (new CourseService())->archive((int) $id);
        Session::flash('success', 'Course archived successfully.');
        $this->redirect('/courses');
    }

    private function payload(): array
    {
        return $this->request->only([
            'code',
            'title',
            'description',
            'level',
            'status',
            'start_date',
            'end_date',
        ]);
    }

    private function validate(array $data): array
    {
        $errors = Validator::make($data, [
            'code' => 'required|max:50',
            'title' => 'required|max:190',
            'description' => 'nullable|max:5000',
            'level' => 'nullable|max:100',
            'status' => 'required|in:active,draft,archived',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        if (
            empty($errors['start_date'])
            && empty($errors['end_date'])
            && ! empty($data['start_date'])
            && ! empty($data['end_date'])
            && strtotime((string) $data['end_date']) < strtotime((string) $data['start_date'])
        ) {
            $errors['end_date'][] = 'End date must be after the start date.';
        }

        return $errors;
    }
}
