<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AssetCondition;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

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
class LoanTransaction extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\LoanTransactionFactory> */
    use HasFactory;

    // TODO: Add LogsActivity trait when spatie/laravel-activitylog is installed
    // use Spatie\Activitylog\Traits\LogsActivity;
    use \OwenIt\Auditing\Auditable;

    public $timestamps = false; // Using created_at only

    protected $fillable = [
        'loan_application_id',
        'asset_id',
        'transaction_type',
        'performed_by_admin_id', // v3.5.0 - Admin who performed the transaction
        'performed_at', // v3.5.0 - When transaction was performed
        'condition_notes', // v3.5.0 - Asset condition notes
        'damage_reported', // v3.5.0 - Damage flag
        'damage_photos', // v3.5.0 - Photo evidence of damage (JSON)
        // Legacy fields (for backward compatibility)
        'processed_by',
        'processed_at',
        'condition_before',
        'condition_after',
        'accessories',
        'damage_report',
        'notes',
    ];

    /** @var array<int, string> */
    protected $auditInclude = [
        'loan_application_id',
        'asset_id',
        'transaction_type',
        'performed_by_admin_id',
        'damage_reported',
    ];

    /**
     * Spatie Activity Log configuration
     */
    protected static $logAttributes = [
        'loan_application_id',
        'asset_id',
        'transaction_type',
        'damage_reported',
    ];

    protected static $logName = 'loan_transaction';

    protected static $logOnlyDirty = true;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'performed_at' => 'datetime',
            'transaction_type' => TransactionType::class,
            'damage_reported' => 'boolean',
            'damage_photos' => 'array',
            // Legacy casts
            'processed_at' => 'datetime',
            'condition_before' => AssetCondition::class,
            'condition_after' => AssetCondition::class,
            'accessories' => 'array',
        ];
    }

    // Relationships
    /** @return BelongsTo<LoanApplication, LoanTransaction> */
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class);
    }

    /** @return BelongsTo<Asset, LoanTransaction> */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /** @return BelongsTo<User, LoanTransaction> */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * v3.5.0 - Admin who performed the transaction
     *
     * @return BelongsTo<User, LoanTransaction>
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by_admin_id');
    }

    /**
     * v3.5.0 - Accessories tracked for this transaction
     *
     * @return HasMany<LoanTransactionAccessory, LoanTransaction>
     */
    public function transactionAccessories(): HasMany
    {
        return $this->hasMany(LoanTransactionAccessory::class);
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
