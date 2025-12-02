# Laravel Sanctum — API Authentication

## Overview

Laravel Sanctum provides a featherweight authentication system for SPAs (single page applications), mobile applications, and simple, token-based APIs. It offers both session-based authentication for SPAs and token-based authentication for APIs.

**Version**: Laravel 12.x compatible  
**Purpose**: API token authentication and SPA authentication

## Installation

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

## Configuration

Published config: `config/sanctum.php`

### Middleware

Add to `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->api(prepend: [
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ]);
})
```

### CORS Configuration

In `config/cors.php`:

```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'supports_credentials' => true,
```

## API Token Authentication

### Add Trait to User Model

```php
<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
}
```

### Issuing Tokens

```php
use App\Models\User;

$user = User::find(1);

// Create token
$token = $user->createToken('token-name');

// Access plain-text token
$plainTextToken = $token->plainTextToken;
```

### Token Abilities

```php
// Create token with specific abilities
$token = $user->createToken('token-name', ['asset:create', 'asset:update']);

// Create token with all abilities
$token = $user->createToken('token-name', ['*']);
```

### Protecting Routes

```php
use Illuminate\Http\Request;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
```

### Making Authenticated Requests

Include token in Authorization header:

```bash
curl -H "Authorization: Bearer YOUR_TOKEN_HERE" \
     http://localhost/api/user
```

Or using JavaScript:

```javascript
fetch('/api/user', {
    headers: {
        'Authorization': 'Bearer ' + token,
        'Accept': 'application/json',
    }
})
```

## Token Management

### Revoking Tokens

```php
// Revoke specific token
$user->tokens()->where('id', $tokenId)->delete();

// Revoke current token
$request->user()->currentAccessToken()->delete();

// Revoke all tokens
$user->tokens()->delete();
```

### Token Abilities

Check if token has ability:

```php
if ($request->user()->tokenCan('asset:create')) {
    // User has ability
}
```

Protect routes by ability:

```php
Route::middleware(['auth:sanctum', 'abilities:asset:create,asset:update'])
    ->post('/assets', [AssetController::class, 'store']);
```

Or check any ability:

```php
Route::middleware(['auth:sanctum', 'ability:asset:create'])
    ->post('/assets', [AssetController::class, 'store']);
```

## SPA Authentication

### CSRF Protection

First, request CSRF cookie:

```javascript
axios.get('/sanctum/csrf-cookie').then(response => {
    // Login request
    axios.post('/login', credentials);
});
```

### Axios Configuration

```javascript
// resources/js/bootstrap.js
import axios from 'axios';

axios.defaults.withCredentials = true;
axios.defaults.baseURL = 'http://localhost';
```

### Login Endpoint

```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return response()->json(['message' => 'Authenticated']);
    }

    return response()->json(['message' => 'Invalid credentials'], 401);
});
```

### Protecting SPA Routes

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'show']);
});
```

## Mobile Application Authentication

### Login and Get Token

```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

Route::post('/auth/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken($request->device_name)->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => $user,
    ]);
});
```

### Logout

```php
Route::middleware('auth:sanctum')->post('/auth/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    
    return response()->json(['message' => 'Logged out']);
});
```

## Token Expiration

### Configure Expiration

```php
// config/sanctum.php
'expiration' => 60, // minutes
```

Or set to null for no expiration:

```php
'expiration' => null,
```

### Custom Expiration Per Token

```php
$token = $user->createToken('token-name', ['*'], now()->addDays(7));
```

## Testing

### Feature Tests

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_get_token(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'test-device',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'user']);
    }

    public function test_authenticated_user_can_access_protected_route(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/user');

        $response->assertStatus(200);
        $response->assertJson(['id' => $user->id]);
    }

    public function test_user_can_revoke_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/logout');

        $response->assertStatus(200);
        $this->assertCount(0, $user->tokens);
    }
}
```

## ICTServe API Implementation

### Authentication Controller

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'token' => $user->createToken($request->device_name)->plainTextToken,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
```

### API Routes

```php
// routes/api.php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AssetController;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    Route::apiResource('assets', AssetController::class);
});
```

## Security Best Practices

1. **Use HTTPS**: Always use HTTPS in production
2. **Token Storage**: Store tokens securely (not in localStorage for web)
3. **Token Rotation**: Implement token rotation for long-lived apps
4. **Rate Limiting**: Apply rate limiting to authentication endpoints
5. **Abilities**: Use token abilities for fine-grained permissions

## Rate Limiting

```php
use Illuminate\Support\Facades\RateLimiter;

RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});
```

Apply to routes:

```php
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    // Protected routes
});
```

## Custom Token Model

Create custom token model:

```php
<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    protected $fillable = [
        'name',
        'token',
        'abilities',
        'expires_at',
        'last_used_at',
    ];
}
```

Register in service provider:

```php
use Laravel\Sanctum\Sanctum;
use App\Models\PersonalAccessToken;

public function boot(): void
{
    Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
}
```

## Troubleshooting

### 401 Unauthenticated

Check:
1. Token is included in Authorization header
2. Token exists in database
3. Token hasn't been revoked
4. Middleware is applied correctly

### CORS Issues

Ensure `config/cors.php` is configured:

```php
'supports_credentials' => true,
```

And frontend sends credentials:

```javascript
axios.defaults.withCredentials = true;
```

## References

- Official Documentation: https://laravel.com/docs/12.x/sanctum
- GitHub Repository: https://github.com/laravel/sanctum
