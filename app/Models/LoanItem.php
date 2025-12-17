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
 * @property string $equipment_type Type of equipment requested
 * @property string|null $notes Additional notes for equipment request
 * @property string|null $brand_model
 * @property string|null $serial_number
 * @property string|null $other_accessories
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Asset|null $asset
 * @property-read \App\Models\LoanApplication $loanApplication
 * @method static \Database\Factories\LoanItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereAccessoriesIssued($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereAccessoriesReturned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereBrandModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereConditionAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereConditionBefore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereDamageReport($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereEquipmentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereLoanApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereOtherAccessories($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereTotalValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereUnitValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LoanItem extends Model
{
    /** @use HasFactory<\Database\Factories\LoanItemFactory> */
    use HasFactory;

    protected $fillable = [
        'loan_application_id',
        'asset_id',
        // Bahagian 3: Equipment request fields
        'equipment_type',
        'quantity',
        'notes',
        // Bahagian 8: BPM staff fields (after asset assignment)
        'brand_model',
        'serial_number',
        'other_accessories',
        // Existing fields
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
    /** @return BelongsTo<LoanApplication, LoanItem> */
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class);
    }

    /** @return BelongsTo<Asset, LoanItem> */
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

    /** @return array<int, mixed> */
    public function getMissingAccessories(): array
    {
        if ($this->accessories_issued === null || $this->accessories_returned === null) {
            return [];
        }

        return array_diff($this->accessories_issued, $this->accessories_returned);
    }
}
