<?php

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        $root = rtrim(__DIR__ . '/..', '/');
        return $path ? $root . '/' . ltrim($path, '/') : $root;
    }
}

if (!function_exists('public_path')) {
    function public_path(string $path = ''): string
    {
        $public = base_path('public');
        return $path ? $public . '/' . ltrim($path, '/') : $public;
    }
}

if (!function_exists('base_url')) {
    function base_url(string $path = ''): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
        $base = rtrim($protocol . '://' . $host . $scriptDir, '/');
        $url = $path ? $base . '/' . ltrim($path, '/') : $base . '/';
        return rtrim($url, '/') . '/';
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return base_url('public/' . ltrim($path, '/'));
    }
}

if (!function_exists('route_to')) {
    function route_to(string $route, array $params = []): string
    {
        unset($params['route']);
        $query = array_merge(['route' => $route], $params);
        return base_url('index.php') . '?' . http_build_query($query);
    }
}

if (!function_exists('redirect')) {
    function redirect(string $route, array $params = []): void
    {
        header('Location: ' . route_to($route, $params));
        exit;
    }
}

if (!function_exists('flash_set')) {
    function flash_set(string $key, string $message, string $type = 'info'): void
    {
        $_SESSION['flash'][$key] = [
            'message' => $message,
            'type' => $type,
            'time' => time(),
        ];
    }
}

if (!function_exists('flash_get')) {
    function flash_get(string $key): ?array
    {
        if (!isset($_SESSION['flash'][$key])) {
            return null;
        }
        $flash = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $flash;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(16));
        }
        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('verify_csrf')) {
    function verify_csrf(): void
    {
        $token = $_POST['_token'] ?? '';
        if (!$token || !hash_equals($_SESSION['_csrf_token'] ?? '', $token)) {
            http_response_code(419);
            die('Invalid CSRF token.');
        }
    }
}

if (!function_exists('dd')) {
    function dd(...$vars): void
    {
        echo '<pre>';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
        die();
    }
}

