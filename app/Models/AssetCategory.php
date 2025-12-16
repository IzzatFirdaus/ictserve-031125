<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Asset Category Model
 * 
 * Defines categories for ICT equipment with custom specification templates.
 *
 * @see D03-FR-018.2 Asset categorization system
 * @see D04 §2.2 Model relationships
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $description
 * @property array<string, mixed>|null $specification_template
 * @property int $default_loan_duration_days
 * @property int $max_loan_duration_days
 * @property bool $requires_approval
 * @property bool $is_active
 * @property int $sort_order
 * @property string|null $name_en
 * @property array<array-key, mixed>|null $default_accessories
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asset> $assets
 * @property-read int|null $assets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Database\Factories\AssetCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereDefaultAccessories($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereDefaultLoanDurationDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereMaxLoanDurationDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereRequiresApproval($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereSpecificationTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @mixin \Eloquent
 */
class AssetCategory extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\AssetCategoryFactory> */
    use HasFactory;

    use LogsActivity;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'specification_template',
        'default_accessories',
        'default_loan_duration_days',
        'max_loan_duration_days',
        'requires_approval',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'specification_template' => 'array',
        'default_accessories' => 'array',
        'default_loan_duration_days' => 'integer',
        'max_loan_duration_days' => 'integer',
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
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
                'name',
                'code',
                'description',
                'requires_approval',
                'is_active',
                'sort_order',
            ])
            ->logOnlyDirty()
            ->useLogName('asset_category')
            ->setDescriptionForEvent(fn (string $eventName) => "Asset category {$eventName}");
    }

    // Relationships
    /** @return HasMany<Asset, AssetCategory> */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'category_id');
    }

    // Helper methods
    public function getAvailableAssetsCount(): int
    {
        return $this->assets()->where('status', 'available')->count();
    }

    public function getTotalAssetsCount(): int
    {
        return $this->assets()->count();
    }
}
