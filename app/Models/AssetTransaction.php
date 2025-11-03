<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class AssetTransaction extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'asset_id',
        'loan_application_id',
        'type',
        'user_id',
        'processed_by',
        'condition_before',
        'condition_after',
        'accessories',
        'notes',
        'damage_description',
        'location_from',
        'location_to',
        'transaction_date',
    ];

    protected $casts = [
        'accessories' => 'array',
        'transaction_date' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
