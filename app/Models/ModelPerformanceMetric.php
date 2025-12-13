<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
