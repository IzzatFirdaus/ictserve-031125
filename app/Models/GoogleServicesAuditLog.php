<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Google Services Audit Log Model
 *
 * Tracks all Google services interactions including SSO authentication,
 * Gmail API operations, and other Google service integrations for security
 * monitoring, compliance auditing, and administrative oversight.
 *
 * Supports Requirements 6.3, 9.1 - Enhanced Security and Audit Logging
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $email
 * @property string|null $google_id
 * @property string $service_type
 * @property string $operation_type
 * @property string|null $authentication_method
 * @property string|null $verification_status
 * @property string $ip_address
 * @property string|null $user_agent
 * @property bool $success
 * @property string|null $error_type
 * @property string|null $error_message
 * @property array<string, mixed>|null $metadata
 * @property \Carbon\Carbon $attempted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read User|null $user
 */
class GoogleServicesAuditLog extends Model
{
    /** @use HasFactory<\Database\Factories\GoogleServicesAuditLogFactory> */
    use HasFactory;

    // Service Types
    public const SERVICE_SSO = 'sso';

    public const SERVICE_GMAIL = 'gmail';

    public const SERVICE_CALENDAR = 'calendar';

    public const SERVICE_DRIVE = 'drive';

    // Operation Types
    public const OPERATION_AUTHENTICATE = 'authenticate';

    public const OPERATION_SEND_EMAIL = 'send_email';

    public const OPERATION_AUTHORIZE = 'authorize';

    public const OPERATION_REFRESH_TOKEN = 'refresh_token';

    public const OPERATION_REVOKE_TOKEN = 'revoke_token';

    public const OPERATION_LINK_ACCOUNT = 'link_account';

    public const OPERATION_UNLINK_ACCOUNT = 'unlink_account';

    public const OPERATION_QUOTA_CHECK = 'quota_check';

    // Authentication Methods
    public const AUTH_OAUTH = 'oauth';

    public const AUTH_SERVICE_ACCOUNT = 'service_account';

    public const AUTH_SMTP_FALLBACK = 'smtp_fallback';

    // Verification Statuses
    public const VERIFICATION_VERIFIED = 'verified';

    public const VERIFICATION_TESTING = 'testing';

    public const VERIFICATION_PENDING = 'pending';

    public const VERIFICATION_REJECTED = 'rejected';

    // Error Types
    public const ERROR_DOMAIN = 'domain_error';

    public const ERROR_OAUTH = 'oauth_error';

    public const ERROR_OAUTH_STATE = 'oauth_state_error';

    public const ERROR_NETWORK = 'network_error';

    public const ERROR_QUOTA_EXCEEDED = 'quota_exceeded';

    public const ERROR_RATE_LIMITED = 'rate_limited';

    public const ERROR_VERIFICATION = 'verification_error';

    public const ERROR_AUTHENTICATION = 'authentication_error';

    public const ERROR_GENERAL = 'general_error';

