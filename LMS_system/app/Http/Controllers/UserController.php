<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\UserService;
use InvalidArgumentException;

final class UserController extends Controller
{
    private UserRepository $users;
    private RoleRepository $roles;

    public function __construct(\App\Core\Request $request)
    {
        parent::__construct($request);
        $this->users = new UserRepository();
        $this->roles = new RoleRepository();
    }

    public function index(): void
    {
        $this->requirePermission('users.view');

        $search = trim((string) $this->request->query('search', ''));
        $page = (int) $this->request->query('page', 1);

        $this->view('users/index', [
            'title' => 'Users',
            'users' => $this->users->paginate($search, $page),
            'search' => $search,
        ]);
    }

    public function create(): void
    {
        $this->requirePermission('users.create');

        $this->view('users/form', [
            'title' => 'Create User',
            'user' => null,
            'roles' => $this->roles->all(),
            'selectedRoles' => [],
            'action' => '/users',
        ]);
    }

    public function store(): void
    {
        $this->requirePermission('users.create');
        $this->validateCsrf();

        $data = $this->payload();
        $errors = $this->validate($data, true);

        if ($errors !== []) {
            unset($data['password']);
            $this->backWithErrors($errors, $data, '/users/create');
        }

        try {
            (new UserService())->create($data);
            Session::flash('success', 'User created successfully.');
            $this->redirect('/users');
        } catch (InvalidArgumentException $exception) {
            unset($data['password']);
            $this->backWithErrors(['general' => [$exception->getMessage()]], $data, '/users/create');
        }
    }

    public function edit(string $id): void
    {
        $this->requirePermission('users.update');

        $user = $this->users->find((int) $id);

        if ($user === null) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'User not found']);
            return;
        }

        $this->view('users/form', [
            'title' => 'Edit User',
            'user' => $user,
            'roles' => $this->roles->all(),
            'selectedRoles' => $this->users->roleIds((int) $id),
            'action' => '/users/' . (int) $id,
        ]);
    }

    public function update(string $id): void
    {
        $this->requirePermission('users.update');
        $this->validateCsrf();

        $data = $this->payload();
        $errors = $this->validate($data, false);

        if ($errors !== []) {
            unset($data['password']);
            $this->backWithErrors($errors, $data, '/users/' . (int) $id . '/edit');
        }

        try {
            (new UserService())->update((int) $id, $data);
            Session::flash('success', 'User updated successfully.');
            $this->redirect('/users');
        } catch (InvalidArgumentException $exception) {
            unset($data['password']);
            $this->backWithErrors(['general' => [$exception->getMessage()]], $data, '/users/' . (int) $id . '/edit');
        }
    }

    public function deactivate(string $id): void
    {
        $this->requirePermission('users.delete');
        $this->validateCsrf();

        try {
            (new UserService())->deactivate((int) $id);
            Session::flash('success', 'User deactivated successfully.');
        } catch (InvalidArgumentException $exception) {
            Session::flash('error', $exception->getMessage());
        }

        $this->redirect('/users');
    }

    private function payload(): array
    {
        $data = $this->request->only(['name', 'email', 'phone', 'password', 'status']);
        $roles = $this->request->post('roles', []);
        $data['roles'] = is_array($roles) ? $roles : [];

        return $data;
    }

    private function validate(array $data, bool $creating): array
    {
        return Validator::make($data, [
            'name' => 'required|max:190',
            'email' => 'required|email|max:190',
            'phone' => 'nullable|max:40',
            'password' => $creating ? 'required|min:8|max:255' : 'nullable|min:8|max:255',
            'status' => 'required|in:active,inactive,locked',
        ]);
    }
}

