<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $asset_id
 * @property string $maintenance_type
 * @property \Illuminate\Support\Carbon $scheduled_date
 * @property \Illuminate\Support\Carbon|null $completed_date
 * @property numeric|null $cost
 * @property string|null $notes
 * @property string|null $performed_by
 * @property int|null $performed_by_user_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Asset $asset
 * @property-read \App\Models\User|null $performedByUser
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMaintenance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMaintenance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMaintenance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMaintenance whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMaintenance whereCompletedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMaintenance whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMaintenance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMaintenance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMaintenance whereMaintenanceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMaintenance whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMaintenance wherePerformedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMaintenance wherePerformedByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMaintenance whereScheduledDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMaintenance whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMaintenance whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AssetMaintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'maintenance_type',
        'scheduled_date',
        'completed_date',
        'cost',
        'notes',
        'performed_by',
        'performed_by_user_id',
        'status',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completed_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function performedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by_user_id');
    }
}
