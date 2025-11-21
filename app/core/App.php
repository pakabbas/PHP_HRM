<?php

class App
{
    protected string $defaultRoute = 'auth/login';

    public function run(): void
    {
        $route = $_GET['route'] ?? ($this->isAuthenticated() ? 'dashboard/index' : $this->defaultRoute);
        $route = trim($route, '/');

        if ($route === '') {
            $route = 'dashboard/index';
        }

        $segments = explode('/', $route);
        $controllerName = ucfirst($segments[0]) . 'Controller';
        $method = $segments[1] ?? 'index';
        $params = array_slice($segments, 2);

        if (!class_exists($controllerName)) {
            $this->abort(404, sprintf('Controller %s not found', $controllerName));
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $method)) {
            $this->abort(404, sprintf('Action %s::%s not found', $controllerName, $method));
        }

        call_user_func_array([$controller, $method], $params);
    }

    protected function abort(int $code, string $message): void
    {
        http_response_code($code);
        echo "<h1>{$code}</h1><p>{$message}</p>";
        exit;
    }

    protected function isAuthenticated(): bool
    {
        return !empty($_SESSION['user']);
    }
}

