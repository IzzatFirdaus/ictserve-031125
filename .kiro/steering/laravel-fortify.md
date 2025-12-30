---
inclusion:
  fileMatchPattern:
    - 'app/Actions/Fortify/**/*.php'
    - 'app/Providers/FortifyServiceProvider.php'
    - 'config/fortify.php'
    - 'resources/views/auth/**/*.blade.php'
  applyWhen:
    - Authentication backend with Laravel Fortify
    - Two-factor authentication implementation
    - Password reset functionality
---

# Laravel Fortify Authentication Guidelines

Laravel Fortify provides headless authentication backend for Laravel applications.

**CRITICAL**: ICTServe uses Laravel Breeze, NOT Fortify. Do not implement Fortify unless explicitly requested.

## ICTServe Authentication Stack

- **Laravel Breeze 2.3.8**: Authentication scaffolding
- **Laravel Sanctum 4.2.1**: API token authentication
- **Laravel Socialite 5.24.0**: Google Workspace SSO
- **Spatie Permission 6.23**: Role-based access control

## If Fortify is Required

```php
// Enable features in config/fortify.php
'features' => [
    Features::registration(),
    Features::resetPasswords(),
    Features::emailVerification(),
    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]),
],
```

## Key Routes

- `POST /login` - Authenticate user
- `POST /register` - Register new user
- `POST /logout` - Logout user
- `POST /forgot-password` - Request password reset
- `POST /reset-password` - Reset password
- `POST /user/two-factor-authentication` - Enable 2FA

## Best Practices

1. Use action classes for custom logic
2. Enable rate limiting for security
3. Implement email verification
4. Offer 2FA for sensitive applications
5. Customize views in `resources/views/auth`

Do not use Fortify in ICTServe unless replacing Breeze is explicitly requested.
