<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Services\AuthService;

final class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }

        $this->view('auth/login', ['title' => 'Sign in'], 'auth');
    }

    public function login(): void
    {
        $this->validateCsrf();

        $data = $this->request->only(['email', 'password']);
        $errors = Validator::make($data, [
            'email' => 'required|email|max:190',
            'password' => 'required|min:8',
        ]);

        if ($errors !== []) {
            $this->backWithErrors($errors, ['email' => $data['email'] ?? ''], '/login');
        }

        if (! (new AuthService())->attempt((string) $data['email'], (string) $data['password'])) {
            Session::flash('error', 'Invalid credentials or inactive account.');
            Session::flash('old', ['email' => $data['email'] ?? '']);
            $this->redirect('/login');
        }

        Session::flash('success', 'Signed in successfully.');
        $this->redirect('/');
    }

    public function logout(): void
    {
        $this->validateCsrf();
        (new AuthService())->logout();
        $this->redirect('/login');
    }
}

