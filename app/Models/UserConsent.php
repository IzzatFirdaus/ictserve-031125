<?php

declare(strict_types=1);

// name: UserConsent
// description: User consent tracking for PDPA compliance (Personal Data Protection Act 2010, Malaysia)
// author: dev-team@motac.gov.my
// trace: SRS-NFR-005; D03 §15.4; D11 §14.4; Requirement 14.4
// last-updated: 2025-11-06

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $consent_type
 * @property string $consent_statement
 * @property string $version
 * @property bool $granted
 * @property string $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $consented_at
 * @property \Illuminate\Support\Carbon|null $revoked_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $history_description
 * @property-read \App\Models\User $user
 * @method static Builder<static>|UserConsent active()
 * @method static Builder<static>|UserConsent newModelQuery()
 * @method static Builder<static>|UserConsent newQuery()
 * @method static Builder<static>|UserConsent ofType(string $type)
 * @method static Builder<static>|UserConsent query()
 * @method static Builder<static>|UserConsent whereConsentStatement($value)
 * @method static Builder<static>|UserConsent whereConsentType($value)
 * @method static Builder<static>|UserConsent whereConsentedAt($value)
 * @method static Builder<static>|UserConsent whereCreatedAt($value)
 * @method static Builder<static>|UserConsent whereGranted($value)
 * @method static Builder<static>|UserConsent whereId($value)
 * @method static Builder<static>|UserConsent whereIpAddress($value)
 * @method static Builder<static>|UserConsent whereRevokedAt($value)
 * @method static Builder<static>|UserConsent whereUpdatedAt($value)
 * @method static Builder<static>|UserConsent whereUserAgent($value)
 * @method static Builder<static>|UserConsent whereUserId($value)
 * @method static Builder<static>|UserConsent whereVersion($value)
 * @mixin \Eloquent
 */
class UserConsent extends Model
{
    /** @use HasFactory<\Database\Factories\UserConsentFactory> */
    use HasFactory;

    /**
     * Consent types
     */
    public const TYPE_DATA_PROCESSING = 'data_processing';

    public const TYPE_MARKETING = 'marketing';

    public const TYPE_ANALYTICS = 'analytics';

    protected $fillable = [
        'user_id',
        'consent_type',
        'consent_statement',
        'version',
        'granted',
        'ip_address',
        'user_agent',
        'consented_at',
        'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'granted' => 'boolean',
            'consented_at' => 'datetime',
            'revoked_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user who gave consent
     *
     * @return BelongsTo<User, UserConsent>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get only active consents (granted = true, not revoked)
     *
     * @param  Builder<UserConsent>  $query
     * @return Builder<UserConsent>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('granted', true)->whereNull('revoked_at');
    }

    /**
     * Scope: Get consents by type
     *
     * @param  Builder<UserConsent>  $query
     * @return Builder<UserConsent>
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('consent_type', $type);
    }

    /**
     * Check if consent is currently active
     */
    public function isActive(): bool
    {
        return $this->granted && $this->revoked_at === null;
    }

    /**
     * Grant consent
     */
    public function grant(string $ipAddress, ?string $userAgent = null): void
    {
        $this->update([
            'granted' => true,
            'consented_at' => now(),
            'revoked_at' => null,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    /**
     * Revoke consent
     */
    public function revoke(): void
    {
        $this->update([
            'granted' => false,
            'revoked_at' => now(),
        ]);
    }

    /**
     * Get formatted consent history
     */
    public function getHistoryDescriptionAttribute(): string
    {
        if ($this->isActive()) {
            return __('portal.consent.granted_on', [
                'date' => $this->consented_at->format('Y-m-d H:i:s'),
            ]);
        }

        if ($this->revoked_at) {
            return __('portal.consent.revoked_on', [
                'date' => $this->revoked_at->format('Y-m-d H:i:s'),
            ]);
        }

        return __('portal.consent.pending');
    }
}
