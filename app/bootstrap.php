<?php

declare(strict_types=1);

session_start();
date_default_timezone_set('UTC');

require_once __DIR__ . '/helpers.php';

spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/core/' . $class . '.php',
        __DIR__ . '/controllers/' . $class . '.php',
        __DIR__ . '/models/' . $class . '.php',
        __DIR__ . '/lib/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Ensure flash storage is available
if (!isset($_SESSION['flash'])) {
    $_SESSION['flash'] = [];
}

