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
 *
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
 * @property array|null $accessories
 * @property \Carbon\Carbon|null $warranty_expiry
 * @property \Carbon\Carbon|null $last_maintenance_date
 * @property \Carbon\Carbon|null $next_maintenance_date
 * @property int $maintenance_tickets_count
 * @property array|null $loan_history_summary
 * @property array|null $availability_calendar
 * @property array|null $utilization_metrics
 */
class Asset extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

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
    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class);
    }

    public function loanItems(): HasMany
    {
        return $this->hasMany(LoanItem::class);
    }

    public function loanTransactions(): HasMany
    {
        return $this->hasMany(LoanTransaction::class);
    }

    public function helpdeskTickets(): HasMany
    {
        return $this->hasMany(HelpdeskTicket::class, 'asset_id');
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->helpdeskTickets()->where('category', 'maintenance');
    }

    // ICTServe Integration Scopes
    public function scopeAvailableForLoan(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->where('status', AssetStatus::AVAILABLE)
            ->where('condition', '!=', AssetCondition::DAMAGED);
    }

    public function scopeRequiringMaintenance(Builder $query): Builder
    {
        return $query->where('status', AssetStatus::MAINTENANCE)
            ->orWhere('condition', AssetCondition::DAMAGED)
            ->orWhere('condition', AssetCondition::POOR);
    }

    public function scopeWithHelpdeskHistory(Builder $query): Builder
    {
        return $query->with(['helpdeskTickets' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }]);
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