    /**
     * The table associated with the model.
     */
    protected $table = 'google_services_audit_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'email',
        'google_id',
        'service_type',
        'operation_type',
        'authentication_method',
        'verification_status',
        'ip_address',
        'user_agent',
        'success',
        'error_type',
        'error_message',
        'metadata',
        'attempted_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'success' => 'boolean',
            'metadata' => 'array',
            'attempted_at' => 'datetime',
        ];
    }

    // =========================================================================
    // Relationships
    // =========================================================================

    /**
     * Get the user associated with this audit log entry.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // =========================================================================
    // Query Scopes
    // =========================================================================

    /**
     * Scope for successful operations.
     *
     * @param  Builder<GoogleServicesAuditLog>  $query
     * @return Builder<GoogleServicesAuditLog>
     */
    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->where('success', true);
    }

    /**
     * Scope for failed operations.
     *
     * @param  Builder<GoogleServicesAuditLog>  $query
     * @return Builder<GoogleServicesAuditLog>
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('success', false);
    }

    /**
     * Scope for filtering by service type.
     *
     * @param  Builder<GoogleServicesAuditLog>  $query
     * @return Builder<GoogleServicesAuditLog>
     */
    public function scopeForService(Builder $query, string $serviceType): Builder
    {
        return $query->where('service_type', $serviceType);
    }

    /**
     * Scope for SSO operations.
     *
     * @param  Builder<GoogleServicesAuditLog>  $query
     * @return Builder<GoogleServicesAuditLog>
     */
    public function scopeSso(Builder $query): Builder
    {
        return $query->where('service_type', self::SERVICE_SSO);
    }

    /**
     * Scope for Gmail operations.
     *
     * @param  Builder<GoogleServicesAuditLog>  $query
     * @return Builder<GoogleServicesAuditLog>
     */
    public function scopeGmail(Builder $query): Builder
    {
        return $query->where('service_type', self::SERVICE_GMAIL);
    }

    /**
     * Scope for filtering by operation type.
     *
     * @param  Builder<GoogleServicesAuditLog>  $query
     * @return Builder<GoogleServicesAuditLog>
     */
    public function scopeForOperation(Builder $query, string $operationType): Builder
    {
        return $query->where('operation_type', $operationType);
    }

    /**
     * Scope for filtering by authentication method.
     *
     * @param  Builder<GoogleServicesAuditLog>  $query
     * @return Builder<GoogleServicesAuditLog>
     */
    public function scopeWithAuthMethod(Builder $query, string $authMethod): Builder
    {
        return $query->where('authentication_method', $authMethod);
    }

    /**
     * Scope for filtering by verification status.
     *
     * @param  Builder<GoogleServicesAuditLog>  $query
     * @return Builder<GoogleServicesAuditLog>
     */
    public function scopeWithVerificationStatus(Builder $query, string $status): Builder
    {
        return $query->where('verification_status', $status);
    }

    /**
     * Scope for filtering by email address.
     *
     * @param  Builder<GoogleServicesAuditLog>  $query
     * @return Builder<GoogleServicesAuditLog>
     */
    public function scopeForEmail(Builder $query, string $email): Builder
    {
        return $query->where('email', $email);
    }

    /**
     * Scope for filtering by IP address.
     *
     * @param  Builder<GoogleServicesAuditLog>  $query
     * @return Builder<GoogleServicesAuditLog>
     */
    public function scopeFromIp(Builder $query, string $ipAddress): Builder
    {
        return $query->where('ip_address', $ipAddress);
    }

    /**
     * Scope for filtering by error type.
     *
     * @param  Builder<GoogleServicesAuditLog>  $query
     * @return Builder<GoogleServicesAuditLog>
     */
    public function scopeWithErrorType(Builder $query, string $errorType): Builder
    {
        return $query->where('error_type', $errorType);
    }

    /**
     * Scope for filtering by date range.
     *
     * @param  Builder<GoogleServicesAuditLog>  $query
     * @return Builder<GoogleServicesAuditLog>
     */
    public function scopeBetweenDates(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('attempted_at', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by user.
     *
     * @param  Builder<GoogleServicesAuditLog>  $query
     * @return Builder<GoogleServicesAuditLog>
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for recent attempts (last 24 hours).
     *
     * @param  Builder<GoogleServicesAuditLog>  $query
     * @return Builder<GoogleServicesAuditLog>
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->where('attempted_at', '>=', now()->subDay());
    }

    /**
     * Scope for quota-related errors.
     *
     * @param  Builder<GoogleServicesAuditLog>  $query
     * @return Builder<GoogleServicesAuditLog>
     */
    public function scopeQuotaErrors(Builder $query): Builder
    {
        return $query->whereIn('error_type', [self::ERROR_QUOTA_EXCEEDED, self::ERROR_RATE_LIMITED]);
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    /**
     * Check if this was a successful operation.
     */
    public function wasSuccessful(): bool
    {
        return $this->success === true;
    }

    /**
     * Check if this was a failed operation.
     */
    public function wasFailed(): bool
    {
        return $this->success === false;
    }

    /**
     * Check if this is an SSO operation.
     */
    public function isSsoOperation(): bool
    {
        return $this->service_type === self::SERVICE_SSO;
    }

    /**
     * Check if this is a Gmail operation.
     */
    public function isGmailOperation(): bool
    {
        return $this->service_type === self::SERVICE_GMAIL;
    }

    /**
     * Get a human-readable description of the service type.
     */
    public function getServiceTypeLabel(): string
    {
        return match ($this->service_type) {
            self::SERVICE_SSO => __('google_services.service_types.sso'),
            self::SERVICE_GMAIL => __('google_services.service_types.gmail'),
            self::SERVICE_CALENDAR => __('google_services.service_types.calendar'),
            self::SERVICE_DRIVE => __('google_services.service_types.drive'),
            default => $this->service_type,
        };
    }

    /**
     * Get a human-readable description of the operation type.
     */
    public function getOperationTypeLabel(): string
    {
        return match ($this->operation_type) {
            self::OPERATION_AUTHENTICATE => __('google_services.operation_types.authenticate'),
            self::OPERATION_SEND_EMAIL => __('google_services.operation_types.send_email'),
            self::OPERATION_AUTHORIZE => __('google_services.operation_types.authorize'),
            self::OPERATION_REFRESH_TOKEN => __('google_services.operation_types.refresh_token'),
            self::OPERATION_REVOKE_TOKEN => __('google_services.operation_types.revoke_token'),
            self::OPERATION_LINK_ACCOUNT => __('google_services.operation_types.link_account'),
            self::OPERATION_UNLINK_ACCOUNT => __('google_services.operation_types.unlink_account'),
            self::OPERATION_QUOTA_CHECK => __('google_services.operation_types.quota_check'),
            default => $this->operation_type,
        };
    }

    /**
     * Get a human-readable description of the error type.
     */
    public function getErrorTypeLabel(): ?string
    {
        if ($this->error_type === null) {
            return null;
        }

        return match ($this->error_type) {
            self::ERROR_DOMAIN => __('google_services.error_types.domain_error'),
            self::ERROR_OAUTH => __('google_services.error_types.oauth_error'),
            self::ERROR_OAUTH_STATE => __('google_services.error_types.oauth_state_error'),
            self::ERROR_NETWORK => __('google_services.error_types.network_error'),
            self::ERROR_QUOTA_EXCEEDED => __('google_services.error_types.quota_exceeded'),
            self::ERROR_RATE_LIMITED => __('google_services.error_types.rate_limited'),
            self::ERROR_VERIFICATION => __('google_services.error_types.verification_error'),
            self::ERROR_AUTHENTICATION => __('google_services.error_types.authentication_error'),
            self::ERROR_GENERAL => __('google_services.error_types.general_error'),
            default => $this->error_type,
        };
    }

    /**
     * Get a human-readable description of the authentication method.
     */
    public function getAuthMethodLabel(): ?string
    {
        if ($this->authentication_method === null) {
            return null;
        }

        return match ($this->authentication_method) {
            self::AUTH_OAUTH => __('google_services.auth_methods.oauth'),
            self::AUTH_SERVICE_ACCOUNT => __('google_services.auth_methods.service_account'),
            self::AUTH_SMTP_FALLBACK => __('google_services.auth_methods.smtp_fallback'),
            default => $this->authentication_method,
        };
    }

    /**
     * Get metadata value by key.
     *
     * @param  mixed  $default
     * @return mixed
     */
    public function getMetadata(string $key, $default = null)
    {
        return data_get($this->metadata, $key, $default);
    }

    // =========================================================================
    // Static Factory Methods
    // =========================================================================

    /**
     * Create a log entry for a successful SSO authentication.
     *
     * @param  array<string, mixed>  $data
     */
    public static function logSsoSuccess(array $data): self
    {
        return self::create([
            'user_id' => $data['user_id'] ?? null,
            'email' => $data['email'],
            'google_id' => $data['google_id'] ?? null,
            'service_type' => self::SERVICE_SSO,
            'operation_type' => self::OPERATION_AUTHENTICATE,
            'authentication_method' => self::AUTH_OAUTH,
            'verification_status' => $data['verification_status'] ?? null,
            'ip_address' => $data['ip_address'],
            'user_agent' => $data['user_agent'] ?? null,
            'success' => true,
            'error_type' => null,
            'error_message' => null,
            'metadata' => $data['metadata'] ?? null,
            'attempted_at' => now(),
        ]);
    }

    /**
     * Create a log entry for a failed SSO authentication.
     *
     * @param  array<string, mixed>  $data
     */
    public static function logSsoFailure(array $data): self
    {
        return self::create([
            'user_id' => $data['user_id'] ?? null,
            'email' => $data['email'],
            'google_id' => $data['google_id'] ?? null,
            'service_type' => self::SERVICE_SSO,
            'operation_type' => self::OPERATION_AUTHENTICATE,
            'authentication_method' => self::AUTH_OAUTH,
            'verification_status' => $data['verification_status'] ?? null,
            'ip_address' => $data['ip_address'],
            'user_agent' => $data['user_agent'] ?? null,
            'success' => false,
            'error_type' => $data['error_type'] ?? self::ERROR_GENERAL,
            'error_message' => $data['error_message'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'attempted_at' => now(),
        ]);
    }

    /**
     * Create a log entry for a successful Gmail operation.
     *
     * @param  array<string, mixed>  $data
     */
    public static function logGmailSuccess(array $data): self
    {
        return self::create([
            'user_id' => $data['user_id'] ?? null,
            'email' => $data['email'],
            'google_id' => $data['google_id'] ?? null,
            'service_type' => self::SERVICE_GMAIL,
            'operation_type' => $data['operation_type'] ?? self::OPERATION_SEND_EMAIL,
            'authentication_method' => $data['authentication_method'] ?? self::AUTH_OAUTH,
            'verification_status' => $data['verification_status'] ?? null,
            'ip_address' => $data['ip_address'] ?? request()->ip() ?? '127.0.0.1',
            'user_agent' => $data['user_agent'] ?? request()->userAgent(),
            'success' => true,
            'error_type' => null,
            'error_message' => null,
            'metadata' => $data['metadata'] ?? null,
            'attempted_at' => now(),
        ]);
    }

    /**
     * Create a log entry for a failed Gmail operation.
     *
     * @param  array<string, mixed>  $data
     */
    public static function logGmailFailure(array $data): self
    {
        return self::create([
            'user_id' => $data['user_id'] ?? null,
            'email' => $data['email'],
            'google_id' => $data['google_id'] ?? null,
            'service_type' => self::SERVICE_GMAIL,
            'operation_type' => $data['operation_type'] ?? self::OPERATION_SEND_EMAIL,
            'authentication_method' => $data['authentication_method'] ?? self::AUTH_OAUTH,
            'verification_status' => $data['verification_status'] ?? null,
            'ip_address' => $data['ip_address'] ?? request()->ip() ?? '127.0.0.1',
            'user_agent' => $data['user_agent'] ?? request()->userAgent(),
            'success' => false,
            'error_type' => $data['error_type'] ?? self::ERROR_GENERAL,
            'error_message' => $data['error_message'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'attempted_at' => now(),
        ]);
    }

    /**
     * Create a generic log entry for any Google service operation.
     *
     * @param  array<string, mixed>  $data
     */
    public static function log(array $data): self
    {
        return self::create([
            'user_id' => $data['user_id'] ?? null,
            'email' => $data['email'],
            'google_id' => $data['google_id'] ?? null,
            'service_type' => $data['service_type'],
            'operation_type' => $data['operation_type'],
            'authentication_method' => $data['authentication_method'] ?? null,
            'verification_status' => $data['verification_status'] ?? null,
            'ip_address' => $data['ip_address'] ?? request()->ip() ?? '127.0.0.1',
            'user_agent' => $data['user_agent'] ?? request()->userAgent(),
            'success' => $data['success'] ?? false,
            'error_type' => $data['error_type'] ?? null,
            'error_message' => $data['error_message'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'attempted_at' => $data['attempted_at'] ?? now(),
        ]);
    }

    // =========================================================================
    // Statistics Methods
    // =========================================================================

    /**
     * Get statistics for a specific service type.
     *
     * @return array<string, mixed>
     */
    public static function getServiceStatistics(string $serviceType, ?int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $query = self::query()
            ->where('service_type', $serviceType)
            ->where('attempted_at', '>=', $startDate);

        $total = $query->count();
        $successful = (clone $query)->where('success', true)->count();
        $failed = $total - $successful;

        return [
            'total' => $total,
            'successful' => $successful,
            'failed' => $failed,
            'success_rate' => $total > 0 ? round(($successful / $total) * 100, 2) : 0,
            'period_days' => $days,
        ];
    }

    /**
     * Get error breakdown for a specific service type.
     *
     * @return array<string, int>
     */
    public static function getErrorBreakdown(string $serviceType, ?int $days = 30): array
    {
        $startDate = now()->subDays($days);

        return self::query()
            ->where('service_type', $serviceType)
            ->where('success', false)
            ->where('attempted_at', '>=', $startDate)
            ->whereNotNull('error_type')
            ->selectRaw('error_type, COUNT(*) as count')
            ->groupBy('error_type')
            ->pluck('count', 'error_type')
            ->toArray();
    }
}
