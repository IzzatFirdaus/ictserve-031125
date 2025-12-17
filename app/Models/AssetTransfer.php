<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $asset_id
 * @property int|null $from_user_id
 * @property int $to_user_id
 * @property string|null $from_location
 * @property string|null $to_location
 * @property \Illuminate\Support\Carbon $transfer_date
 * @property int $initiated_by
 * @property int|null $approved_by
 * @property string $status
 * @property string|null $notes
 * @property string|null $cancellation_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $approver
 * @property-read \App\Models\Asset $asset
 * @property-read \App\Models\User|null $fromUser
 * @property-read \App\Models\User $initiator
 * @property-read \App\Models\User $toUser
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransfer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransfer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransfer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransfer whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransfer whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransfer whereCancellationReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransfer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransfer whereFromLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransfer whereFromUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransfer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransfer whereInitiatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransfer whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransfer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransfer whereToLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransfer whereToUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransfer whereTransferDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransfer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AssetTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'from_user_id',
        'to_user_id',
        'from_location',
        'to_location',
        'transfer_date',
        'initiated_by',
        'approved_by',
        'status',
        'notes',
        'cancellation_reason',
    ];

    protected $casts = [
        'transfer_date' => 'date',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
