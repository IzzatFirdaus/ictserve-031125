<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PKS 5.4.3 Password Policy Compliance - Authentication Log Model
 *
 * Logs all authentication attempts for security monitoring per PKS 5.4.3.
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $username
 * @property string $auth_method
 * @property string $status
 * @property string|null $failure_reason
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $location
 * @property int $failed_attempts
 * @property bool $is_lockout_event
 * @property \Illuminate\Support\Carbon|null $lockout_until
 * @property array<string, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @see D03-FR-027 (Authentication Requirements)
 * @see PKS 5.4.3 (Password Policy Requirements)
 *
 * @trace Requirements 27.4, 27.5
 */
class AuthenticationLog extends Model
{
    use HasFactory;

    // Status constants
    public const STATUS_SUCCESS = 'success';

    public const STATUS_FAILED = 'failed';

    public const STATUS_LOCKED = 'locked';

    public const STATUS_EXPIRED = 'expired';

    // Auth method constants
    public const METHOD_LDAP = 'ldap';

    public const METHOD_LOCAL = 'local';

    public const METHOD_SSO = 'sso';

    // Failure reason constants
    public const REASON_INVALID_CREDENTIALS = 'invalid_credentials';

    public const REASON_ACCOUNT_LOCKED = 'account_locked';

    public const REASON_ACCOUNT_DISABLED = 'account_disabled';

    public const REASON_PASSWORD_EXPIRED = 'password_expired';

    public const REASON_USER_NOT_FOUND = 'user_not_found';

    public const REASON_LDAP_ERROR = 'ldap_error';

