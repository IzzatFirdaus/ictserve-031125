<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/**
 * Cross Module Integration Model
 *
 * @property int $id
 * @property int $helpdesk_ticket_id
 * @property int $loan_application_id
 * @property string $integration_type
 * @property string $trigger_event
 * @property array $integration_data
 * @property \Illuminate\Support\Carbon|null $processed_at
 * @property int|null $processed_by
 */
class CrossModuleIntegration extends Model implements AuditableContract
{
    use Auditable, HasFactory, SoftDeletes;

    // Integration type constants
    public const TYPE_ASSET_DAMAGE_REPORT = 'asset_damage_report';

    public const TYPE_ASSET_TICKET_LINK = 'asset_ticket_link';

    public const TYPE_MAINTENANCE_REQUEST = 'maintenance_request';

    // Trigger event constants (must match migration enum values)
    public const EVENT_ASSET_RETURNED_DAMAGED = 'asset_returned_damaged';

    public const EVENT_TICKET_ASSET_SELECTED = 'ticket_asset_selected';

    public const EVENT_MAINTENANCE_SCHEDULED = 'maintenance_scheduled';

    protected $fillable = [
        'helpdesk_ticket_id',
        'loan_application_id',
        'integration_type',
        'trigger_event',
        'integration_data',
        'processed_at',
        'processed_by',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'integration_data' => 'array',
            'processed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the helpdesk ticket associated with this integration.
     */
    public function helpdeskTicket(): BelongsTo
    {
        return $this->belongsTo(HelpdeskTicket::class);
    }

    /**
     * Get the loan application associated with this integration.
     */
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class);
    }

    /**
     * Alias for loanApplication relationship (for backward compatibility)
     */
    public function assetLoan(): BelongsTo
    {
        return $this->loanApplication();
    }

    /**
     * Get the user who processed this integration.
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Check if integration has been processed
     */
    public function isProcessed(): bool
    {
        return $this->processed_at !== null;
    }

    /**
     * Mark integration as processed
     */
    public function markAsProcessed(?int $userId = null): bool
    {
        return $this->update([
            'processed_at' => now(),
            'processed_by' => $userId ?? auth()->id(),
        ]);
    }

    /**
     * Get human-readable label for integration type
     */
    public function getIntegrationTypeLabel(): string
    {
        return match ($this->integration_type) {
            self::TYPE_ASSET_DAMAGE_REPORT => __('cross_module.type.asset_damage_report'),
            self::TYPE_MAINTENANCE_REQUEST => __('cross_module.type.maintenance_request'),
            self::TYPE_ASSET_TICKET_LINK => __('cross_module.type.asset_ticket_link'),
            default => $this->integration_type,
        };
    }

    /**
     * Get human-readable label for trigger event
     */
    public function getTriggerEventLabel(): string
    {
        return match ($this->trigger_event) {
            self::EVENT_ASSET_RETURNED_DAMAGED => __('cross_module.event.asset_returned_damaged'),
            self::EVENT_TICKET_ASSET_SELECTED => __('cross_module.event.ticket_asset_selected'),
            self::EVENT_MAINTENANCE_SCHEDULED => __('cross_module.event.maintenance_scheduled'),
            default => $this->trigger_event,
        };
    }

    /**
     * Get all valid integration types
     */
    public static function getIntegrationTypes(): array
    {
        return [
            self::TYPE_ASSET_DAMAGE_REPORT,
            self::TYPE_MAINTENANCE_REQUEST,
            self::TYPE_ASSET_TICKET_LINK,
        ];
    }

    /**
     * Get all valid trigger events
     */
    public static function getTriggerEvents(): array
    {
        return [
            self::EVENT_ASSET_RETURNED_DAMAGED,
            self::EVENT_TICKET_ASSET_SELECTED,
            self::EVENT_MAINTENANCE_SCHEDULED,
        ];
    }

    /**
     * Scope: Filter by integration type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('integration_type', $type);
    }

    /**
     * Scope: Filter by trigger event
     */
    public function scopeTriggeredBy($query, string $event)
    {
        return $query->where('trigger_event', $event);
    }

    /**
     * Scope: Filter processed integrations
     */
    public function scopeProcessed($query)
    {
        return $query->whereNotNull('processed_at');
    }

    /**
     * Scope: Filter unprocessed integrations
     */
    public function scopeUnprocessed($query)
    {
        return $query->whereNull('processed_at');
    }
}
