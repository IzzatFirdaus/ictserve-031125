# Laravel Fortify — Authentication Backend

## Overview

Laravel Fortify is a frontend agnostic authentication backend for Laravel. It implements Laravel's authentication features including login, registration, email verification, password reset, and two-factor authentication.

**Version**: Laravel 12.x compatible  
**Purpose**: Headless authentication backend

## Installation

```bash
composer require laravel/fortify
php artisan fortify:install
php artisan migrate
```

## Configuration

Published config file: `config/fortify.php`

### Enable Features

```php
// config/fortify.php
'features' => [
    Features::registration(),
    Features::resetPasswords(),
    Features::emailVerification(),
    Features::updateProfileInformation(),
    Features::updatePasswords(),
    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]),
],
```

## Service Provider Setup

In `app/Providers/FortifyServiceProvider.php`:

```php
use Laravel\Fortify\Fortify;

public function boot(): void
{
    Fortify::loginView(fn () => view('auth.login'));
    Fortify::registerView(fn () => view('auth.register'));
    Fortify::requestPasswordResetLinkView(fn () => view('auth.forgot-password'));
    Fortify::resetPasswordView(fn ($request) => view('auth.reset-password', ['request' => $request]));
    Fortify::verifyEmailView(fn () => view('auth.verify-email'));
    Fortify::confirmPasswordView(fn () => view('auth.confirm-password'));
    Fortify::twoFactorChallengeView(fn () => view('auth.two-factor-challenge'));
}
```

## Authentication

### Login

**Route**: `POST /login`

```html
<form method="POST" action="/login">
    @csrf
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <input type="checkbox" name="remember"> Remember Me
    <button type="submit">Login</button>
</form>
```

### Logout

**Route**: `POST /logout`

```html
<form method="POST" action="/logout">
    @csrf
    <button type="submit">Logout</button>
</form>
```

### Customizing Authentication Logic

```php
use Laravel\Fortify\Fortify;
use Illuminate\Http\Request;

Fortify::authenticateUsing(function (Request $request) {
    $user = User::where('email', $request->email)->first();

    if ($user && Hash::check($request->password, $user->password)) {
        return $user;
    }
});
```

## Registration

**Route**: `POST /register`

```html
<form method="POST" action="/register">
    @csrf
    <input type="text" name="name" required>
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <input type="password" name="password_confirmation" required>
    <button type="submit">Register</button>
</form>
```

### Customizing Registration

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
```

Register in `FortifyServiceProvider`:

```php
use App\Actions\Fortify\CreateNewUser;
use Laravel\Fortify\Fortify;

public function register(): void
{
    $this->app->singleton(CreatesNewUsers::class, CreateNewUser::class);
}
```

## Password Reset

### Request Reset Link

**Route**: `POST /forgot-password`

```html
<form method="POST" action="/forgot-password">
    @csrf
    <input type="email" name="email" required>
    <button type="submit">Email Password Reset Link</button>
</form>
```

### Reset Password

**Route**: `POST /reset-password`

```html
<form method="POST" action="/reset-password">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">
    <input type="email" name="email" value="{{ old('email', $request->email) }}" required>
    <input type="password" name="password" required>
    <input type="password" name="password_confirmation" required>
    <button type="submit">Reset Password</button>
</form>
```

## Email Verification

Enable in config:

```php
'features' => [
    Features::emailVerification(),
],
```

Add to User model:

```php
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    // ...
}
```

Protect routes:

```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
```

## Two-Factor Authentication

### Enable 2FA

**Route**: `POST /user/two-factor-authentication`

```html
<form method="POST" action="/user/two-factor-authentication">
    @csrf
    <button type="submit">Enable Two-Factor Authentication</button>
