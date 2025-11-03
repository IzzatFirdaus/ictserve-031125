<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AssetCondition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Loan Item Model
 *
 * Junction table linking loan applications to specific assets with condition tracking.
 *
 * @see D03-FR-003.2 Asset issuance tracking
 * @see D03-FR-003.3 Asset return processing
 * @see D04 §2.2 Model relationships
 *
 * @property int $id
 * @property int $loan_application_id
 * @property int $asset_id
 * @property int $quantity
 * @property float $unit_value
 * @property float $total_value
 * @property AssetCondition|null $condition_before
 * @property AssetCondition|null $condition_after
 * @property array|null $accessories_issued
 * @property array|null $accessories_returned
 * @property string|null $damage_report
 */
class LoanItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_application_id',
        'asset_id',
        'quantity',
        'unit_value',
        'total_value',
        'condition_before',
        'condition_after',
        'accessories_issued',
        'accessories_returned',
        'damage_report',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_value' => 'decimal:2',
        'total_value' => 'decimal:2',
        'condition_before' => AssetCondition::class,
        'condition_after' => AssetCondition::class,
        'accessories_issued' => 'array',
        'accessories_returned' => 'array',
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

    // Helper methods
    public function hasDamage(): bool
    {
        return $this->condition_after !== null
            && $this->condition_after !== $this->condition_before
            && in_array($this->condition_after, [AssetCondition::DAMAGED, AssetCondition::POOR]);
    }

    public function getMissingAccessories(): array
    {
        if ($this->accessories_issued === null || $this->accessories_returned === null) {
            return [];
        }

        return array_diff($this->accessories_issued, $this->accessories_returned);
    }
}
