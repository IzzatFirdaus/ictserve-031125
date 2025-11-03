<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AssetCondition;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Loan Transaction Model
 *
 * Complete audit trail for all asset loan transactions.
 *
 * @see D03-FR-010.2 Comprehensive audit logging
 * @see D03-FR-018.3 Asset lifecycle tracking
 * @see D04 §2.2 Model relationships
 *
 * @property int $id
 * @property int $loan_application_id
 * @property int $asset_id
 * @property TransactionType $transaction_type
 * @property int $processed_by
 * @property \Carbon\Carbon $processed_at
 * @property AssetCondition|null $condition_before
 * @property AssetCondition|null $condition_after
 * @property array|null $accessories
 * @property string|null $damage_report
 * @property string|null $notes
 */
class LoanTransaction extends Model
{
    use HasFactory;

    public $timestamps = false; // Using created_at only

    protected $fillable = [
        'loan_application_id',
        'asset_id',
        'transaction_type',
        'processed_by',
        'processed_at',
        'condition_before',
        'condition_after',
        'accessories',
        'damage_report',
        'notes',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'transaction_type' => TransactionType::class,
        'condition_before' => AssetCondition::class,
        'condition_after' => AssetCondition::class,
        'accessories' => 'array',
    ];

    // Relationships
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Helper methods
    public function isIssueTransaction(): bool
    {
        return $this->transaction_type === TransactionType::ISSUE;
    }

    public function isReturnTransaction(): bool
    {
        return $this->transaction_type === TransactionType::RETURN;
    }

    public function hasConditionChange(): bool
    {
        return $this->condition_before !== null
            && $this->condition_after !== null
            && $this->condition_before !== $this->condition_after;
    }

    public function hasDamage(): bool
    {
        return $this->condition_after !== null
            && in_array($this->condition_after, [AssetCondition::DAMAGED, AssetCondition::POOR]);
    }
}
