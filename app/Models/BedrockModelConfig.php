<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property string $model_id
 * @property string $model_name
 * @property string $provider
 * @property array<array-key, mixed>|null $task_types
 * @property numeric|null $cost_per_token
 * @property int|null $max_tokens
 * @property bool $enabled
 * @property array<array-key, mixed>|null $configuration
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $creator
 * @method static \Database\Factories\BedrockModelConfigFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig whereConfiguration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig whereCostPerToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig whereMaxTokens($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig whereModelName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig whereTaskTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @mixin \Eloquent
 */
class BedrockModelConfig extends Model implements AuditableContract
{
    use Auditable;
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'model_id',
        'model_name',
        'provider',
        'task_types',
        'cost_per_token',
        'max_tokens',
        'enabled',
        'configuration',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'task_types' => 'array',
            'configuration' => 'array',
            'enabled' => 'boolean',
            'cost_per_token' => 'decimal:8',
            'max_tokens' => 'integer',
        ];
    }

    /**
     * Spatie Activity Log configuration
     *
     * @see D09 §4.7 - Activity Log Requirements
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'model_id',
                'model_name',
                'provider',
                'enabled',
                'cost_per_token',
                'max_tokens',
            ])
            ->logOnlyDirty()
            ->useLogName('bedrock_model_config')
            ->setDescriptionForEvent(fn (string $eventName) => "Bedrock model config {$eventName}");
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
