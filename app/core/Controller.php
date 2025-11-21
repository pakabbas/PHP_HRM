<?php

abstract class Controller
{
    protected Auth $auth;
    protected array $data = [];

    public function __construct()
    {
        $this->auth = new Auth();
        $this->data['currentUser'] = $this->auth->user();
    }

    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        $viewPath = base_path('app/views/' . $view . '.php');
        if (!file_exists($viewPath)) {
            throw new RuntimeException("View {$view} not found");
        }

        extract(array_merge($this->data, $data));
        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        $layoutPath = base_path('app/views/layouts/' . $layout . '.php');
        if (!file_exists($layoutPath)) {
            echo $content;
            return;
        }

        require $layoutPath;
    }

    protected function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }

    protected function requireAuth(array $roles = []): void
    {
        if (!$this->auth->check()) {
            redirect('auth/login');
        }

        if ($roles && !$this->auth->hasRole($roles)) {
            http_response_code(403);
            die('Forbidden');
        }
    }
}