    protected $fillable = [
        'user_id',
        'username',
        'auth_method',
        'status',
        'failure_reason',
        'ip_address',
        'user_agent',
        'location',
        'failed_attempts',
        'is_lockout_event',
        'lockout_until',
        'metadata',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'failed_attempts' => 'integer',
            'is_lockout_event' => 'boolean',
            'lockout_until' => 'datetime',
            'metadata' => 'array',
        ];
    }

    /**
     * @return BelongsTo<User, AuthenticationLog>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log a successful authentication
     */
    public static function logSuccess(
        string $username,
        string $authMethod,
        ?int $userId = null,
    ): self {
        return self::create([
            'user_id' => $userId,
            'username' => $username,
            'auth_method' => $authMethod,
            'status' => self::STATUS_SUCCESS,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'failed_attempts' => 0,
            'is_lockout_event' => false,
        ]);
    }

    /**
     * Log a failed authentication attempt
     */
    public static function logFailure(
        string $username,
        string $authMethod,
        string $reason,
        int $failedAttempts = 1,
        ?int $userId = null,
    ): self {
        return self::create([
            'user_id' => $userId,
            'username' => $username,
            'auth_method' => $authMethod,
            'status' => self::STATUS_FAILED,
            'failure_reason' => $reason,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'failed_attempts' => $failedAttempts,
            'is_lockout_event' => false,
        ]);
    }

    /**
     * Log an account lockout event
     */
    public static function logLockout(
        string $username,
        string $authMethod,
        int $failedAttempts,
        \DateTimeInterface $lockoutUntil,
        ?int $userId = null,
    ): self {
        return self::create([
            'user_id' => $userId,
            'username' => $username,
            'auth_method' => $authMethod,
            'status' => self::STATUS_LOCKED,
            'failure_reason' => self::REASON_ACCOUNT_LOCKED,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'failed_attempts' => $failedAttempts,
            'is_lockout_event' => true,
            'lockout_until' => $lockoutUntil,
        ]);
    }

    /**
     * Scope: Successful logins only
     *
     * @param  \Illuminate\Database\Eloquent\Builder<AuthenticationLog>  $query
     * @return \Illuminate\Database\Eloquent\Builder<AuthenticationLog>
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    /**
     * Scope: Failed logins only
     *
     * @param  \Illuminate\Database\Eloquent\Builder<AuthenticationLog>  $query
     * @return \Illuminate\Database\Eloquent\Builder<AuthenticationLog>
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope: Lockout events only
     *
     * @param  \Illuminate\Database\Eloquent\Builder<AuthenticationLog>  $query
     * @return \Illuminate\Database\Eloquent\Builder<AuthenticationLog>
     */
    public function scopeLockouts($query)
    {
        return $query->where('is_lockout_event', true);
    }

    /**
     * Scope: For specific username
     *
     * @param  \Illuminate\Database\Eloquent\Builder<AuthenticationLog>  $query
     * @return \Illuminate\Database\Eloquent\Builder<AuthenticationLog>
     */
    public function scopeForUsername($query, string $username)
    {
        return $query->where('username', $username);
    }

    /**
     * Scope: For specific IP address
     *
     * @param  \Illuminate\Database\Eloquent\Builder<AuthenticationLog>  $query
     * @return \Illuminate\Database\Eloquent\Builder<AuthenticationLog>
     */
    public function scopeFromIp($query, string $ip)
    {
        return $query->where('ip_address', $ip);
    }

    /**
     * Get authentication statistics for security monitoring
     *
     * @return array<string, mixed>
     */
    public static function getSecurityStats(): array
    {
        $today = now()->startOfDay();
        $thisWeek = now()->startOfWeek();

        return [
            'total_attempts' => self::count(),
            'today_attempts' => self::where('created_at', '>=', $today)->count(),
            'today_successful' => self::successful()->where('created_at', '>=', $today)->count(),
            'today_failed' => self::failed()->where('created_at', '>=', $today)->count(),
            'today_lockouts' => self::lockouts()->where('created_at', '>=', $today)->count(),
            'week_lockouts' => self::lockouts()->where('created_at', '>=', $thisWeek)->count(),
            'success_rate' => self::where('created_at', '>=', $today)->count() > 0
                ? round((self::successful()->where('created_at', '>=', $today)->count() /
                    self::where('created_at', '>=', $today)->count()) * 100, 2)
                : 100.0,
            'by_method' => self::query()
                ->selectRaw('auth_method, COUNT(*) as count')
                ->groupBy('auth_method')
                ->pluck('count', 'auth_method')
                ->toArray(),
        ];
    }

    /**
     * Get password policy message in Bahasa Melayu
     *
     * @return array<string, string>
     */
    public static function getPasswordPolicyMessages(): array
    {
        return [
            'min_length' => 'Kata laluan mesti mengandungi sekurang-kurangnya 8 aksara.',
            'max_age' => 'Kata laluan mesti ditukar setiap 90 hari.',
            'lockout_threshold' => 'Akaun akan dikunci selepas 3 percubaan log masuk yang gagal.',
            'lockout_duration' => 'Akaun yang dikunci akan dibuka semula selepas 30 minit.',
            'complexity' => 'Kata laluan mesti mengandungi huruf besar, huruf kecil, nombor, dan aksara khas.',
            'history' => 'Kata laluan tidak boleh sama dengan 5 kata laluan sebelumnya.',
            'account_locked' => 'Akaun anda telah dikunci kerana terlalu banyak percubaan log masuk yang gagal. Sila cuba lagi selepas :minutes minit.',
            'invalid_credentials' => 'Nama pengguna atau kata laluan tidak sah. Percubaan :attempts daripada 3.',
            'password_expired' => 'Kata laluan anda telah tamat tempoh. Sila tukar kata laluan anda.',
        ];
    }

    /**
     * Get localized failure message
     */
    public function getLocalizedFailureMessage(): string
    {
        $messages = self::getPasswordPolicyMessages();

        return match ($this->failure_reason) {
            self::REASON_ACCOUNT_LOCKED => str_replace(
                ':minutes',
                (string) ($this->lockout_until?->diffInMinutes(now()) ?? 30),
                $messages['account_locked']
            ),
            self::REASON_INVALID_CREDENTIALS => str_replace(
                ':attempts',
                (string) $this->failed_attempts,
                $messages['invalid_credentials']
            ),
            self::REASON_PASSWORD_EXPIRED => $messages['password_expired'],
            default => 'Pengesahan gagal. Sila cuba lagi.',
        };
    }
}
