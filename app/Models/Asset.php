<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Enhanced Asset Model with Cross-Module Integration
 * 
 * Comprehensive asset tracking with maintenance integration and cross-module
 * connectivity with the helpdesk system.
 *
 * @see D03-FR-003.1 Asset inventory management
 * @see D03-FR-016.2 Cross-module integration
 * @see D03-FR-018.1 Asset lifecycle management
 * @see D04 §2.2 Model relationships
 * @property int $id
 * @property string $asset_tag
 * @property string $name
 * @property string $brand
 * @property string $model
 * @property string|null $serial_number
 * @property int $category_id
 * @property array|null $specifications
 * @property \Carbon\Carbon $purchase_date
 * @property float $purchase_value
 * @property float $current_value
 * @property AssetStatus $status
 * @property string $location
 * @property AssetCondition $condition
 * @property array<string, mixed>|null $accessories
 * @property \Carbon\Carbon|null $warranty_expiry
 * @property \Carbon\Carbon|null $last_maintenance_date
 * @property \Carbon\Carbon|null $next_maintenance_date
 * @property int $maintenance_tickets_count
 * @property array<string, mixed>|null $loan_history_summary
 * @property array<string, mixed>|null $availability_calendar
 * @property array<string, mixed>|null $utilization_metrics
 * @property array<string, mixed>|null $specifications
 * @property numeric|null $purchase_price
 * @property int $useful_life_years
 * @property string|null $last_depreciation_calculation
 * @property numeric $accumulated_depreciation
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\AssetCategory $category
 * @property-read \App\Models\LoanApplication|null $currentLoan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskTicket> $helpdeskTickets
 * @property-read int|null $helpdesk_tickets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanItem> $loanItems
 * @property-read int|null $loan_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransaction> $loanTransactions
 * @property-read int|null $loan_transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskTicket> $maintenanceRecords
 * @property-read int|null $maintenance_records_count
 * @method static Builder<static>|Asset available()
 * @method static Builder<static>|Asset availableForLoan(string $startDate, string $endDate)
 * @method static \Database\Factories\AssetFactory factory($count = null, $state = [])
 * @method static Builder<static>|Asset newModelQuery()
 * @method static Builder<static>|Asset newQuery()
 * @method static Builder<static>|Asset onlyTrashed()
 * @method static Builder<static>|Asset query()
 * @method static Builder<static>|Asset requiringMaintenance()
 * @method static Builder<static>|Asset whereAccessories($value)
 * @method static Builder<static>|Asset whereAccumulatedDepreciation($value)
 * @method static Builder<static>|Asset whereAssetTag($value)
 * @method static Builder<static>|Asset whereAvailabilityCalendar($value)
 * @method static Builder<static>|Asset whereBrand($value)
 * @method static Builder<static>|Asset whereCategoryId($value)
 * @method static Builder<static>|Asset whereCondition($value)
 * @method static Builder<static>|Asset whereCreatedAt($value)
 * @method static Builder<static>|Asset whereCurrentValue($value)
 * @method static Builder<static>|Asset whereDeletedAt($value)
 * @method static Builder<static>|Asset whereId($value)
 * @method static Builder<static>|Asset whereLastDepreciationCalculation($value)
 * @method static Builder<static>|Asset whereLastMaintenanceDate($value)
 * @method static Builder<static>|Asset whereLoanHistorySummary($value)
 * @method static Builder<static>|Asset whereLocation($value)
 * @method static Builder<static>|Asset whereMaintenanceTicketsCount($value)
 * @method static Builder<static>|Asset whereModel($value)
 * @method static Builder<static>|Asset whereName($value)
 * @method static Builder<static>|Asset whereNextMaintenanceDate($value)
 * @method static Builder<static>|Asset wherePurchaseDate($value)
 * @method static Builder<static>|Asset wherePurchasePrice($value)
 * @method static Builder<static>|Asset wherePurchaseValue($value)
 * @method static Builder<static>|Asset whereSerialNumber($value)
 * @method static Builder<static>|Asset whereSpecifications($value)
 * @method static Builder<static>|Asset whereStatus($value)
 * @method static Builder<static>|Asset whereUpdatedAt($value)
 * @method static Builder<static>|Asset whereUsefulLifeYears($value)
 * @method static Builder<static>|Asset whereUtilizationMetrics($value)
 * @method static Builder<static>|Asset whereWarrantyExpiry($value)
 * @method static Builder<static>|Asset withHelpdeskHistory()
 * @method static Builder<static>|Asset withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Asset withoutTrashed()
 * @mixin \Eloquent
 */
