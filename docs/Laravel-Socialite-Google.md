# Laravel Socialite — Google OAuth Authentication

## Overview

Laravel Socialite provides an expressive, fluent interface to OAuth authentication with social platforms. This guide focuses specifically on Google OAuth integration for ICTServe.

**Version**: Laravel 12.x compatible  
**Purpose**: Google OAuth authentication

## Installation

```bash
composer require laravel/socialite
```

## Google OAuth Setup

### Create Google OAuth Credentials

1. Go to [Google Cloud Console](https://console.cloud.google.com)
2. Create new project or select existing
3. Enable Google+ API
4. Go to Credentials → Create Credentials → OAuth 2.0 Client ID
5. Configure OAuth consent screen
6. Add authorized redirect URI: `http://localhost/auth/google/callback`

### Environment Configuration

Add to `.env`:

```env
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=http://localhost/auth/google/callback
```

### Service Configuration

Add to `config/services.php`:

```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
],
```

## Implementation

### Routes

```php
// routes/web.php
use App\Http\Controllers\Auth\GoogleAuthController;

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])
    ->name('auth.google');

Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
    ->name('auth.google.callback');
```

### Controller

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Update existing user
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(uniqid()), // Random password
                    'email_verified_at' => now(),
                ]);
            }

            Auth::login($user);

            return redirect()->intended('/dashboard');
            
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Google authentication failed');
        }
    }
}
```

## Database Migration

Add Google ID column to users table:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->unique()->after('email');
            $table->string('avatar')->nullable()->after('google_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'avatar']);
        });
    }
};
```

## User Model

Update fillable fields:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'email_verified_at',
    ];
}
```

## Login View

Add Google login button:

```blade
{{-- resources/views/auth/login.blade.php --}}
<div class="flex flex-col gap-4">
    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <input type="email" name="email" required>
        <input type="password" name="password" required>
        
        <button type="submit">Login</button>
    </form>

    <div class="relative">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-gray-500">Or continue with</span>
        </div>
    </div>

    <a href="{{ route('auth.google') }}" 
       class="flex items-center justify-center gap-2 border border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50">
        <svg class="w-5 h-5" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        Sign in with Google
    </a>
</div>
```

## Advanced Features

### Request Additional Scopes

```php
public function redirect()
{
    return Socialite::driver('google')
        ->scopes(['https://www.googleapis.com/auth/calendar.readonly'])
        ->redirect();
}
```

### Stateless Authentication

For API usage:

```php
public function callback()
{
    $googleUser = Socialite::driver('google')->stateless()->user();
    
    // Process user...
}
```

### Retrieve User Details

```php
$user = Socialite::driver('google')->user();

$token = $user->token; // OAuth 2.0 access token
$refreshToken = $user->refreshToken; // OAuth 2.0 refresh token
$expiresIn = $user->expiresIn; // Token expiration time

// User details
$id = $user->getId();
$nickname = $user->getNickname();
$name = $user->getName();
$email = $user->getEmail();
$avatar = $user->getAvatar();
```

## Security Considerations

### Validate Email Domain

Restrict to specific domains (e.g., government emails):

```php
public function callback()
{
    $googleUser = Socialite::driver('google')->user();
    
    // Only allow @motac.gov.my emails
    if (!str_ends_with($googleUser->getEmail(), '@motac.gov.my')) {
        return redirect('/login')->with('error', 'Only MOTAC email addresses are allowed');
    }
    
    // Continue with authentication...
}
```

### Prevent Account Takeover

Check if email already exists with different provider:

```php
$user = User::where('email', $googleUser->getEmail())->first();

if ($user && !$user->google_id) {
    // Email exists but not linked to Google
    return redirect('/login')->with('error', 'Email already registered. Please login with password.');
}
```

## Testing

```php
<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_google(): void
    {
        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('123456789');
        $abstractUser->shouldReceive('getName')->andReturn('Test User');
        $abstractUser->shouldReceive('getEmail')->andReturn('test@motac.gov.my');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

        Socialite::shouldReceive('driver->user')->andReturn($abstractUser);

        $response = $this->get('/auth/google/callback');

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
        
        $this->assertDatabaseHas('users', [
            'email' => 'test@motac.gov.my',
            'google_id' => '123456789',
        ]);
    }

    public function test_existing_user_can_link_google_account(): void
    {
        $user = User::factory()->create([
            'email' => 'test@motac.gov.my',
            'google_id' => null,
        ]);

        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('123456789');
        $abstractUser->shouldReceive('getName')->andReturn($user->name);
        $abstractUser->shouldReceive('getEmail')->andReturn($user->email);
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

        Socialite::shouldReceive('driver->user')->andReturn($abstractUser);

        $response = $this->get('/auth/google/callback');

        $this->assertAuthenticated();
        
        $user->refresh();
        $this->assertEquals('123456789', $user->google_id);
    }
}
```

## ICTServe Implementation

### Staff-Only Google Login

```php
public function callback()
{
    try {
        $googleUser = Socialite::driver('google')->user();
        
        // Validate MOTAC email
        if (!str_ends_with($googleUser->getEmail(), '@motac.gov.my')) {
            return redirect('/login')->with('error', 'Hanya e-mel MOTAC dibenarkan');
        }
        
        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            // Auto-create staff account
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => Hash::make(uniqid()),
                'email_verified_at' => now(),
            ]);
            
            // Assign default staff role
            $user->assignRole('Staff');
        } else {
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
        }

        Auth::login($user);

        return redirect()->intended('/dashboard');
        
    } catch (\Exception $e) {
        \Log::error('Google auth failed: ' . $e->getMessage());
        return redirect('/login')->with('error', 'Pengesahan Google gagal');
    }
}
```

## Troubleshooting

### Redirect URI Mismatch

Ensure redirect URI in Google Console matches exactly:
- `http://localhost/auth/google/callback` (development)
- `https://ictserve.motac.gov.my/auth/google/callback` (production)

### Invalid Client Error

Check:
1. Client ID and Secret are correct
2. Google+ API is enabled
3. OAuth consent screen is configured

### Token Expired

Implement token refresh:

```php
if ($user->tokenExpired()) {
    $newToken = Socialite::driver('google')
        ->refreshToken($user->refresh_token);
}
```

## Best Practices

1. **Email Verification**: Google emails are pre-verified
2. **Domain Restriction**: Limit to organization domain
3. **Error Handling**: Gracefully handle OAuth failures
4. **Logging**: Log authentication attempts for security
5. **HTTPS**: Always use HTTPS in production

## References

- Official Documentation: https://laravel.com/docs/12.x/socialite
- GitHub Repository: https://github.com/laravel/socialite
- Google OAuth Documentation: https://developers.google.com/identity/protocols/oauth2
