#!/usr/bin/env php
<?php

/**
 * Verify CSRF Token in Login Page HTML
 *
 * This script renders the login page and checks if CSRF token is present
 *
 * Usage: php scripts/verify-csrf-in-html.php
 */

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n=== CSRF Token Verification in Login Page HTML ===\n\n";

try {
    // Create a test request to the login page
    $request = \Illuminate\Http\Request::create('/admin/login', 'GET');

    // Set up session
    $request->setLaravelSession($app->make('session')->driver());

    // Create response
    $response = $app->handle($request);

    // Get the HTML content
    $html = $response->getContent();

    echo "1. Checking if login page renders...\n";
    if (strlen($html) > 0) {
        echo '   ✅ Login page HTML retrieved ('.strlen($html)." bytes)\n";
    } else {
        echo "   ❌ Empty HTML response!\n";
        exit(1);
    }

    echo "\n2. Checking for CSRF token field...\n";

    // Look for CSRF token input field
    if (preg_match('/<input[^>]*name=["\']_token["\'][^>]*>/', $html, $matches)) {
        echo '   ✅ CSRF token field found: '.htmlspecialchars($matches[0])."\n";

        // Extract token value
        if (preg_match('/value=["\']([^"\']+)["\']/', $matches[0], $tokenMatch)) {
            $tokenValue = $tokenMatch[1];
            echo '   ✅ Token value: '.substr($tokenValue, 0, 20).'...'.substr($tokenValue, -10)."\n";
            echo '   ✅ Token length: '.strlen($tokenValue)." characters\n";
        }
    } else {
        echo "   ❌ CSRF token field NOT found in HTML!\n";
        echo "   🔍 Searching for '@csrf' directive in source...\n";

        if (strpos($html, '@csrf') !== false) {
            echo "   ⚠️  Found '@csrf' directive (not compiled!)\n";
            echo "   ℹ️  Run: php artisan view:clear\n";
        }
    }

    echo "\n3. Checking for form element...\n";
    if (preg_match('/<form[^>]*wire:submit=["\']authenticate["\'][^>]*>/', $html)) {
        echo "   ✅ Livewire form found with wire:submit=\"authenticate\"\n";
    } else {
        echo "   ❌ Livewire form NOT found!\n";
    }

    echo "\n4. Checking for Livewire scripts...\n";
    if (stripos($html, 'livewire') !== false) {
        echo "   ✅ Livewire references found in HTML\n";
    } else {
        echo "   ⚠️  No Livewire references found (this may be normal if loaded via CDN)\n";
    }

    echo "\n5. Checking for login button...\n";
    if (preg_match('/Log Masuk|login_button/', $html)) {
        echo "   ✅ Login button text found\n";
    } else {
        echo "   ❌ Login button text NOT found!\n";
    }

    echo "\n=== Verification Complete ===\n\n";

    // Save HTML to file for inspection
    $outputFile = storage_path('logs/login-page-html.html');
    file_put_contents($outputFile, $html);
    echo "📄 Full HTML saved to: {$outputFile}\n";
    echo "   You can inspect this file to see the rendered output\n\n";

} catch (\Exception $e) {
    echo "\n❌ Error: {$e->getMessage()}\n";
    echo "   File: {$e->getFile()}\n";
    echo "   Line: {$e->getLine()}\n\n";
    exit(1);
}

echo "Summary:\n";
echo "--------\n";
echo "If CSRF token field was found, the fix is working!\n";
echo "If not found:\n";
echo "1. Clear views cache: php artisan view:clear\n";
echo "2. Clear all caches: php artisan optimize:clear\n";
echo "3. Check if @csrf directive is properly placed in Blade file\n\n";
