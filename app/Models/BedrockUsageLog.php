<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BedrockUsageLog extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'request_id',
        'model_id',
        'input_tokens',
        'output_tokens',
        'cost_estimate',
        'response_time_ms',
        'success',
        'error_message',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'request_id' => 'string',
            'input_tokens' => 'integer',
            'output_tokens' => 'integer',
            'cost_estimate' => 'decimal:6',
            'response_time_ms' => 'integer',
            'success' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }
}
