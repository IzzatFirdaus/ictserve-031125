# Design Document

## Overview

This design document outlines the enhancement of the existing Google SSO implementation in ICTServe. The current system already provides basic Google OAuth authentication through Laravel Socialite, but requires improvements in testing, error handling, administrative oversight, and user experience.

**Current Architecture**: The system uses Laravel Socialite with a dedicated GoogleAuthController, proper domain validation for @motac.gov.my accounts, and basic user creation/linking functionality.

**Enhancement Goals**: Improve reliability, security, administrative capabilities, and user experience while maintaining the existing authentication flow and Bahasa Melayu exclusive interface.

## Architecture

### Current Implementation Analysis

**Existing Components**:

- `GoogleAuthController`: Handles OAuth redirect and callback
- `config/services.php`: Google OAuth configuration
- `routes/auth.php`: SSO routes (`/auth/google`, `/auth/google/callback`)
- `GoogleSsoLinked` event: Real-time notification for account linking
- Domain validation: Restricts access to @motac.gov.my emails only

**Current Flow**:

1. User clicks Google SSO button → `GoogleAuthController@redirect`
2. Redirect to Google OAuth with email/profile scopes
3. Google callback → `GoogleAuthController@callback`
4. Domain validation and user creation/linking
5. Authentication and redirect to dashboard

### Enhanced Architecture

**New Components**:

- `GoogleSsoService`: Business logic extraction from controller
- `SsoHealthCheck`: Service availability monitoring
- `SsoAnalyticsService`: Usage metrics and reporting
- Enhanced test suite with Socialite faking
- Filament admin resources for SSO management
- Artisan commands for SSO maintenance

**Enhanced Flow**:

1. Pre-authentication health check
2. Enhanced error handling with user-friendly messages
3. Comprehensive audit logging
4. Performance monitoring and caching
5. Graceful fallback mechanisms

## Components and Interfaces

### GoogleSsoService

```php
<?php

namespace App\Services;

class GoogleSsoService
{
    public function validateDomain(string $email): bool
    public function createOrUpdateUser(SocialiteUser $googleUser): User
    public function linkExistingAccount(User $user, SocialiteUser $googleUser): void
    public function logAuthenticationAttempt(string $email, bool $success, ?string $error = null): void
    public function getHealthStatus(): array
}
```

### SsoHealthCheck

```php
<?php

namespace App\Services;

class SsoHealthCheck
{
    public function checkGoogleOAuthAvailability(): bool
    public function validateConfiguration(): array
    public function testConnectivity(): bool
    public function getServiceStatus(): string
}
```

### Enhanced GoogleAuthController

```php
<?php

namespace App\Http\Controllers\Auth;

class GoogleAuthController extends Controller
{
    public function __construct(
        private GoogleSsoService $ssoService,
        private SsoHealthCheck $healthCheck
    ) {}

    public function redirect(): RedirectResponse
    public function callback(): RedirectResponse
    private function handleAuthenticationFailure(\Exception $e): RedirectResponse
    private function handleSuccessfulAuthentication(User $user): RedirectResponse
}
```

### Filament Admin Resources

```php
<?php

namespace App\Filament\Resources;

class SsoUserResource extends Resource
{
    // Admin interface for managing SSO users
}

class SsoAuditResource extends Resource
{
    // Admin interface for viewing SSO audit logs
}
```

## Data Models

### User Model Enhancements

```php
// Existing fields (no changes needed):
// - google_id: string (nullable)
// - avatar: string (nullable)
// - email_verified_at: timestamp (nullable)

// New methods to add:
public function hasGoogleSso(): bool
public function unlinkGoogleSso(): void
public function getSsoAuthenticationHistory(): Collection
```

### New SsoAuditLog Model

