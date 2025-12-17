<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $model_id
 * @property string $metric_type
 * @property numeric $metric_value
 * @property \Illuminate\Support\Carbon $measurement_time
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\ModelPerformanceMetricFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelPerformanceMetric newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelPerformanceMetric newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelPerformanceMetric query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelPerformanceMetric whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelPerformanceMetric whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelPerformanceMetric whereMeasurementTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelPerformanceMetric whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelPerformanceMetric whereMetricType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelPerformanceMetric whereMetricValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelPerformanceMetric whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelPerformanceMetric whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ModelPerformanceMetric extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'model_id',
        'metric_type',
        'metric_value',
        'measurement_time',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metric_value' => 'decimal:6',
            'measurement_time' => 'datetime',
            'metadata' => 'array',
        ];
    }
}
