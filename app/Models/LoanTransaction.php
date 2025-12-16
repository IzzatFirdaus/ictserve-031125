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
 * @property string|null $transaction_at Explicit timestamp of the transaction event
 * @property int|null $admin_id
 * @property bool $damage_reported Flag indicating if damage was reported during transaction
 * @property array<array-key, mixed>|null $damage_photos JSON array of damage photo file paths
 * @property string $created_at Record creation timestamp
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\User|null $admin
 * @property-read \App\Models\Asset $asset
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\LoanApplication $loanApplication
 * @property-read \App\Models\User $processedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransactionAccessory> $transactionAccessories
 * @property-read int|null $transaction_accessories_count
 * @method static \Database\Factories\LoanTransactionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereAccessories($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereConditionAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereConditionBefore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereDamagePhotos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereDamageReport($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereDamageReported($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereLoanApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereProcessedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereTransactionAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereTransactionType($value)
 * @mixin \Eloquent
 */
class LoanTransaction extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\LoanTransactionFactory> */
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    use \Spatie\Activitylog\Traits\LogsActivity;

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
     *
     * @see D09 §4.7 - Activity Log Requirements
     */
    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logOnly([
                'loan_application_id',
                'asset_id',
                'transaction_type',
                'damage_reported',
            ])
            ->logOnlyDirty()
            ->useLogName('loan')
            ->setDescriptionForEvent(fn (string $eventName) => "Loan transaction {$eventName}");
    }

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
            && \in_array($this->condition_after, [AssetCondition::DAMAGED, AssetCondition::POOR], true);
    }
}