```php
<?php

namespace App\Models;

class SsoAuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'success',
        'error_message',
        'google_id',
        'attempted_at',
    ];

    protected $casts = [
        'success' => 'boolean',
        'attempted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Domain Validation Consistency
*For any* email address provided during Google SSO authentication, the system should only allow authentication if the email ends with '@motac.gov.my', regardless of case sensitivity
**Validates: Requirements 1.1, 4.3**

### Property 2: User Creation Idempotency
*For any* Google user attempting authentication multiple times, the system should create exactly one User record and subsequent authentications should update the existing record
**Validates: Requirements 1.2, 6.3**

### Property 3: Audit Logging Completeness
*For any* Google SSO authentication attempt (successful or failed), the system should create exactly one audit log entry with all required fields populated
**Validates: Requirements 4.1, 4.4**

### Property 4: Session Management Consistency
*For any* successful Google SSO authentication, the user session should be equivalent to a traditional login session with identical permissions and access rights
**Validates: Requirements 6.1, 6.4**

### Property 5: Error Handling Graceful Degradation
*For any* Google OAuth service failure or network error, the system should provide a user-friendly error message in Bahasa Melayu and offer fallback authentication options
**Validates: Requirements 2.1, 2.2**

### Property 6: Performance Consistency
*For any* Google SSO authentication flow under normal conditions, the complete process should complete within 5 seconds from redirect to dashboard access
**Validates: Requirements 7.1, 7.3**

## Error Handling

### Error Categories

**1. Domain Validation Errors**

- Invalid email domain (not @motac.gov.my)
- Malformed email addresses
- Case sensitivity handling

**2. OAuth Flow Errors**

- Google service unavailability
- Invalid OAuth responses
- Token validation failures
- Network timeouts

**3. User Management Errors**

- Account creation failures
- Account linking conflicts
- Database transaction errors

**4. Configuration Errors**

- Missing OAuth credentials
- Invalid redirect URIs
- Scope permission issues

### Error Handling Strategy

```php
// Enhanced error handling in GoogleAuthController
try {
    $googleUser = Socialite::driver('google')->user();
    return $this->ssoService->handleAuthentication($googleUser);
} catch (InvalidStateException $e) {
    return $this->handleAuthenticationFailure($e, 'oauth_state_error');
} catch (GuzzleException $e) {
    return $this->handleAuthenticationFailure($e, 'network_error');
} catch (DomainValidationException $e) {
    return $this->handleAuthenticationFailure($e, 'domain_error');
} catch (\Exception $e) {
    return $this->handleAuthenticationFailure($e, 'general_error');
}
```

### User-Friendly Error Messages (Bahasa Melayu)

```php
// resources/lang/ms/auth.php
'google_sso_failed' => 'Pengesahan Google tidak berjaya. Sila cuba lagi atau gunakan log masuk biasa.',
'domain_error' => 'Hanya akaun @motac.gov.my sahaja dibenarkan untuk log masuk.',
'oauth_state_error' => 'Ralat keselamatan semasa pengesahan. Sila cuba lagi.',
'network_error' => 'Masalah sambungan ke Google. Sila cuba lagi atau gunakan log masuk biasa.',
```

## Testing Strategy

### Unit Testing

**GoogleSsoService Tests**:

- Domain validation with various email formats
- User creation and linking logic
- Audit logging functionality
- Health check methods

**GoogleAuthController Tests**:

- Redirect flow with proper scopes
- Callback handling with mocked Socialite responses
- Error handling for various failure scenarios
- Session management and redirects

### Integration Testing

**OAuth Flow Tests**:

- Complete authentication flow using Socialite fake
- Database transactions and rollbacks
- Event dispatching verification
- Audit log creation

**Admin Interface Tests**:

- Filament resource functionality
- SSO user management operations
- Audit log viewing and filtering

### Property-Based Testing

```php
// Example property test for domain validation
#[Test]
public function domain_validation_property(): void
{
    // Property: Only @motac.gov.my emails should pass validation
    $this->forAll(
        Generator\string(),
        Generator\elements(['@motac.gov.my', '@gmail.com', '@yahoo.com', '@invalid.com'])
    )->then(function (string $localPart, string $domain) {
        $email = $localPart . $domain;
        $isValid = $this->ssoService->validateDomain($email);
        
        if ($domain === '@motac.gov.my') {
            $this->assertTrue($isValid);
        } else {
            $this->assertFalse($isValid);
        }
    });
}
```

### Testing Requirements

- **Minimum 95% code coverage** for all SSO-related classes
- **Property-based tests** for domain validation and user creation
- **Integration tests** for complete OAuth flow
- **Performance tests** for authentication timing
- **Security tests** for token validation and session management

## Implementation Notes

### Backward Compatibility

- Existing GoogleAuthController will be enhanced, not replaced
- Current OAuth routes remain unchanged
- Existing user accounts with google_id will continue to work
- No breaking changes to authentication flow

### Performance Considerations

- Implement Redis caching for Google user profile data
- Use database transactions for user creation/linking
- Optimize audit log writes with queued jobs
- Implement connection pooling for Google API calls

### Security Enhancements

- Add CSRF protection to OAuth state parameter
- Implement rate limiting for authentication attempts
- Add IP-based monitoring for suspicious patterns
- Enhance token validation and expiration handling

### Monitoring and Alerting

- Health check endpoints for Google OAuth availability
- Metrics collection for authentication success/failure rates
- Automated alerts for service degradation
- Performance monitoring for authentication timing

This design maintains the existing functionality while adding comprehensive enhancements for reliability, security, and administrative oversight.
