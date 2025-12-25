<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DLP Audit Log Model
 *
 * PKS 9.2.1 Compliance - Data Transfer and DLP Audit Logging
 *
 * Records all DLP filter decisions for compliance monitoring.
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $session_id
 * @property string $classification
 * @property string $routing_decision
 * @property int $risk_score
 * @property array<string, mixed>|null $detected_patterns
 * @property int $content_length
 * @property string|null $content_hash
 * @property string $target_provider
 * @property string|null $model_id
 * @property string|null $operation_type
 * @property string|null $source_component
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property bool $was_bypassed
 * @property int|null $bypassed_by
 * @property string|null $bypass_reason
 * @property \Illuminate\Support\Carbon $processed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User|null $user
 * @property-read User|null $bypassedByUser
 *
 * @see D03-FR-025.4 (DLP audit logging)
 *
 * @trace Requirements 25.4, 25.6
 */
class DlpAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'classification',
        'routing_decision',
        'risk_score',
        'detected_patterns',
        'content_length',
        'content_hash',
        'target_provider',
        'model_id',
        'operation_type',
        'source_component',
        'ip_address',
        'user_agent',
        'was_bypassed',
        'bypassed_by',
        'bypass_reason',
        'processed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'detected_patterns' => 'array',
            'risk_score' => 'integer',
            'content_length' => 'integer',
            'was_bypassed' => 'boolean',
            'processed_at' => 'datetime',
        ];
    }

    // Classification constants
    public const CLASSIFICATION_PUBLIC = 'PUBLIC';

    public const CLASSIFICATION_SENSITIVE = 'SENSITIVE';

    public const CLASSIFICATION_RESTRICTED = 'RESTRICTED';

    // Routing decision constants
    public const ROUTE_CLOUD_ALLOWED = 'CLOUD_ALLOWED';

    public const ROUTE_LOCAL_ONLY = 'LOCAL_ONLY';

    public const ROUTE_BLOCKED = 'BLOCKED';

    // Provider constants
    public const PROVIDER_BEDROCK = 'bedrock';

    public const PROVIDER_OLLAMA = 'ollama';

    public const PROVIDER_BLOCKED = 'blocked';

    /**
     * User who triggered the DLP check
     *
     * @return BelongsTo<User, self>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * User who bypassed the DLP filter (if applicable)
     *
     * @return BelongsTo<User, self>
     */
    public function bypassedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bypassed_by');
    }

    /**
     * Create a DLP audit log entry
     *
     * @param  array<string, mixed>  $dlpResult
     * @param  array<string, mixed>  $context
     */
    public static function logDecision(array $dlpResult, array $context = []): self
    {
        return self::create([
            'user_id' => $context['user_id'] ?? auth()->id(),
            'session_id' => $context['session_id'] ?? session()->getId(),
            'classification' => $dlpResult['classification'] ?? self::CLASSIFICATION_PUBLIC,
            'routing_decision' => $dlpResult['routing_decision'] ?? self::ROUTE_CLOUD_ALLOWED,
            'risk_score' => $dlpResult['risk_score'] ?? 0,
            'detected_patterns' => $dlpResult['detected_patterns'] ?? null,
            'content_length' => $context['content_length'] ?? 0,
            'content_hash' => $context['content_hash'] ?? null,
            'target_provider' => $context['target_provider'] ?? self::PROVIDER_BEDROCK,
            'model_id' => $context['model_id'] ?? null,
            'operation_type' => $context['operation_type'] ?? null,
            'source_component' => $context['source_component'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'was_bypassed' => $context['was_bypassed'] ?? false,
            'bypassed_by' => $context['bypassed_by'] ?? null,
            'bypass_reason' => $context['bypass_reason'] ?? null,
            'processed_at' => now(),
        ]);
    }

    /**
     * Scope: Sensitive content only
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeSensitive(Builder $query): Builder
    {
        return $query->where('classification', self::CLASSIFICATION_SENSITIVE);
    }

    /**
     * Scope: Blocked requests only
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeBlocked(Builder $query): Builder
    {
        return $query->where('routing_decision', self::ROUTE_BLOCKED);
    }

    /**
     * Scope: Allowed requests (cloud or local)
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeAllowed(Builder $query): Builder
    {
        return $query->whereIn('routing_decision', [self::ROUTE_CLOUD_ALLOWED, self::ROUTE_LOCAL_ONLY]);
    }

    /**
     * Scope: Bypassed requests only
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeBypassed(Builder $query): Builder
    {
        return $query->where('was_bypassed', true);
    }

    /**
     * Scope: High risk (score >= threshold)
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeHighRisk(Builder $query, int $threshold = 50): Builder
    {
        return $query->where('risk_score', '>=', $threshold);
    }

    /**
     * Scope: Within date range
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeDateRange(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('processed_at', [$from, $to]);
    }

    /**
     * Get summary statistics for a date range
     *
     * @return array<string, mixed>
     */
    public static function getSummaryStats(string $from, string $to): array
    {
        $query = self::query()->whereBetween('processed_at', [$from, $to]);

        return [
            'total_requests' => (clone $query)->count(),
            'sensitive_count' => (clone $query)->sensitive()->count(),
            'blocked_count' => (clone $query)->blocked()->count(),
            'bypassed_count' => (clone $query)->bypassed()->count(),
            'cloud_allowed' => (clone $query)->where('routing_decision', self::ROUTE_CLOUD_ALLOWED)->count(),
            'local_only' => (clone $query)->where('routing_decision', self::ROUTE_LOCAL_ONLY)->count(),
            'avg_risk_score' => (clone $query)->avg('risk_score') ?? 0,
            'by_classification' => (clone $query)
                ->selectRaw('classification, COUNT(*) as count')
                ->groupBy('classification')
                ->pluck('count', 'classification')
                ->toArray(),
            'by_provider' => (clone $query)
                ->selectRaw('target_provider, COUNT(*) as count')
                ->groupBy('target_provider')
                ->pluck('count', 'target_provider')
                ->toArray(),
        ];
    }
}
