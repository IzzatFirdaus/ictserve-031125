<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\PersonalAccessToken;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * ApiTokenUsageLog Model - v3.5.0 True Hybrid Architecture
 * 
 * Tracks API token usage for security auditing and monitoring.
 * Records all API requests made with Sanctum tokens.
 *
 * @see D03 Software Requirements Specification - Requirement 37.5
 * @see D04 Software Design Document - API Token Service
 * @see D09 Database Documentation - api_token_usage_logs table
 * @property int $id
 * @property int $personal_access_token_id
 * @property int $user_id
 * @property string $action
 * @property string $endpoint
 * @property string $ip_hash
 * @property string|null $user_agent
 * @property int|null $response_status
 * @property \Carbon\Carbon $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read PersonalAccessToken $personalAccessToken
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiTokenUsageLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiTokenUsageLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiTokenUsageLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiTokenUsageLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiTokenUsageLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiTokenUsageLog whereEndpoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiTokenUsageLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiTokenUsageLog whereIpHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiTokenUsageLog wherePersonalAccessTokenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiTokenUsageLog whereResponseStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiTokenUsageLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiTokenUsageLog whereUserId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @mixin \Eloquent
 */
class ApiTokenUsageLog extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\ApiTokenUsageLogFactory> */
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use LogsActivity;

    public $timestamps = false; // Using created_at only

    protected $fillable = [
        'personal_access_token_id',
        'user_id',
        'action',
        'endpoint',
        'ip_hash',
        'user_agent',
        'response_status',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'response_status' => 'integer',
        ];
    }

    /** @var array<int, string> */
    protected $auditInclude = [
        'personal_access_token_id',
        'user_id',
        'action',
        'endpoint',
        'response_status',
    ];

    /**
     * Spatie Activity Log configuration
     *
     * @see D09 §4.7 - Activity Log Requirements
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'personal_access_token_id',
                'user_id',
                'action',
                'endpoint',
                'response_status',
            ])
            ->logOnlyDirty()
            ->useLogName('api_token_usage')
            ->setDescriptionForEvent(fn(string $eventName) => "API token usage {$eventName}");
    }

    // Relationships

    /**
     * Get the personal access token this log belongs to
     *
     * @return BelongsTo<PersonalAccessToken, ApiTokenUsageLog>
     */
    public function personalAccessToken(): BelongsTo
    {
        return $this->belongsTo(PersonalAccessToken::class, 'personal_access_token_id');
    }

    /**
     * Get the user who made the API request
     *
     * @return BelongsTo<User, ApiTokenUsageLog>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helper Methods

    /**
     * Check if request was successful (2xx status code)
     */
    public function isSuccessful(): bool
    {
        return $this->response_status >= 200 && $this->response_status < 300;
    }

    /**
     * Check if request failed (4xx or 5xx status code)
     */
    public function isFailed(): bool
    {
        return $this->response_status >= 400;
    }

    /**
     * Get status code category
     */
    public function getStatusCategory(): string
    {
        return match (true) {
            $this->response_status >= 500 => 'server_error',
            $this->response_status >= 400 => 'client_error',
            $this->response_status >= 300 => 'redirect',
            $this->response_status >= 200 => 'success',
            default => 'unknown',
        };
    }
}