</form>
```

### Show QR Code

```blade
@if(auth()->user()->two_factor_secret)
    <div>
        {!! auth()->user()->twoFactorQrCodeSvg() !!}
    </div>
    
    <div>
        <h3>Recovery Codes</h3>
        @foreach(json_decode(decrypt(auth()->user()->two_factor_recovery_codes)) as $code)
            <code>{{ $code }}</code>
        @endforeach
    </div>
@endif
```

### Disable 2FA

**Route**: `DELETE /user/two-factor-authentication`

```html
<form method="POST" action="/user/two-factor-authentication">
    @csrf
    @method('DELETE')
    <button type="submit">Disable Two-Factor Authentication</button>
</form>
```

### Challenge View

```html
<form method="POST" action="/two-factor-challenge">
    @csrf
    <input type="text" name="code" placeholder="Authentication Code">
    <p>OR</p>
    <input type="text" name="recovery_code" placeholder="Recovery Code">
    <button type="submit">Verify</button>
</form>
```

## Profile Management

### Update Profile Information

**Route**: `PUT /user/profile-information`

```html
<form method="POST" action="/user/profile-information">
    @csrf
    @method('PUT')
    <input type="text" name="name" value="{{ auth()->user()->name }}" required>
    <input type="email" name="email" value="{{ auth()->user()->email }}" required>
    <button type="submit">Update Profile</button>
</form>
```

### Update Password

**Route**: `PUT /user/password`

```html
<form method="POST" action="/user/password">
    @csrf
    @method('PUT')
    <input type="password" name="current_password" required>
    <input type="password" name="password" required>
    <input type="password" name="password_confirmation" required>
    <button type="submit">Update Password</button>
</form>
```

## Password Confirmation

Protect sensitive routes:

```php
Route::get('/settings', function () {
    // ...
})->middleware(['password.confirm']);
```

**Route**: `POST /user/confirm-password`

```html
<form method="POST" action="/user/confirm-password">
    @csrf
    <input type="password" name="password" required>
    <button type="submit">Confirm</button>
</form>
```

## Redirects

Customize redirects in `FortifyServiceProvider`:

```php
use Laravel\Fortify\Fortify;

Fortify::registerView(function () {
    return view('auth.register');
});

// After login redirect
protected $home = '/dashboard';
```

Or in `config/fortify.php`:

```php
'home' => '/dashboard',
```

## Rate Limiting

Configure in `config/fortify.php`:

```php
'limiters' => [
    'login' => 'login',
    'two-factor' => 'two-factor',
],
```

Define limiters in `app/Providers/RouteServiceProvider.php`:

```php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)->by($request->email.$request->ip());
});
```

## Testing

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_login(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    public function test_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }
}
```

## Integration with Livewire

```blade
{{-- Login form with Livewire --}}
<div>
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="/login" wire:submit.prevent="login">
        @csrf
        
        <input type="email" wire:model="email" required>
        @error('email') <span>{{ $message }}</span> @enderror
        
        <input type="password" wire:model="password" required>
        @error('password') <span>{{ $message }}</span> @enderror
        
        <button type="submit">Login</button>
    </form>
</div>
```

## Best Practices

1. **Use Action Classes**: Customize authentication logic in action classes
2. **Enable Features Selectively**: Only enable features you need
3. **Rate Limiting**: Always configure rate limiting for security
4. **Email Verification**: Enable for production applications
5. **2FA**: Offer two-factor authentication for sensitive applications

## Common Patterns

### Custom Login Redirect

```php
use Laravel\Fortify\Fortify;

Fortify::authenticateUsing(function (Request $request) {
    $user = User::where('email', $request->email)->first();

    if ($user && Hash::check($request->password, $user->password)) {
        if ($user->hasRole('admin')) {
            session()->put('url.intended', '/admin');
        }
        return $user;
    }
});
```

### Remember Me Duration

```php
// config/session.php
'lifetime' => 120, // minutes
'expire_on_close' => false,
```

## References

- Official Documentation: https://laravel.com/docs/12.x/fortify
- GitHub Repository: https://github.com/laravel/fortify
