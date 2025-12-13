<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebSearchLog extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'request_id',
        'search_query',
        'provider',
        'results_count',
        'sources_used',
        'cost',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'request_id' => 'string',
            'results_count' => 'integer',
            'sources_used' => 'array',
            'cost' => 'decimal:6',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }
}
