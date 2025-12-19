<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
// Priority: Codespaces vendor -> Docker/Local vendor -> Fallback
if (isset($_ENV['CODESPACES']) && $_ENV['CODESPACES'] === 'true' && file_exists('/tmp/vendor/autoload.php')) {
    require '/tmp/vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
} else {
    require __DIR__ . '/../../../vendor/autoload.php';
}

// Load environment variables from .env file
// [TRACE: D00-Laravel12-EnvLoading]
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->handleRequest(Request::capture());
