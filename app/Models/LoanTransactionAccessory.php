<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * LoanTransactionAccessory Model - v3.5.0 True Hybrid Architecture
 *
 * Tracks accessories included with asset loans during check-out and check-in.
 * Supports discrepancy detection for missing or damaged accessories.
 *
 * @see D03 Software Requirements Specification - Requirement 26.6
 * @see D04 Software Design Document - Accessory Tracking Service
 * @see D09 Database Documentation - loan_transaction_accessories table
 *
 * @property int $id
 * @property int $loan_transaction_id
 * @property string $accessory_type (POWER_ADAPTER, BAG, MOUSE, USB_CABLE, HDMI_VGA_CABLE, REMOTE, OTHERS)
 * @property string|null $accessory_name (for OTHERS type)
 * @property bool $present_at_checkout
 * @property bool|null $present_at_checkin
 * @property string|null $condition_notes
 */
class LoanTransactionAccessory extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\LoanTransactionAccessoryFactory> */
    use HasFactory;

    // TODO: Add LogsActivity trait when spatie/laravel-activitylog is installed
    // use Spatie\Activitylog\Traits\LogsActivity;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'loan_transaction_id',
        'accessory_type',
        'accessory_name',
        'present_at_checkout',
        'present_at_checkin',
        'condition_notes',
    ];

    protected function casts(): array
    {
        return [
            'present_at_checkout' => 'boolean',
            'present_at_checkin' => 'boolean',
        ];
    }

    /** @var array<int, string> */
    protected $auditInclude = [
        'loan_transaction_id',
        'accessory_type',
        'present_at_checkout',
        'present_at_checkin',
    ];

    /**
     * Spatie Activity Log configuration
     */
    protected static $logAttributes = [
        'loan_transaction_id',
        'accessory_type',
        'present_at_checkout',
        'present_at_checkin',
    ];

    protected static $logName = 'loan_transaction_accessory';

    protected static $logOnlyDirty = true;

    // Relationships

    /**
     * Get the loan transaction this accessory belongs to
     *
     * @return BelongsTo<LoanTransaction, LoanTransactionAccessory>
     */
    public function loanTransaction(): BelongsTo
    {
        return $this->belongsTo(LoanTransaction::class);
    }

    // Helper Methods

    /**
     * Check if accessory has discrepancy (missing at check-in)
     */
    public function hasDiscrepancy(): bool
    {
        return $this->present_at_checkout && ! $this->present_at_checkin;
    }

    /**
     * Check if accessory is custom (OTHERS type)
     */
    public function isCustomAccessory(): bool
    {
        return $this->accessory_type === 'OTHERS';
    }

    /**
     * Get accessory display name
     */
    public function getDisplayName(): string
    {
        if ($this->isCustomAccessory()) {
            return $this->accessory_name ?? __('loan.accessory.others');
        }

        return __("loan.accessory.{$this->accessory_type}");
    }

    /**
     * Get standard accessory types
     *
     * @return array<string>
     */
    public static function getStandardAccessoryTypes(): array
    {
        return [
            'POWER_ADAPTER',
            'BAG',
            'MOUSE',
            'USB_CABLE',
            'HDMI_VGA_CABLE',
            'REMOTE',
            'OTHERS',
        ];
    }
}
