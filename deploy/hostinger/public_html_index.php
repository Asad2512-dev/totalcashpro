<?php

/**
 * Hostinger fallback when public_html cannot symlink to Laravel's /public.
 *
 * Usage:
 * 1. Clone the repo to: ~/domains/totalcashpro.com/app
 * 2. Copy Laravel public files into public_html
 * 3. Replace public_html/index.php with this file
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../app/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../app/vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../app/bootstrap/app.php';

$app->handleRequest(Request::capture());
