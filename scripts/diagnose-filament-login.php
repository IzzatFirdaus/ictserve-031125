#!/usr/bin/env php
<?php

/**
 * Filament Admin Login Diagnostic Script
 *
 * This script helps diagnose login issues by testing various components
 *
 * Usage: php scripts/diagnose-filament-login.php
 */

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n=== Filament Admin Login Diagnostic ===\n\n";

// Test 1: Check if admin user exists
echo "1. Checking admin user...\n";
$user = \App\Models\User::where('email', 'admin@motac.gov.my')->first();
if ($user) {
    echo "   ✅ Admin user exists: {$user->name} ({$user->email})\n";
    echo "   ✅ User ID: {$user->id}\n";
} else {
    echo "   ❌ Admin user NOT found!\n";
    exit(1);
}

// Test 2: Check user roles
echo "\n2. Checking user roles...\n";
$roles = $user->roles->pluck('name')->toArray();
if (! empty($roles)) {
    echo '   ✅ Roles: '.implode(', ', $roles)."\n";
} else {
    echo "   ❌ No roles assigned!\n";
}

// Test 3: Check Filament panel access
echo "\n3. Checking Filament panel access...\n";
try {
    $panel = \Filament\Facades\Filament::getPanel('admin');
    $canAccess = $user->canAccessPanel($panel);
    if ($canAccess) {
        echo "   ✅ User can access admin panel\n";
    } else {
        echo "   ❌ User CANNOT access admin panel!\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Error checking panel access: {$e->getMessage()}\n";
}

// Test 4: Test password verification
echo "\n4. Testing password verification...\n";
if (\Illuminate\Support\Facades\Hash::check('password', $user->password)) {
    echo "   ✅ Password 'password' is correct\n";
} else {
    echo "   ❌ Password verification FAILED!\n";
}

// Test 5: Check custom Login class
echo "\n5. Checking custom Login class...\n";
if (class_exists(\App\Filament\Pages\Auth\Login::class)) {
    echo "   ✅ Custom Login class exists\n";

    // Check if it has authenticate method
    $reflection = new \ReflectionClass(\App\Filament\Pages\Auth\Login::class);
    if ($reflection->hasMethod('authenticate')) {
        echo "   ✅ authenticate() method exists\n";
    } else {
        echo "   ❌ authenticate() method NOT found!\n";
    }

    // Check if it has normalizeLoginIdentifier method
    if ($reflection->hasMethod('normalizeLoginIdentifier')) {
        echo "   ✅ normalizeLoginIdentifier() method exists\n";
    }
} else {
    echo "   ❌ Custom Login class NOT found!\n";
}

// Test 6: Check Livewire component
echo "\n6. Checking Livewire registration...\n";
try {
    $livewire = app(\Livewire\LivewireManager::class);
    echo "   ✅ Livewire is registered\n";
} catch (\Exception $e) {
    echo "   ❌ Livewire error: {$e->getMessage()}\n";
}

// Test 7: Check session configuration
echo "\n7. Checking session configuration...\n";
$sessionDriver = config('session.driver');
$sessionLifetime = config('session.lifetime');
echo "   Driver: {$sessionDriver}\n";
echo "   Lifetime: {$sessionLifetime} minutes\n";

if ($sessionDriver === 'redis') {
    try {
        \Illuminate\Support\Facades\Redis::connection()->ping();
        echo "   ✅ Redis connection: OK\n";
    } catch (\Exception $e) {
        echo "   ❌ Redis connection: FAILED - {$e->getMessage()}\n";
    }
}

// Test 8: Check CSRF token generation
echo "\n8. Testing CSRF token...\n";
try {
    $token = csrf_token();
    if ($token) {
        echo '   ✅ CSRF token generated: '.substr($token, 0, 10)."...\n";
    } else {
        echo "   ❌ CSRF token is empty!\n";
    }
} catch (\Exception $e) {
    echo "   ❌ CSRF error: {$e->getMessage()}\n";
}

// Test 9: Test authentication manually
echo "\n9. Testing manual authentication...\n";
try {
    \Illuminate\Support\Facades\Auth::login($user);

    if (\Illuminate\Support\Facades\Auth::check()) {
        echo "   ✅ Manual login successful\n";
        echo '   ✅ Authenticated user: '.\Illuminate\Support\Facades\Auth::user()->email."\n";

        // Logout to clean up
        \Illuminate\Support\Facades\Auth::logout();
        echo "   ✅ Logout successful\n";
    } else {
        echo "   ❌ Manual login FAILED!\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Authentication error: {$e->getMessage()}\n";
}

// Test 10: Check Filament admin panel configuration
echo "\n10. Checking Filament admin panel config...\n";
try {
    $panel = \Filament\Facades\Filament::getPanel('admin');
    echo '   ID: '.$panel->getId()."\n";
    echo '   Path: '.$panel->getPath()."\n";
    echo "   ✅ Panel configuration OK\n";
} catch (\Exception $e) {
    echo "   ❌ Panel configuration error: {$e->getMessage()}\n";
}

echo "\n=== Diagnostic Complete ===\n\n";

echo "Summary:\n";
echo "--------\n";
echo "If all tests passed, the login issue is likely:\n";
echo "1. Livewire form submission not triggering\n";
echo "2. JavaScript issue in browser\n";
echo "3. CSRF token mismatch during submission\n";
echo "4. Network/browser cache issue\n\n";

echo "Suggested fixes:\n";
echo "1. Clear browser cache and cookies\n";
echo "2. Test in incognito/private window\n";
echo "3. Check browser console for JavaScript errors\n";
echo "4. Verify CSRF token in form HTML\n";
echo "5. Test with disabled JavaScript to check if Livewire is issue\n\n";
