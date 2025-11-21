<?php

class AuthController extends Controller
{
    public function login(): void
    {
        if ($this->auth->check()) {
            redirect('dashboard/index');
        }
        $this->view('auth/login', [
            'pageTitle' => 'Sign In',
            'flash' => flash_get('auth'),
        ], 'auth');
    }

    public function authenticate(): void
    {
        verify_csrf();
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($this->auth->attempt($username, $password)) {
            flash_set('global', 'Welcome back!', 'success');
            redirect('dashboard/index');
        }

        flash_set('auth', 'Invalid credentials or inactive account.', 'danger');
        redirect('auth/login');
    }

    public function logout(): void
    {
        $this->auth->logout();
        flash_set('auth', 'You have been logged out.', 'info');
        redirect('auth/login');
    }
}

