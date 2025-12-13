<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class BedrockModelConfig extends Model implements AuditableContract
{
    use Auditable;
    use HasFactory;
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
