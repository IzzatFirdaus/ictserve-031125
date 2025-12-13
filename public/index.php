<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Load environment variables from .env file
// [TRACE: D00-Laravel12-EnvLoading]
$envPath = __DIR__.'/../.env';
if (file_exists($envPath)) {
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__.'/..');
    $dotenv->load();
}

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
