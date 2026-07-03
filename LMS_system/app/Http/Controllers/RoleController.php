<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Repositories\RoleRepository;
use App\Services\RoleService;
use InvalidArgumentException;

final class RoleController extends Controller
{
    private RoleRepository $roles;

    public function __construct(\App\Core\Request $request)
    {
        parent::__construct($request);
        $this->roles = new RoleRepository();
    }

    public function index(): void
    {
        $this->requirePermission('roles.view');

        $this->view('roles/index', [
            'title' => 'Roles',
            'roles' => $this->roles->allWithPermissionCounts(),
        ]);
    }

    public function create(): void
    {
        $this->requirePermission('roles.manage');

        $this->view('roles/form', [
            'title' => 'Create Role',
            'role' => null,
            'permissions' => $this->roles->allPermissions(),
            'selectedPermissions' => [],
            'action' => '/roles',
        ]);
    }

    public function store(): void
    {
        $this->requirePermission('roles.manage');
        $this->validateCsrf();

        $data = $this->payload();
        $errors = $this->validate($data);

        if ($errors !== []) {
            $this->backWithErrors($errors, $data, '/roles/create');
        }

        try {
            (new RoleService())->create($data);
            Session::flash('success', 'Role created successfully.');
            $this->redirect('/roles');
        } catch (InvalidArgumentException $exception) {
            $this->backWithErrors(['general' => [$exception->getMessage()]], $data, '/roles/create');
        }
    }

    public function edit(string $id): void
    {
        $this->requirePermission('roles.manage');

        $role = $this->roles->find((int) $id);

        if ($role === null) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Role not found']);
            return;
        }

        $this->view('roles/form', [
            'title' => 'Edit Role',
            'role' => $role,
            'permissions' => $this->roles->allPermissions(),
            'selectedPermissions' => $this->roles->permissionIds((int) $id),
            'action' => '/roles/' . (int) $id,
        ]);
    }

    public function update(string $id): void
    {
        $this->requirePermission('roles.manage');
        $this->validateCsrf();

        $data = $this->payload();
        $errors = $this->validate($data);

        if ($errors !== []) {
            $this->backWithErrors($errors, $data, '/roles/' . (int) $id . '/edit');
        }

        try {
            (new RoleService())->update((int) $id, $data);
            Session::flash('success', 'Role updated successfully.');
            $this->redirect('/roles');
        } catch (InvalidArgumentException $exception) {
            $this->backWithErrors(['general' => [$exception->getMessage()]], $data, '/roles/' . (int) $id . '/edit');
        }
    }

    private function payload(): array
    {
        $data = $this->request->only(['name', 'slug', 'description']);
        $permissions = $this->request->post('permissions', []);
        $data['permissions'] = is_array($permissions) ? $permissions : [];

        return $data;
    }

    private function validate(array $data): array
    {
        return Validator::make($data, [
            'name' => 'required|max:100',
            'slug' => 'required|max:100',
            'description' => 'nullable|max:500',
        ]);
    }
}

