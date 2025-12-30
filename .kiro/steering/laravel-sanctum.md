---
inclusion:
  fileMatchPattern:
    - 'app/Http/Controllers/Api/**/*.php'
    - 'routes/api.php'
    - 'config/sanctum.php'
    - 'database/migrations/*_personal_access_tokens_table.php'
  applyWhen:
    - API token authentication
    - SPA authentication
    - Mobile app authentication
---

# Laravel Sanctum API Authentication Guidelines

Laravel Sanctum provides token-based API authentication for SPAs and mobile applications.

**Version**: 4.2.1 (ICTServe v3.6.1)

## ICTServe Implementation

Sanctum is used for:
- API token authentication for future mobile apps
- External integrations
- Token-based access with configurable abilities

## Token Generation

```php
use App\Models\User;

$user = User::find(1);

// Create token with abilities
$token = $user->createToken('api-token', [
    'read:tickets',
    'write:tickets',
    'read:loans',
    'write:loans'
])->plainTextToken;
```

## Token Abilities

ICTServe defines these abilities:

- `read:tickets` - View helpdesk tickets
- `write:tickets` - Create/update tickets
- `read:loans` - View loan applications
- `write:loans` - Create/update loans
- `admin:all` - Full admin access

## Protecting Routes

```php
// routes/api.php
Route::middleware(['auth:sanctum', 'ability:read:tickets'])
    ->get('/tickets', [ApiTicketController::class, 'index']);

Route::middleware(['auth:sanctum', 'ability:write:tickets'])
    ->post('/tickets', [ApiTicketController::class, 'store']);
```

## Token Expiration

```env
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
SANCTUM_TOKEN_EXPIRATION=30
```

Default: 30 days expiration

## Rate Limiting

```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->throttleApi('60,1'); // 60 requests per minute
})
```

## Token Management

```php
// Revoke specific token
$user->tokens()->where('id', $tokenId)->delete();

// Revoke all tokens
$user->tokens()->delete();

// Check token abilities
if ($user->tokenCan('read:tickets')) {
    // User has permission
}
```

## Best Practices

1. Use specific abilities (not wildcard `*`)
2. Set token expiration for security
3. Log token usage for audit trail
4. Revoke tokens on password change
5. Implement rate limiting on API routes

## Testing

```php
use Laravel\Sanctum\Sanctum;

public function test_api_endpoint(): void
{
    Sanctum::actingAs(
        User::factory()->create(),
        ['read:tickets']
    );

    $response = $this->getJson('/api/tickets');
    $response->assertOk();
}
```
