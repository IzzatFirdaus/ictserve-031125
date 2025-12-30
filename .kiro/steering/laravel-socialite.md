---
inclusion:
  fileMatchPattern:
    - 'app/Http/Controllers/Auth/GoogleController.php'
    - 'app/Services/Auth/GoogleSsoService.php'
    - 'config/services.php'
    - 'routes/auth.php'
  applyWhen:
    - OAuth authentication with Google Workspace
    - Social login implementation
    - SSO integration
---

# Laravel Socialite OAuth Guidelines

Laravel Socialite provides OAuth authentication for social platforms.

**Version**: 5.24.0 (ICTServe v3.6.1)

## ICTServe Implementation

Socialite is used for **Google Workspace SSO** (optional authentication method):

- Domain restriction: `@motac.gov.my` only
- OAuth 2.0 Authorization Code Grant
- Auto-account creation or linking

## Configuration

```env
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

## Service Implementation

```php
namespace App\Services\Auth;

use Laravel\Socialite\Facades\Socialite;

class GoogleSsoService
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes(['email', 'profile'])
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();
        
        // Validate @motac.gov.my domain
        if (!str_ends_with($googleUser->getEmail(), '@motac.gov.my')) {
            throw new \Exception('Only @motac.gov.my accounts allowed');
        }
        
        // Find or create user
        $user = User::where('email', $googleUser->getEmail())->first();
        
        if (!$user) {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'email_verified_at' => now(),
            ]);
        }
        
        return $user;
    }
}
```

## Routes

```php
// routes/auth.php
Route::get('/auth/google', [GoogleController::class, 'redirect'])
    ->name('auth.google.redirect');

Route::get('/auth/google/callback', [GoogleController::class, 'callback'])
    ->name('auth.google.callback');
```

## Controller

```php
namespace App\Http\Controllers\Auth;

use App\Services\Auth\GoogleSsoService;

class GoogleController extends Controller
{
    public function __construct(
        private GoogleSsoService $googleSso
    ) {}

    public function redirect()
    {
        return $this->googleSso->redirectToGoogle();
    }

    public function callback()
    {
        $user = $this->googleSso->handleGoogleCallback();
        
        Auth::login($user);
        
        return redirect()->intended('/dashboard');
    }
}
```

## UI Integration

```blade
<a href="{{ route('auth.google.redirect') }}" 
   class="flex items-center justify-center gap-2 px-4 py-2 border rounded">
    <svg class="w-5 h-5">{{-- Google logo --}}</svg>
    <span>Sign in with Google</span>
</a>
```

## Security Considerations

1. **Domain Validation**: Always validate `@motac.gov.my` domain
2. **State Parameter**: Socialite handles CSRF protection automatically
3. **Token Storage**: Store `google_id` for account linking
4. **Audit Logging**: Log all OAuth events

## Best Practices

1. Validate email domain before account creation
2. Link existing accounts by email
3. Provide fallback to traditional login
4. Log OAuth authentication events
5. Handle OAuth errors gracefully

## Testing

```php
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

public function test_google_sso_login(): void
{
    $socialiteUser = Mockery::mock(SocialiteUser::class);
    $socialiteUser->shouldReceive('getEmail')
        ->andReturn('test@motac.gov.my');
    $socialiteUser->shouldReceive('getName')
        ->andReturn('Test User');
    $socialiteUser->shouldReceive('getId')
        ->andReturn('google-id-123');

    Socialite::shouldReceive('driver->user')
        ->andReturn($socialiteUser);

    $response = $this->get('/auth/google/callback');
    
    $response->assertRedirect('/dashboard');
    $this->assertAuthenticated();
}
```
