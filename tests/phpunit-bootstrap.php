<?php

declare(strict_types=1);

// PHPUnit bootstrap that ensures test environment uses fresh config (no cached config)
// Require Composer autoload
require __DIR__.'/../vendor/autoload.php';

// Manually load dev dependencies only if not already loaded
// These are not in the autoload classmap because composer install didn't complete
if (!class_exists('Mockery')) {
    $devDeps = [
        __DIR__.'/../vendor/mockery/mockery/library/Mockery.php',
        __DIR__.'/../vendor/mockery/mockery/library/helpers.php',
        __DIR__.'/../vendor/hamcrest/hamcrest-php/hamcrest/Hamcrest.php',
    ];

    foreach ($devDeps as $file) {
        if (file_exists($file)) {
            require_once $file;
        }
    }
}

// Register PSR-4 namespace autoloader for dev dependencies if not already registered
spl_autoload_register(function ($class) {
    $prefixes = [
        'Mockery\\' => __DIR__.'/../vendor/mockery/mockery/library/Mockery/',
        'Hamcrest\\' => __DIR__.'/../vendor/hamcrest/hamcrest-php/hamcrest/Hamcrest/',
        'Faker\\' => __DIR__.'/../vendor/fakerphp/faker/src/Faker/',
    ];
    
    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }
        
        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        
        if (file_exists($file)) {
            require $file;
            return true;
        }
    }
    return false;
});

// If a cached config file exists, remove it for tests so environment overrides (phpunit.xml/.env.testing)
// are respected. This prevents tests from using a production-cached configuration (e.g., 'mysql').
$cachedConfig = __DIR__.'/../bootstrap/cache/config.php';
if (file_exists($cachedConfig)) {
    @unlink($cachedConfig);
}

// Ensure common testing env vars are set (phpunit.xml should already set them, but be explicit)
putenv('APP_ENV=testing');

// Use MySQL database for testing with a dedicated test database
// IMPORTANT: Tests must run sequentially (not in parallel) to avoid race conditions
putenv('DB_CONNECTION=mysql');
putenv('DB_DATABASE=ictserve_test');

// Also set superglobals used by bootstrap/app.php (_ENV/_SERVER) so it does not override to 'local'
$_ENV['APP_ENV'] = 'testing';
$_SERVER['APP_ENV'] = 'testing';
$_ENV['DB_CONNECTION'] = 'mysql';
$_ENV['DB_DATABASE'] = 'ictserve_test';
$_SERVER['DB_CONNECTION'] = 'mysql';
$_SERVER['DB_DATABASE'] = 'ictserve_test';

// Allow tests to detect this bootstrap ran
if (! defined('TEST_BOOTSTRAP_RAN')) {
    define('TEST_BOOTSTRAP_RAN', true);
}
