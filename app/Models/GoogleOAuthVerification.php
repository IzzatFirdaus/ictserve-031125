<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Google OAuth Verification Model
 *
 * Tracks OAuth app verification status, test users, and verification documents
 * for managing Google OAuth verification process.
 *
 * Supports Requirements 1.1, 4.1 - OAuth Verification Management
 *
 * @property int $id
 * @property string $client_id
 * @property string $verification_status
 * @property array<string>|null $test_users
 * @property \Carbon\Carbon|null $verification_submitted_at
 * @property \Carbon\Carbon|null $verification_approved_at
 * @property array<string, mixed>|null $verification_documents
 * @property array<string, mixed>|null $quota_limits
 * @property \Carbon\Carbon|null $last_status_check
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @method static Builder<static>|GoogleOAuthVerification verified()
 * @method static Builder<static>|GoogleOAuthVerification testing()
 * @method static Builder<static>|GoogleOAuthVerification pending()
 * @method static Builder<static>|GoogleOAuthVerification rejected()
 * @method static Builder<static>|GoogleOAuthVerification forClientId(string $clientId)
 * @method static Builder<static>|GoogleOAuthVerification newModelQuery()
 * @method static Builder<static>|GoogleOAuthVerification newQuery()
 * @method static Builder<static>|GoogleOAuthVerification query()
 *
 * @mixin \Eloquent
 */
class GoogleOAuthVerification extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'google_oauth_verifications';

    /**
     * Verification status constants
     */
    public const STATUS_VERIFIED = 'verified';

    public const STATUS_PENDING = 'pending';

    public const STATUS_TESTING = 'testing';

    public const STATUS_REJECTED = 'rejected';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'client_id',
        'verification_status',
        'test_users',
        'verification_submitted_at',
        'verification_approved_at',
        'verification_documents',
        'quota_limits',
        'last_status_check',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'test_users' => 'array',
            'verification_documents' => 'array',
            'quota_limits' => 'array',
            'verification_submitted_at' => 'datetime',
            'verification_approved_at' => 'datetime',
            'last_status_check' => 'datetime',
        ];
    }

    // =========================================================================
    // Query Scopes
    // =========================================================================

    /**
     * Scope for verified OAuth apps.
     *
     * @param  Builder<GoogleOAuthVerification>  $query
     * @return Builder<GoogleOAuthVerification>
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('verification_status', self::STATUS_VERIFIED);
    }

    /**
     * Scope for testing mode OAuth apps.
     *
     * @param  Builder<GoogleOAuthVerification>  $query
     * @return Builder<GoogleOAuthVerification>
     */
    public function scopeTesting(Builder $query): Builder
    {
        return $query->where('verification_status', self::STATUS_TESTING);
    }

    /**
     * Scope for pending verification OAuth apps.
     *
     * @param  Builder<GoogleOAuthVerification>  $query
     * @return Builder<GoogleOAuthVerification>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('verification_status', self::STATUS_PENDING);
    }

    /**
     * Scope for rejected OAuth apps.
     *
     * @param  Builder<GoogleOAuthVerification>  $query
     * @return Builder<GoogleOAuthVerification>
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('verification_status', self::STATUS_REJECTED);
    }

    /**
     * Scope for filtering by client ID.
     *
     * @param  Builder<GoogleOAuthVerification>  $query
     * @return Builder<GoogleOAuthVerification>
     */
    public function scopeForClientId(Builder $query, string $clientId): Builder
    {
        return $query->where('client_id', $clientId);
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    /**
     * Check if OAuth app is verified.
     */
    public function isVerified(): bool
    {
        return $this->verification_status === self::STATUS_VERIFIED;
    }

    /**
     * Check if OAuth app is in testing mode.
     */
    public function isInTestingMode(): bool
    {
        return $this->verification_status === self::STATUS_TESTING;
    }

    /**
     * Check if OAuth app verification is pending.
     */
    public function isPending(): bool
    {
        return $this->verification_status === self::STATUS_PENDING;
    }

    /**
     * Check if OAuth app verification was rejected.
     */
    public function isRejected(): bool
    {
        return $this->verification_status === self::STATUS_REJECTED;
    }

    /**
     * Get the number of test users.
     */
    public function getTestUserCount(): int
    {
        return count($this->test_users ?? []);
    }

    /**
     * Check if a specific email is a test user.
     */
    public function isTestUser(string $email): bool
    {
        $testUsers = $this->test_users ?? [];

        return in_array(strtolower(trim($email)), array_map('strtolower', $testUsers), true);
    }

    /**
     * Add a test user.
     */
    public function addTestUser(string $email): bool
    {
        $email = strtolower(trim($email));
        $testUsers = $this->test_users ?? [];

        if (in_array($email, $testUsers, true)) {
            return false;
        }

        $testUsers[] = $email;
        $this->test_users = $testUsers;

        return true;
    }

    /**
     * Remove a test user.
     */
    public function removeTestUser(string $email): bool
    {
        $email = strtolower(trim($email));
        $testUsers = $this->test_users ?? [];

        $key = array_search($email, $testUsers, true);
        if ($key === false) {
            return false;
        }

        unset($testUsers[$key]);
        $this->test_users = array_values($testUsers);

        return true;
    }

    /**
     * Get human-readable status label.
     */
    public function getStatusLabel(): string
    {
        return match ($this->verification_status) {
            self::STATUS_VERIFIED => __('auth.oauth_status.verified'),
            self::STATUS_PENDING => __('auth.oauth_status.pending'),
            self::STATUS_TESTING => __('auth.oauth_status.testing'),
            self::STATUS_REJECTED => __('auth.oauth_status.rejected'),
            default => __('auth.oauth_status.unknown'),
        };
    }

    /**
     * Get quota limit for a specific type.
     */
    public function getQuotaLimit(string $type): ?int
    {
        $limits = $this->quota_limits ?? [];

        return $limits[$type] ?? null;
    }

    /**
     * Set quota limit for a specific type.
     */
    public function setQuotaLimit(string $type, int $limit): void
    {
        $limits = $this->quota_limits ?? [];
        $limits[$type] = $limit;
        $this->quota_limits = $limits;
    }

    /**
     * Get verification document by key.
     */
    public function getVerificationDocument(string $key): mixed
    {
        $documents = $this->verification_documents ?? [];

        return $documents[$key] ?? null;
    }

    /**
     * Set verification document.
     */
    public function setVerificationDocument(string $key, mixed $value): void
    {
        $documents = $this->verification_documents ?? [];
        $documents[$key] = $value;
        $this->verification_documents = $documents;
    }

    /**
     * Mark verification as submitted.
     */
    public function markAsSubmitted(): void
    {
        $this->verification_status = self::STATUS_PENDING;
        $this->verification_submitted_at = now();
    }

    /**
     * Mark verification as approved.
     */
    public function markAsApproved(): void
    {
        $this->verification_status = self::STATUS_VERIFIED;
        $this->verification_approved_at = now();
    }

    /**
     * Mark verification as rejected.
     */
    public function markAsRejected(): void
    {
        $this->verification_status = self::STATUS_REJECTED;
    }
}
