<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Blocked IP Model
 * 
 * Stores IP addresses blocked for abuse prevention.
 * Supports both manual (admin) and automatic (rate limit violation) blocking.
 *
 * @property int $id
 * @property string $ip_address
 * @property string|null $reason
 * @property string $type
 * @property int $violation_count
 * @property \Carbon\Carbon $blocked_at
 * @property \Carbon\Carbon|null $expires_at
 * @property int|null $blocked_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\User|null $blockedByUser
 * @method static Builder<static>|BlockedIp active()
 * @method static Builder<static>|BlockedIp expired()
 * @method static Builder<static>|BlockedIp newModelQuery()
 * @method static Builder<static>|BlockedIp newQuery()
 * @method static Builder<static>|BlockedIp query()
 * @method static Builder<static>|BlockedIp whereBlockedAt($value)
 * @method static Builder<static>|BlockedIp whereBlockedBy($value)
 * @method static Builder<static>|BlockedIp whereCreatedAt($value)
 * @method static Builder<static>|BlockedIp whereExpiresAt($value)
 * @method static Builder<static>|BlockedIp whereId($value)
 * @method static Builder<static>|BlockedIp whereIpAddress($value)
 * @method static Builder<static>|BlockedIp whereReason($value)
 * @method static Builder<static>|BlockedIp whereType($value)
 * @method static Builder<static>|BlockedIp whereUpdatedAt($value)
 * @method static Builder<static>|BlockedIp whereViolationCount($value)
 * @mixin \Eloquent
 */
class BlockedIp extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ip_address',
        'reason',
        'type',
        'violation_count',
        'blocked_at',
        'expires_at',
        'blocked_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'blocked_at' => 'datetime',
            'expires_at' => 'datetime',
            'violation_count' => 'integer',
        ];
    }

    /**
     * Get the user who blocked this IP.
     */
    public function blockedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }

    /**
     * Scope to get only active (non-expired) blocks.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function (Builder $q): void {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope to get expired blocks.
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    /**
     * Check if this block is currently active.
     */
    public function isActive(): bool
    {
        return $this->expires_at === null || $this->expires_at->isFuture();
    }

    /**
     * Check if this block has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /**
     * Check if an IP address is currently blocked.
     */
    public static function isBlocked(string $ipAddress): bool
    {
        return static::where('ip_address', $ipAddress)
            ->active()
            ->exists();
    }

    /**
     * Get the active block for an IP address.
     */
    public static function getActiveBlock(string $ipAddress): ?self
    {
        return static::where('ip_address', $ipAddress)
            ->active()
            ->first();
    }
}
