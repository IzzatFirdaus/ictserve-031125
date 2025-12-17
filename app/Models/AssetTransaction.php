<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property int $asset_id
 * @property int|null $loan_application_id
 * @property string $type
 * @property int|null $user_id
 * @property int|null $processed_by
 * @property string|null $condition_before
 * @property string|null $condition_after
 * @property array<array-key, mixed>|null $accessories
 * @property string|null $notes
 * @property string|null $damage_description
 * @property string|null $location_from
 * @property string|null $location_to
 * @property \Illuminate\Support\Carbon $transaction_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Asset $asset
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\LoanApplication|null $loanApplication
 * @property-read \App\Models\User|null $processedByUser
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereAccessories($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereConditionAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereConditionBefore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereDamageDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereLoanApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereLocationFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereLocationTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereProcessedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereUserId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @mixin \Eloquent
 */
class AssetTransaction extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\AssetTransactionFactory> */
    use HasFactory;

    use LogsActivity;
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

    /**
     * Spatie Activity Log configuration
     *
     * @see D09 §4.7 - Activity Log Requirements
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'asset_id',
                'type',
                'user_id',
                'condition_before',
                'condition_after',
                'transaction_date',
            ])
            ->logOnlyDirty()
            ->useLogName('asset_transaction')
            ->setDescriptionForEvent(fn (string $eventName) => "Asset transaction {$eventName}");
    }

    /** @return BelongsTo<Asset, AssetTransaction> */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /** @return BelongsTo<LoanApplication, AssetTransaction> */
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class);
    }

    /** @return BelongsTo<User, AssetTransaction> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<User, AssetTransaction> */
    public function processedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
