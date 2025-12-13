<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * SSO Audit Log Model
 *
 * Tracks all Google SSO authentication attempts for security monitoring,
 * compliance auditing, and administrative oversight.
 *
 * Supports Requirements 4.1, 4.2 - Enhanced Security and Audit Logging
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $email
 * @property string|null $google_id
 * @property string $ip_address
 * @property string|null $user_agent
 * @property bool $success
 * @property string|null $error_type
 * @property string|null $error_message
 * @property \Carbon\Carbon $attempted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read User|null $user
 */
class SsoAuditLog extends Model
{
    /** @use HasFactory<\Database\Factories\SsoAuditLogFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'sso_audit_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'email',
        'google_id',
        'ip_address',
        'user_agent',
        'success',
        'error_type',
        'error_message',
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
     * Scope for successful authentication attempts.
     *
     * @param  Builder<SsoAuditLog>  $query
     * @return Builder<SsoAuditLog>
     */
    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->where('success', true);
    }

    /**
     * Scope for failed authentication attempts.
     *
     * @param  Builder<SsoAuditLog>  $query
     * @return Builder<SsoAuditLog>
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('success', false);
    }

    /**
     * Scope for filtering by email address.
     *
     * @param  Builder<SsoAuditLog>  $query
     * @return Builder<SsoAuditLog>
     */
    public function scopeForEmail(Builder $query, string $email): Builder
    {
        return $query->where('email', $email);
    }

    /**
     * Scope for filtering by IP address.
     *
     * @param  Builder<SsoAuditLog>  $query
     * @return Builder<SsoAuditLog>
     */
    public function scopeFromIp(Builder $query, string $ipAddress): Builder
    {
        return $query->where('ip_address', $ipAddress);
    }

    /**
     * Scope for filtering by error type.
     *
     * @param  Builder<SsoAuditLog>  $query
     * @return Builder<SsoAuditLog>
     */
    public function scopeWithErrorType(Builder $query, string $errorType): Builder
    {
        return $query->where('error_type', $errorType);
    }

    /**
     * Scope for filtering by date range.
     *
     * @param  Builder<SsoAuditLog>  $query
     * @return Builder<SsoAuditLog>
     */
    public function scopeBetweenDates(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('attempted_at', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by user.
     *
     * @param  Builder<SsoAuditLog>  $query
     * @return Builder<SsoAuditLog>
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for recent attempts (last 24 hours).
     *
     * @param  Builder<SsoAuditLog>  $query
     * @return Builder<SsoAuditLog>
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->where('attempted_at', '>=', now()->subDay());
    }

    /**
     * Scope for domain validation errors.
     *
     * @param  Builder<SsoAuditLog>  $query
     * @return Builder<SsoAuditLog>
     */
    public function scopeDomainErrors(Builder $query): Builder
    {
        return $query->where('error_type', 'domain_error');
    }

    /**
     * Scope for OAuth errors.
     *
     * @param  Builder<SsoAuditLog>  $query
     * @return Builder<SsoAuditLog>
     */
    public function scopeOAuthErrors(Builder $query): Builder
    {
        return $query->where('error_type', 'oauth_error');
    }

    /**
     * Scope for network errors.
     *
     * @param  Builder<SsoAuditLog>  $query
     * @return Builder<SsoAuditLog>
     */
    public function scopeNetworkErrors(Builder $query): Builder
    {
        return $query->where('error_type', 'network_error');
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    /**
     * Check if this was a successful authentication attempt.
     */
    public function wasSuccessful(): bool
    {
        return $this->success === true;
    }

    /**
     * Check if this was a failed authentication attempt.
     */
    public function wasFailed(): bool
    {
        return $this->success === false;
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
            'domain_error' => __('auth.error_types.domain_error'),
            'oauth_error' => __('auth.error_types.oauth_error'),
            'oauth_state_error' => __('auth.error_types.oauth_state_error'),
            'network_error' => __('auth.error_types.network_error'),
            'general_error' => __('auth.error_types.general_error'),
            default => $this->error_type,
        };
    }

    /**
     * Create a log entry for a successful authentication.
     *
     * @param  array<string, mixed>  $data
     */
    public static function logSuccess(array $data): self
    {
        return self::create([
            'user_id' => $data['user_id'] ?? null,
            'email' => $data['email'],
            'google_id' => $data['google_id'] ?? null,
            'ip_address' => $data['ip_address'],
            'user_agent' => $data['user_agent'] ?? null,
            'success' => true,
            'error_type' => null,
            'error_message' => null,
            'attempted_at' => now(),
        ]);
    }

    /**
     * Create a log entry for a failed authentication.
     *
     * @param  array<string, mixed>  $data
     */
    public static function logFailure(array $data): self
    {
        return self::create([
            'user_id' => $data['user_id'] ?? null,
            'email' => $data['email'],
            'google_id' => $data['google_id'] ?? null,
            'ip_address' => $data['ip_address'],
            'user_agent' => $data['user_agent'] ?? null,
            'success' => false,
            'error_type' => $data['error_type'] ?? 'general_error',
            'error_message' => $data['error_message'] ?? null,
            'attempted_at' => now(),
        ]);
    }
}
