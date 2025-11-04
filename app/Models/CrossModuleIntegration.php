<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * CrossModuleIntegration Model
 *
 * Manages integration records between helpdesk tickets and asset loan applications.
 * Tracks cross-module events and maintains referential integrity.
 *
 * @see D03 Software Requirements Specification - Requirement 2.2, 2.3
 * @see D04 Software Design Document - Cross-Module Integration
 * @see D09 Database Documentation - cross_module_integrations table
 */
class CrossModuleIntegration extends Model implements Auditable
{
    use HasAuditTrail;
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cross_module_integrations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'helpdesk_ticket_id',
        'loan_application_id',
        'integration_type',
        'trigger_event',
        'integration_data',
        'processed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'integration_data' => 'array',
        'processed_at' => 'datetime',
    ];

    // Integration Type Constants

    /**
     * Asset damage report integration type
     */
    const TYPE_ASSET_DAMAGE_REPORT = 'asset_damage_report';

    /**
     * Maintenance request integration type
     */
    const TYPE_MAINTENANCE_REQUEST = 'maintenance_request';

    /**
     * Asset-ticket linking integration type
     */
    const TYPE_ASSET_TICKET_LINK = 'asset_ticket_link';

    // Trigger Event Constants

    /**
     * Asset returned with damage trigger event
     */
    const EVENT_ASSET_RETURNED_DAMAGED = 'asset_returned_damaged';

    /**
     * Ticket asset selected trigger event
     */
    const EVENT_TICKET_ASSET_SELECTED = 'ticket_asset_selected';

    /**
     * Maintenance scheduled trigger event
     */
    const EVENT_MAINTENANCE_SCHEDULED = 'maintenance_scheduled';

    // Relationships

    /**
     * Get the helpdesk ticket associated with this integration
     */
    public function helpdeskTicket(): BelongsTo
    {
        return $this->belongsTo(HelpdeskTicket::class, 'helpdesk_ticket_id');
    }

    /**
     * Get the asset loan application associated with this integration
     */
    public function assetLoan(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

    // Helper Methods

    /**
     * Check if integration has been processed
     */
    public function isProcessed(): bool
    {
        return ! is_null($this->processed_at);
    }

    /**
     * Mark integration as processed
     */
    public function markAsProcessed(): bool
    {
        $this->processed_at = now();

        return $this->save();
    }

    /**
     * Get integration type label
     */
    public function getIntegrationTypeLabel(): string
    {
        return match ($this->integration_type) {
            self::TYPE_ASSET_DAMAGE_REPORT => __('integration.type.asset_damage_report'),
            self::TYPE_MAINTENANCE_REQUEST => __('integration.type.maintenance_request'),
            self::TYPE_ASSET_TICKET_LINK => __('integration.type.asset_ticket_link'),
            default => __('integration.type.unknown'),
        };
    }

    /**
     * Get trigger event label
     */
    public function getTriggerEventLabel(): string
    {
        return match ($this->trigger_event) {
            self::EVENT_ASSET_RETURNED_DAMAGED => __('integration.event.asset_returned_damaged'),
            self::EVENT_TICKET_ASSET_SELECTED => __('integration.event.ticket_asset_selected'),
            self::EVENT_MAINTENANCE_SCHEDULED => __('integration.event.maintenance_scheduled'),
            default => __('integration.event.unknown'),
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
     * Scope to filter by integration type
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('integration_type', $type);
    }

    /**
     * Scope to filter by trigger event
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTriggeredBy($query, string $event)
    {
        return $query->where('trigger_event', $event);
    }

    /**
     * Scope to filter processed integrations
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProcessed($query)
    {
        return $query->whereNotNull('processed_at');
    }

    /**
     * Scope to filter unprocessed integrations
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnprocessed($query)
    {
        return $query->whereNull('processed_at');
    }
}
