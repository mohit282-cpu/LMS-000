<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    public function __construct(protected Request $request)
    {
    }

    protected function view(string $view, array $data = [], string $layout = 'app'): void
    {
        echo View::render($view, $data, $layout);
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . url($path), true, 302);
        exit;
    }

    protected function requireAuth(): void
    {
        if (! Auth::check()) {
            Session::flash('warning', 'Please sign in to continue.');
            $this->redirect('/login');
        }
    }

    protected function requirePermission(string $permission): void
    {
        $this->requireAuth();

        if (! Auth::can($permission)) {
            http_response_code(403);
            $this->view('errors/403', ['title' => 'Access denied']);
            exit;
        }
    }

    protected function validateCsrf(): void
    {
        if (! Csrf::validate((string) $this->request->post('_csrf', ''))) {
            http_response_code(419);
            $this->view('errors/419', ['title' => 'Session expired'], Auth::check() ? 'app' : 'auth');
            exit;
        }
    }

    protected function backWithErrors(array $errors, array $old, string $path): void
    {
        Session::flash('errors', $errors);
        Session::flash('old', $old);
        $this->redirect($path);
    }
}

