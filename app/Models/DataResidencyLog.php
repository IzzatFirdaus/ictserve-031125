<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PKS 4.2 Data Sovereignty Compliance - Data Residency Log Model
 *
 * Tracks all AI operations and their data residency for compliance
 * monitoring and audit purposes per PKS 4.2 requirements.
 *
 * @property int $id
 * @property int $user_id
 * @property string $service
 * @property string $operation
 * @property string $data_classification
 * @property string $processing_location
 * @property bool $is_local_processing
 * @property bool $is_compliant
 * @property string|null $model_id
 * @property int|null $input_tokens
 * @property int|null $output_tokens
 * @property int|null $response_time_ms
 * @property array<string, mixed>|null $metadata
 * @property string|null $compliance_notes
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @see D03-FR-025 (Data Sovereignty Requirements)
 * @see PKS 4.2 (Data Residency Requirements)
 *
 * @trace Requirements 26.2, 26.4
 */
class DataResidencyLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service',
        'operation',
        'data_classification',
        'processing_location',
        'is_local_processing',
        'is_compliant',
        'model_id',
        'input_tokens',
        'output_tokens',
        'response_time_ms',
        'metadata',
        'compliance_notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_local_processing' => 'boolean',
            'is_compliant' => 'boolean',
            'input_tokens' => 'integer',
            'output_tokens' => 'integer',
            'response_time_ms' => 'integer',
            'metadata' => 'array',
        ];
    }

    /**
     * @return BelongsTo<User, DataResidencyLog>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log a data residency event
     *
     * @param  array<string, mixed>  $data
     */
    public static function logOperation(array $data): self
    {
        return self::create([
            'user_id' => $data['user_id'] ?? auth()->id() ?? 0,
            'service' => $data['service'],
            'operation' => $data['operation'],
            'data_classification' => $data['data_classification'],
            'processing_location' => $data['processing_location'],
            'is_local_processing' => $data['is_local_processing'] ?? false,
            'is_compliant' => $data['is_compliant'] ?? true,
            'model_id' => $data['model_id'] ?? null,
            'input_tokens' => $data['input_tokens'] ?? null,
            'output_tokens' => $data['output_tokens'] ?? null,
            'response_time_ms' => $data['response_time_ms'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'compliance_notes' => $data['compliance_notes'] ?? null,
        ]);
    }

    /**
     * Scope for local processing only
     *
     * @param  \Illuminate\Database\Eloquent\Builder<DataResidencyLog>  $query
     * @return \Illuminate\Database\Eloquent\Builder<DataResidencyLog>
     */
    public function scopeLocalOnly($query)
    {
        return $query->where('is_local_processing', true);
    }

    /**
     * Scope for cloud processing
     *
     * @param  \Illuminate\Database\Eloquent\Builder<DataResidencyLog>  $query
     * @return \Illuminate\Database\Eloquent\Builder<DataResidencyLog>
     */
    public function scopeCloudProcessing($query)
    {
        return $query->where('is_local_processing', false);
    }

    /**
     * Scope for sensitive data
     *
     * @param  \Illuminate\Database\Eloquent\Builder<DataResidencyLog>  $query
     * @return \Illuminate\Database\Eloquent\Builder<DataResidencyLog>
     */
    public function scopeSensitive($query)
    {
        return $query->where('data_classification', 'SENSITIVE');
    }

    /**
     * Scope for compliant operations
     *
     * @param  \Illuminate\Database\Eloquent\Builder<DataResidencyLog>  $query
     * @return \Illuminate\Database\Eloquent\Builder<DataResidencyLog>
     */
    public function scopeCompliant($query)
    {
        return $query->where('is_compliant', true);
    }

    /**
     * Scope for non-compliant operations
     *
     * @param  \Illuminate\Database\Eloquent\Builder<DataResidencyLog>  $query
     * @return \Illuminate\Database\Eloquent\Builder<DataResidencyLog>
     */
    public function scopeNonCompliant($query)
    {
        return $query->where('is_compliant', false);
    }

    /**
     * Scope for specific service
     *
     * @param  \Illuminate\Database\Eloquent\Builder<DataResidencyLog>  $query
     * @return \Illuminate\Database\Eloquent\Builder<DataResidencyLog>
     */
    public function scopeForService($query, string $service)
    {
        return $query->where('service', $service);
    }

    /**
     * Get compliance statistics for dashboard
     *
     * @return array<string, mixed>
     */
    public static function getComplianceStats(): array
    {
        $today = now()->startOfDay();
        $thisWeek = now()->startOfWeek();
        $thisMonth = now()->startOfMonth();

        return [
            'total_operations' => self::count(),
            'today_operations' => self::where('created_at', '>=', $today)->count(),
            'week_operations' => self::where('created_at', '>=', $thisWeek)->count(),
            'month_operations' => self::where('created_at', '>=', $thisMonth)->count(),
            'local_processing_count' => self::localOnly()->count(),
            'cloud_processing_count' => self::cloudProcessing()->count(),
            'sensitive_local_count' => self::sensitive()->localOnly()->count(),
            'sensitive_cloud_count' => self::sensitive()->cloudProcessing()->count(),
            'compliant_count' => self::compliant()->count(),
            'non_compliant_count' => self::nonCompliant()->count(),
            'compliance_rate' => self::count() > 0
                ? round((self::compliant()->count() / self::count()) * 100, 2)
                : 100.0,
            'by_service' => self::query()
                ->selectRaw('service, COUNT(*) as count')
                ->groupBy('service')
                ->pluck('count', 'service')
                ->toArray(),
        ];
    }
}
