<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $request_id
 * @property string $model_id
 * @property int $input_tokens
 * @property int $output_tokens
 * @property numeric|null $cost_estimate
 * @property int|null $response_time_ms
 * @property bool $success
 * @property string|null $error_message
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\BedrockUsageLogFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockUsageLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockUsageLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockUsageLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockUsageLog whereCostEstimate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockUsageLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockUsageLog whereErrorMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockUsageLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockUsageLog whereInputTokens($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockUsageLog whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockUsageLog whereOutputTokens($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockUsageLog whereRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockUsageLog whereResponseTimeMs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockUsageLog whereSuccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockUsageLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockUsageLog whereUserId($value)
 * @mixin \Eloquent
 */
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
