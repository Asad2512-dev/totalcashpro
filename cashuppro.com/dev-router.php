<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;

// Handle login route
if ($path === '/login') {
    require __DIR__ . '/frontend/pages/account/login.php';
    return true;
}

if ($path !== '/' && is_file($file)) {
    return false;
}

require __DIR__ . '/index.php';
