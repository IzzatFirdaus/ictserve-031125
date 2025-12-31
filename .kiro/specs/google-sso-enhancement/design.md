# Design Document

## Overview

This design document outlines the comprehensive enhancement of Google services integration in ICTServe, including Google SSO, Gmail API integration, and OAuth verification for production readiness. The current system provides basic Google OAuth authentication and Gmail API functionality, but requires significant enhancements for production use, comprehensive testing, and unified Google services management.

**Current Architecture**: The system uses Laravel Socialite for Google SSO with domain validation, and has Gmail API integration that is currently limited to test users due to OAuth verification requirements.

**Enhancement Goals**: Achieve production readiness through OAuth verification, integrate Gmail API with the notification system, provide comprehensive testing coverage, and create unified Google services management while maintaining the Bahasa Melayu exclusive interface.

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
- `GmailService`: Enhanced Gmail API integration with OAuth verification support
- `GoogleOAuthVerificationService`: Manages OAuth app verification process and test users
- `UnifiedNotificationDispatcher`: Central notification system supporting Gmail API and SMTP
- `SsoHealthCheck`: Service availability monitoring for all Google services
- `SsoAnalyticsService`: Usage metrics and reporting for SSO and Gmail
- Enhanced test suite with comprehensive property-based testing
- Filament admin resources for unified Google services management
- Artisan commands for Google services maintenance and verification

**Enhanced Flow**:

1. Pre-authentication health check for both SSO and Gmail services
2. OAuth verification status detection and appropriate handling
3. Enhanced error handling with user-friendly messages and fallback options
4. Comprehensive audit logging for all Google services interactions
5. Performance monitoring and caching for both SSO and Gmail API
6. Graceful fallback mechanisms (SSO to traditional auth, Gmail API to SMTP)

## Components and Interfaces

### GmailService (Enhanced)

```php
<?php

namespace App\Services;

class GmailService
{
    public function __construct(
        private GoogleOAuthVerificationService $verificationService,
        private SsoHealthCheck $healthCheck
    ) {}

    public function isAuthenticated(): bool
    public function authenticate(?string $authCode = null): bool
    public function sendEmail(string $to, string $subject, string $body, array $attachments = []): bool
    public function getAuthenticationMethod(): string // 'oauth', 'service_account', 'smtp_fallback'
    public function getVerificationStatus(): array
    public function handleTestUserLimitation(string $email): array
    public function fallbackToSmtp(): void
    public function getQuotaUsage(): array
}
```

### GoogleOAuthVerificationService

```php
<?php

namespace App\Services;

class GoogleOAuthVerificationService
{
    public function getVerificationStatus(): string // 'verified', 'pending', 'testing', 'rejected'
    public function isInTestingMode(): bool
    public function addTestUser(string $email): bool
    public function removeTestUser(string $email): bool
    public function getTestUsers(): array
    public function canUserAuthenticate(string $email): bool
    public function getVerificationRequirements(): array
    public function submitForVerification(array $documents): bool
    public function handleVerificationCallback(array $data): void
}
```

### UnifiedNotificationDispatcher

```php
<?php

namespace App\Services;

class UnifiedNotificationDispatcher
{
    public function __construct(
        private GmailService $gmailService,
        private EmailService $emailService // SMTP fallback
    ) {}

    public function dispatch(Notification $notification, array $channels = ['database', 'gmail', 'broadcast']): array
    public function dispatchCritical(Notification $notification): array // Bypasses user preferences
    public function getChannelStatus(): array
    public function getDispatchStatistics(): array
    public function setUserPreferences(User $user, array $preferences): void
    public function getUserPreferences(User $user): array
}
```

### GoogleSsoService (Enhanced)

```php
<?php

namespace App\Services;

class GoogleSsoService
{
    public function __construct(
        private GoogleOAuthVerificationService $verificationService
    ) {}

    public function validateDomain(string $email): bool
    public function createOrUpdateUser(SocialiteUser $googleUser): User
    public function linkExistingAccount(User $user, SocialiteUser $googleUser): void
    public function logAuthenticationAttempt(string $email, bool $success, ?string $error = null): void
    public function getHealthStatus(): array
    public function canUserAuthenticate(string $email): bool // Checks verification status
    public function handleTestUserLimitation(string $email): array
}
```

