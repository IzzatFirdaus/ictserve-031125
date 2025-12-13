<?php

require __DIR__.'/../vendor/autoload.php';

// Bootstrap laravel app
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$u = User::where('email', 'admin@motac.gov.my')->first();
if (! $u) {
    echo "No user with email admin@motac.gov.my\n";
    exit(1);
}

echo 'Email: '.$u->email."\n";
echo 'Password length: '.strlen($u->password)."\n";
echo Hash::check('password', $u->password) ? "Hash matches\n" : "Hash mismatch\n";
// Check role and flags
if (method_exists($u, 'hasRole')) {
    echo 'hasRole("admin"): '.($u->hasRole('admin') ? 'yes' : 'no')."\n";
}

echo 'is_active: '.($u->is_active ? 'true' : 'false')."\n";
echo 'email_verified_at: '.($u->email_verified_at ? $u->email_verified_at : 'null')."\n";
echo 'deleted_at: '.($u->deleted_at ? $u->deleted_at : 'null')."\n";

// Try authenticating via the 'web' guard and Filament guard
use Illuminate\Support\Facades\Auth;

// Using web guard
$guard = Auth::guard('web');
echo "Auth::guard('web')->check(): ".($guard->check() ? 'true' : 'false')."\n";
// Try guard attempt properly
$attempt = $guard->attempt(['email' => 'admin@motac.gov.my', 'password' => 'password']);
echo "Auth::guard('web')->attempt returned: ".($attempt ? 'true' : 'false').PHP_EOL;
$globalAttempt = Auth::attempt(['email' => 'admin@motac.gov.my', 'password' => 'password']);
echo 'Auth::attempt returned: '.($globalAttempt ? 'true' : 'false').PHP_EOL;
// Manual provider check
$providerUser = $guard->getProvider()->retrieveByCredentials(['email' => 'admin@motac.gov.my']);
echo 'Provider found: '.($providerUser ? 'yes' : 'no').PHP_EOL;
if ($providerUser) {
    echo 'Provider user email: '.$providerUser->email.PHP_EOL;
}
try {
    $isValid = $guard->getProvider()->validateCredentials($providerUser, ['password' => 'password']);
    echo 'Provider validated password: '.($isValid ? 'yes' : 'no').PHP_EOL;
} catch (\Throwable $e) {
    echo 'Provider validation thrown: '.$e->getMessage().PHP_EOL;
}

// Check Filament guard
if (class_exists('\\Filament\\Facades\\Filament')) {
    $filamentGuard = \Filament\Facades\Filament::auth();
    echo 'Filament auth check: '.($filamentGuard->check() ? 'true' : 'false')."\n";
    echo 'Filament guard name: '.\Filament\Facades\Filament::getAuthGuard().PHP_EOL;

    // Trigger a failed authentication intentionally to test event logging (unsafe on production)
    \Illuminate\Support\Facades\Auth::attempt(['email' => 'admin@motac.gov.my', 'password' => 'badfail']);
    echo "Simulated a failed login attempt to write event log. Check storage/logs/laravel.log for the listener message\n";
}
