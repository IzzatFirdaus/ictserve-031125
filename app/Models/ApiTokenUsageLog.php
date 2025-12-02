<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

// TODO: Install Laravel Sanctum (composer require laravel/sanctum)
// use Laravel\Sanctum\PersonalAccessToken;

/**
 * ApiTokenUsageLog Model - v3.5.0 True Hybrid Architecture
 *
 * Tracks API token usage for security auditing and monitoring.
 * Records all API requests made with Sanctum tokens.
 *
 * @see D03 Software Requirements Specification - Requirement 37.5
 * @see D04 Software Design Document - API Token Service
 * @see D09 Database Documentation - api_token_usage_logs table
 *
 * @property int $id
 * @property int $personal_access_token_id
 * @property int $user_id
 * @property string $action
 * @property string $endpoint
 * @property string $ip_hash
 * @property string|null $user_agent
 * @property int|null $response_status
 * @property \Carbon\Carbon $created_at
 */
class ApiTokenUsageLog extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\ApiTokenUsageLogFactory> */
    use HasFactory;

    use \OwenIt\Auditing\Auditable;

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

    // Relationships

    /**
     * Get the personal access token this log belongs to
     * TODO: Update return type when Laravel Sanctum is installed
     *
     * @return BelongsTo<Model, ApiTokenUsageLog>
     */
    public function personalAccessToken(): BelongsTo
    {
        // TODO: Update to PersonalAccessToken::class when Sanctum is installed
        return $this->belongsTo(Model::class, 'personal_access_token_id');
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