### SsoHealthCheck (Enhanced)

```php
<?php

namespace App\Services;

class SsoHealthCheck
{
    public function checkGoogleSsoAvailability(): bool
    public function checkGmailApiAvailability(): bool
    public function validateSsoConfiguration(): array
    public function validateGmailConfiguration(): array
    public function testConnectivity(): bool
    public function getOverallServiceStatus(): string
    public function getVerificationStatus(): array
    public function checkQuotaLimits(): array
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
        private SsoHealthCheck $healthCheck,
        private GoogleOAuthVerificationService $verificationService
    ) {}

    public function redirect(): RedirectResponse
    public function callback(): RedirectResponse
    private function handleAuthenticationFailure(\Exception $e): RedirectResponse
    private function handleSuccessfulAuthentication(User $user): RedirectResponse
    private function handleTestUserLimitation(string $email): RedirectResponse
}
```

### Filament Admin Resources (Enhanced)

```php
<?php

namespace App\Filament\Resources;

class GoogleServicesResource extends Resource
{
    // Unified admin interface for all Google services
    // Includes SSO users, Gmail configuration, verification status
}

class GoogleServicesAuditResource extends Resource
{
    // Comprehensive audit logs for SSO and Gmail API usage
}

class GoogleVerificationResource extends Resource
{
    // OAuth verification management and test user administration
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
public function getGmailAuthenticationStatus(): string
public function getNotificationPreferences(): array
public function setNotificationPreferences(array $preferences): void
```

### Enhanced SsoAuditLog Model

```php
<?php

namespace App\Models;

class GoogleServicesAuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'service_type', // 'sso', 'gmail'
        'action', // 'authenticate', 'send_email', 'authorize'
        'ip_address',
        'user_agent',
        'success',
        'error_message',
        'google_id',
        'authentication_method', // 'oauth', 'service_account', 'smtp_fallback'
        'verification_status', // 'verified', 'testing', 'pending'
        'metadata', // JSON field for additional data
        'attempted_at',
    ];

    protected $casts = [
        'success' => 'boolean',
        'attempted_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

### New GoogleOAuthVerification Model

```php
<?php

namespace App\Models;

class GoogleOAuthVerification extends Model
{
    protected $fillable = [
        'client_id',
        'verification_status', // 'verified', 'pending', 'testing', 'rejected'
        'test_users', // JSON array of test user emails
        'verification_submitted_at',
        'verification_approved_at',
        'verification_documents', // JSON field
        'quota_limits', // JSON field
        'last_status_check',
    ];

    protected $casts = [
        'test_users' => 'array',
        'verification_documents' => 'array',
        'quota_limits' => 'array',
        'verification_submitted_at' => 'datetime',
        'verification_approved_at' => 'datetime',
        'last_status_check' => 'datetime',
    ];
}
```

### New NotificationPreference Model

```php
<?php

