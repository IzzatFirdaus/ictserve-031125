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
    use HasFactory, SoftDeletes, Auditable;

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
}
