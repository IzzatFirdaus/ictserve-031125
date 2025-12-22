<?php

declare(strict_types=1);

// PHPUnit bootstrap that ensures test environment uses fresh config (no cached config)
// Require Composer autoload
require __DIR__.'/../vendor/autoload.php';

// If a cached config file exists, remove it for tests so environment overrides (phpunit.xml/.env.testing)
// are respected. This prevents tests from using a production-cached configuration (e.g., 'mysql').
$cachedConfig = __DIR__.'/../bootstrap/cache/config.php';
if (file_exists($cachedConfig)) {
    @unlink($cachedConfig);
}

// Ensure common testing env vars are set (phpunit.xml should already set them, but be explicit)
putenv('APP_ENV=testing');

// Use MySQL database for testing (same as development for now)
putenv('DB_CONNECTION=mysql');
putenv('DB_DATABASE=ictserve');

// Also set superglobals used by bootstrap/app.php (_ENV/_SERVER) so it does not override to 'local'
$_ENV['APP_ENV'] = 'testing';
$_SERVER['APP_ENV'] = 'testing';
$_ENV['DB_CONNECTION'] = 'mysql';
$_ENV['DB_DATABASE'] = 'ictserve';
$_SERVER['DB_CONNECTION'] = 'mysql';
$_SERVER['DB_DATABASE'] = 'ictserve';

// Allow tests to detect this bootstrap ran
if (! defined('TEST_BOOTSTRAP_RAN')) {
    define('TEST_BOOTSTRAP_RAN', true);
}