namespace App\Models;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'notification_type', // 'ticket_update', 'loan_approval', etc.
        'channels', // JSON array: ['database', 'gmail', 'broadcast']
        'enabled',
        'quiet_hours_start',
        'quiet_hours_end',
        'frequency', // 'immediate', 'daily_digest', 'weekly_digest'
    ];

    protected $casts = [
        'channels' => 'array',
        'enabled' => 'boolean',
        'quiet_hours_start' => 'datetime:H:i',
        'quiet_hours_end' => 'datetime:H:i',
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
**Validates: Requirements 5.1, 9.1**

### Property 2: User Creation Idempotency
*For any* Google user attempting authentication multiple times, the system should create exactly one User record and subsequent authentications should update the existing record
**Validates: Requirements 5.2, 12.3**

### Property 3: Gmail OAuth Production Mode Authentication
*For any* @motac.gov.my user, when the OAuth app is in production mode, Gmail authentication should succeed without requiring test user approval
**Validates: Requirements 1.1, 1.2**

### Property 4: Gmail Authentication Method Selection
*For any* Gmail API request, when multiple authentication methods are available, the system should automatically select the most appropriate method based on availability and verification status
**Validates: Requirements 3.3, 3.4**

### Property 5: Gmail API Fallback Behavior
*For any* Gmail API failure, the system should attempt fallback methods (service account, then SMTP) before reporting complete failure
**Validates: Requirements 3.4, 3.5, 6.2**

### Property 6: Unified Notification Channel Dispatch
*For any* notification dispatch request, the UnifiedNotificationDispatcher should attempt delivery through all requested channels (database, Gmail API, broadcast) simultaneously
**Validates: Requirements 10.1, 10.2**

### Property 7: Critical Notification Bypass
*For any* critical notification, the system should bypass user preferences and use the most reliable delivery method available
**Validates: Requirements 10.3**

### Property 8: Gmail API Audit Logging Completeness
*For any* Gmail API interaction (authentication, email sending), the system should create exactly one audit log entry with all required fields populated
**Validates: Requirements 6.3, 9.1**

### Property 9: Email Template Gmail API Compatibility
*For any* email template rendered for Gmail API, the output should be properly formatted for Gmail API consumption and include all specified attachments
**Validates: Requirements 6.5**

### Property 10: Notification Statistics Collection
*For any* notification dispatch operation, the system should collect and store statistics including channel usage, success rates, and fallback occurrences
**Validates: Requirements 10.5**

### Property 11: OAuth Verification Status Detection
*For any* Google services operation, the system should correctly detect and handle the current OAuth verification status (verified, testing, pending, rejected)
**Validates: Requirements 1.5, 2.5**

### Property 12: Service Account Domain Impersonation
*For any* @motac.gov.my user, when service account authentication is configured with domain-wide delegation, Gmail operations should successfully impersonate the user
**Validates: Requirements 3.1, 3.2**

### Property 13: Session Management Consistency
*For any* successful Google SSO authentication, the user session should be equivalent to a traditional login session with identical permissions and access rights
**Validates: Requirements 12.1, 12.4**

### Property 14: Error Handling Graceful Degradation
*For any* Google OAuth service failure or network error, the system should provide a user-friendly error message in Bahasa Melayu and offer fallback authentication options
**Validates: Requirements 7.1, 7.4**

### Property 15: Performance Consistency
*For any* Google SSO authentication flow under normal conditions, the complete process should complete within 5 seconds from redirect to dashboard access
**Validates: Requirements 13.1, 13.3**

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

**3. OAuth Verification Errors**

- App in testing mode with unauthorized user
- Verification pending or rejected
- Test user limit exceeded
- Verification documentation incomplete

**4. Gmail API Errors**

- Gmail API quota exceeded
- Authentication method failures
- Email sending failures
- Attachment processing errors
- Template rendering errors

**5. User Management Errors**

- Account creation failures
- Account linking conflicts
- Database transaction errors

**6. Configuration Errors**

- Missing OAuth credentials
- Invalid redirect URIs
- Scope permission issues
- Service account configuration errors

### Error Handling Strategy

```php
// Enhanced error handling in GoogleAuthController
try {
    $googleUser = Socialite::driver('google')->user();
    
    // Check verification status first
    if (!$this->verificationService->canUserAuthenticate($googleUser->getEmail())) {
        return $this->handleTestUserLimitation($googleUser->getEmail());
    }
    
    return $this->ssoService->handleAuthentication($googleUser);
} catch (InvalidStateException $e) {
    return $this->handleAuthenticationFailure($e, 'oauth_state_error');
} catch (GuzzleException $e) {
    return $this->handleAuthenticationFailure($e, 'network_error');
} catch (DomainValidationException $e) {
    return $this->handleAuthenticationFailure($e, 'domain_error');
} catch (VerificationException $e) {
    return $this->handleAuthenticationFailure($e, 'verification_error');
} catch (\Exception $e) {
    return $this->handleAuthenticationFailure($e, 'general_error');
}
```

```php
// Gmail API error handling
try {
    $result = $this->gmailService->sendEmail($to, $subject, $body);
    return $result;
} catch (GoogleQuotaExceededException $e) {
    return $this->fallbackToSmtp($to, $subject, $body);
} catch (GoogleAuthenticationException $e) {
    $this->logGmailError($e);
    return $this->fallbackToSmtp($to, $subject, $body);
} catch (GoogleVerificationException $e) {
    return $this->handleVerificationError($e);
} catch (\Exception $e) {
    $this->logGmailError($e);
    return $this->fallbackToSmtp($to, $subject, $body);
}
```

### User-Friendly Error Messages (Bahasa Melayu)

```php
// resources/lang/ms/auth.php
'google_sso_failed' => 'Pengesahan Google tidak berjaya. Sila cuba lagi atau gunakan log masuk biasa.',
'domain_error' => 'Hanya akaun @motac.gov.my sahaja dibenarkan untuk log masuk.',
'oauth_state_error' => 'Ralat keselamatan semasa pengesahan. Sila cuba lagi.',
'network_error' => 'Masalah sambungan ke Google. Sila cuba lagi atau gunakan log masuk biasa.',
'verification_pending' => 'Aplikasi sedang dalam proses pengesahan Google. Sila hubungi pentadbir sistem.',
'test_user_required' => 'Akaun anda perlu ditambah ke senarai pengguna ujian. Sila hubungi pentadbir sistem.',
'gmail_quota_exceeded' => 'Had penggunaan Gmail API telah dicapai. E-mel akan dihantar melalui sistem biasa.',
'gmail_auth_failed' => 'Pengesahan Gmail tidak berjaya. E-mel akan dihantar melalui sistem biasa.',
```

## Testing Strategy

### Unit Testing

**GoogleSsoService Tests**:

- Domain validation with various email formats
- User creation and linking logic
- Audit logging functionality
- Health check methods
- Verification status handling

**GmailService Tests**:

- OAuth authentication flow
- Service account authentication
- Email sending with various templates
- Attachment handling
- Fallback mechanism testing
- Quota limit handling

**GoogleOAuthVerificationService Tests**:

- Verification status detection
- Test user management
- Verification requirement validation
- Status transition handling

**UnifiedNotificationDispatcher Tests**:

- Multi-channel dispatch logic
- User preference handling
- Critical notification bypass
- Statistics collection
- Fallback behavior

**GoogleAuthController Tests**:

- Redirect flow with proper scopes
- Callback handling with mocked Socialite responses
- Error handling for various failure scenarios
- Session management and redirects
- Verification status handling

### Integration Testing

**OAuth Flow Tests**:

- Complete authentication flow using Socialite fake
- Database transactions and rollbacks
- Event dispatching verification
- Audit log creation

**Gmail API Integration Tests**:

- Email sending through Gmail API
- Template rendering and formatting
- Attachment processing
- Fallback to SMTP testing
- Quota monitoring

**Notification System Tests**:

- End-to-end notification dispatch
- Multi-channel delivery verification
- User preference application
- Critical notification handling

**Admin Interface Tests**:

- Filament resource functionality
- Google services management operations
- Audit log viewing and filtering
- Verification status monitoring

### Property-Based Testing

```php
// Property test for Gmail OAuth production mode authentication
#[Test]
public function gmail_oauth_production_mode_authentication(): void
{
    // Property 3: Gmail OAuth Production Mode Authentication
    $this->forAll(
        Generator\elements(['@motac.gov.my']),
        Generator\string()
    )->then(function (string $domain, string $localPart) {
        $email = $localPart . $domain;
        
        // Set OAuth app to production mode
        $this->verificationService->setProductionMode(true);
        
        $canAuthenticate = $this->verificationService->canUserAuthenticate($email);
        $this->assertTrue($canAuthenticate, "Production mode should allow any @motac.gov.my user");
    });
}

// Property test for Gmail API fallback behavior
#[Test]
public function gmail_api_fallback_behavior(): void
{
    // Property 5: Gmail API Fallback Behavior
    $this->forAll(
        Generator\string(),
        Generator\string(),
        Generator\string()
    )->then(function (string $to, string $subject, string $body) {
        // Simulate Gmail API failure
        $this->gmailService->simulateFailure();
        
        $result = $this->unifiedDispatcher->dispatch(
            new EmailNotification($to, $subject, $body),
            ['gmail']
        );
        
        // Should fallback to SMTP
        $this->assertTrue($result['smtp_used'], "Should fallback to SMTP when Gmail API fails");
        $this->assertFalse($result['gmail_used'], "Gmail should not be used when failing");
    });
}

// Property test for unified notification channel dispatch
#[Test]
public function unified_notification_channel_dispatch(): void
{
    // Property 6: Unified Notification Channel Dispatch
    $this->forAll(
        Generator\elements(['database', 'gmail', 'broadcast']),
        Generator\subset(['database', 'gmail', 'broadcast'])
    )->then(function (string $primaryChannel, array $requestedChannels) {
        $notification = new TestNotification();
        
        $result = $this->unifiedDispatcher->dispatch($notification, $requestedChannels);
        
        foreach ($requestedChannels as $channel) {
            $this->assertTrue(
                $result['channels'][$channel]['attempted'],
                "Channel {$channel} should be attempted when requested"
            );
        }
    });
}

// Property test for domain validation consistency
#[Test]
public function domain_validation_consistency(): void
{
    // Property 1: Domain Validation Consistency
    $this->forAll(
        Generator\string(),
        Generator\elements(['@motac.gov.my', '@MOTAC.GOV.MY', '@gmail.com', '@yahoo.com'])
    )->then(function (string $localPart, string $domain) {
        $email = $localPart . $domain;
        $isValid = $this->ssoService->validateDomain($email);
        
        if (strtolower($domain) === '@motac.gov.my') {
            $this->assertTrue($isValid, "Should accept @motac.gov.my regardless of case");
        } else {
            $this->assertFalse($isValid, "Should reject non-@motac.gov.my domains");
        }
    });
}
```

### Testing Requirements

- **Minimum 95% code coverage** for all Google services-related classes
- **Property-based tests** for all critical correctness properties
- **Integration tests** for complete OAuth and Gmail API flows
- **Performance tests** for authentication and email sending timing
- **Security tests** for token validation and session management
- **Verification tests** for OAuth app verification status handling
- **Fallback tests** for all failure scenarios and recovery mechanisms

### Test Configuration

- **Property tests**: Minimum 100 iterations per test
- **Gmail API tests**: Use test credentials and sandbox environment
- **OAuth tests**: Use Socialite fake for consistent testing
- **Database tests**: Use transactions with rollback for isolation
- **Performance tests**: Set realistic timing expectations (5s for SSO, 10s for Gmail)

## Implementation Notes

### Backward Compatibility

- Existing GoogleAuthController will be enhanced, not replaced
- Current OAuth routes remain unchanged
- Existing user accounts with google_id will continue to work
- Current Gmail API implementation will be enhanced with verification support
- No breaking changes to authentication or email sending flows
- SMTP email sending remains as fallback option

### Performance Considerations

- Implement Redis caching for Google user profile data and OAuth tokens
- Use database transactions for user creation/linking operations
- Optimize audit log writes with queued jobs for both SSO and Gmail operations
- Implement connection pooling for Google API calls
- Cache verification status to reduce API calls
- Use efficient batch processing for bulk email operations

### Security Enhancements

- Add CSRF protection to OAuth state parameter
- Implement rate limiting for authentication attempts and Gmail API calls
- Add IP-based monitoring for suspicious patterns
- Enhance token validation and expiration handling for both SSO and Gmail
- Implement secure storage for service account credentials
- Add encryption for sensitive audit log data

### OAuth Verification Strategy

- **Phase 1**: Implement test user management for immediate access
- **Phase 2**: Prepare verification documentation and submit to Google
- **Phase 3**: Handle verification review process and respond to Google requests
- **Phase 4**: Transition to production mode upon approval
- **Fallback**: Implement service account authentication if verification fails

### Gmail API Integration Strategy

- **Primary**: OAuth 2.0 authentication (current implementation)
- **Secondary**: Service account with domain-wide delegation (if available)
- **Fallback**: SMTP email sending (existing implementation)
- **Monitoring**: Track usage, quotas, and success rates across all methods

### Monitoring and Alerting

- Health check endpoints for Google OAuth and Gmail API availability
- Metrics collection for authentication success/failure rates
- Gmail API quota monitoring and alerting
- Automated alerts for service degradation
- Performance monitoring for authentication and email sending timing
- Verification status change notifications

### Configuration Management

- Environment-specific OAuth credentials (dev, staging, production)
- Separate Gmail API configurations for different environments
- Secure credential storage and rotation procedures
- Configuration validation on application startup
- Health checks for all Google services configurations

This design maintains existing functionality while adding comprehensive enhancements for production readiness, OAuth verification, Gmail API integration, and unified Google services management.
