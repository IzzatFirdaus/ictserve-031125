<?php

declare(strict_types=1);

// name: UserConsent
// description: User consent tracking for PDPA compliance (Personal Data Protection Act 2010, Malaysia)
// author: dev-team@motac.gov.my
// trace: SRS-NFR-005; D03 §15.4; D11 §14.4; Requirement 14.4
// last-updated: 2025-11-06

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserConsent extends Model
{
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
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get only active consents (granted = true, not revoked)
     */
    public function scopeActive($query)
    {
        return $query->where('granted', true)->whereNull('revoked_at');
    }

    /**
     * Scope: Get consents by type
     */
    public function scopeOfType($query, string $type)
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