class Asset extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\AssetFactory> */
    use HasFactory;

    use LogsActivity;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    /**
     * Spatie Activity Log configuration
     *
     * @see D09 §4.7 - Activity Log Requirements
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'asset_tag',
                'name',
                'status',
                'condition',
                'location',
                'current_value',
            ])
            ->logOnlyDirty()
            ->useLogName('asset')
            ->setDescriptionForEvent(fn (string $eventName) => "Asset {$eventName}");
    }

    protected $fillable = [
        'asset_tag',
        'name',
        'brand',
        'model',
        'serial_number',
        'category_id',
        // Asset specifications and details
        'specifications',
        'purchase_date',
        'purchase_value',
        'current_value',
        'status',
        'location',
        'condition',
        'accessories',
        'warranty_expiry',
        // Maintenance tracking
        'last_maintenance_date',
        'next_maintenance_date',
        // Cross-module integration metrics
        'maintenance_tickets_count',
        'loan_history_summary',
        'availability_calendar',
        'utilization_metrics',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'purchase_value' => 'decimal:2',
        'current_value' => 'decimal:2',
        'status' => AssetStatus::class,
        'condition' => AssetCondition::class,
        'accessories' => 'array',
        'specifications' => 'array',
        'availability_calendar' => 'array',
        'utilization_metrics' => 'array',
        'loan_history_summary' => 'array',
        'maintenance_tickets_count' => 'integer',
    ];

    // Cross-Module Relationships
    /** @return BelongsTo<AssetCategory, Asset> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class);
    }

    /** @return HasMany<LoanItem, Asset> */
    public function loanItems(): HasMany
    {
        return $this->hasMany(LoanItem::class);
    }

    /**
     * Get the current active loan for this asset through its most recent loan item.
     * Returns the latest loan application that is approved or on_loan status.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough<LoanApplication, LoanItem, Asset>
     */
    public function currentLoan(): \Illuminate\Database\Eloquent\Relations\HasOneThrough
    {
        return $this->hasOneThrough(
            LoanApplication::class,
            LoanItem::class,
            'asset_id',
            'id',
            'id',
            'loan_application_id'
        )->whereIn('loan_applications.status', ['approved', 'on_loan'])
            ->latest('loan_applications.created_at');
    }

    /** @return HasMany<LoanTransaction, Asset> */
    public function loanTransactions(): HasMany
    {
        return $this->hasMany(LoanTransaction::class);
    }

    /** @return HasMany<HelpdeskTicket, Asset> */
    public function helpdeskTickets(): HasMany
    {
        return $this->hasMany(HelpdeskTicket::class, 'asset_id');
    }

    /** @return HasMany<HelpdeskTicket, Asset> */
    public function maintenanceRecords(): HasMany
    {
        return $this->helpdeskTickets()->where('category', 'maintenance');
    }

    /** @param Builder<Asset> $query */
    public function scopeAvailableForLoan(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->where('status', AssetStatus::AVAILABLE)
            ->where('condition', '!=', AssetCondition::DAMAGED);
    }

    /**
     * Simple scope for listing available assets in guest form
     *
     * @param  Builder<Asset>  $query
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', AssetStatus::AVAILABLE)
            ->where('condition', '!=', AssetCondition::DAMAGED);
    }

    /** @param Builder<Asset> $query */
    public function scopeRequiringMaintenance(Builder $query): Builder
    {
        return $query->where('status', AssetStatus::MAINTENANCE)
            ->orWhere('condition', AssetCondition::DAMAGED)
            ->orWhere('condition', AssetCondition::POOR);
    }

    /** @param Builder<Asset> $query */
    public function scopeWithHelpdeskHistory(Builder $query): Builder
    {
        return $query->with([
            'helpdeskTickets' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            },
        ]);
    }

    // Helper methods
    public function isAvailable(): bool
    {
        return $this->status === AssetStatus::AVAILABLE
            && $this->condition !== AssetCondition::DAMAGED;
    }

    public function requiresMaintenance(): bool
    {
        return $this->status === AssetStatus::MAINTENANCE
            || $this->condition === AssetCondition::DAMAGED
            || $this->condition === AssetCondition::POOR;
    }

    public function isUnderWarranty(): bool
    {
        return $this->warranty_expiry !== null && $this->warranty_expiry > now();
    }
}
